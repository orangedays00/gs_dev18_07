<?php

//XSS対応
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES);
}

// 文字出力
function p($str) {
    print $str;
}

// URLのドメイン以降を取得する
function getRequestURL() {
    return $_SERVER["REQUEST_URI"];
}

// データベースの情報を取得
function getDbh() {
    $dsn='mysql:dbname=gs_db;charset=utf8;host=localhost';
    $user='root';
    $pass='root';
        try{
        $dbh = new PDO($dsn,$user,$pass);
        $dbh->query('SET NAMES utf8');
    }catch(PDOException $e){
        p('Error:'.$e->getMessage());
        p('データベースへの接続に失敗しました。');
        die();
    }
    return $dbh;
}

// アクセスされたスレッドIDのスレッド情報を返す
function getThreadInfo(){
    // URLからスレッドIDを取得する
    $urlArray = explode('/', getRequestURL());
    $thread_id = $urlArray[3];

    // メッセージ取得
    $sql = 'SELECT
            TD.thread_id,
            TD.title,
            TD.closeFlg,
            TD.updateTime,
            count(*) count
        FROM
            gs_thread_data TD,
            gs_res_data RD
        WHERE
            TD.thread_id = RD.thread_id
        AND TD.thread_id = :thread_id
        AND TD.disabledFlg != 1
        AND RD.disabledFlg != 1';

$stmt = getDbh()->prepare($sql);
$stmt->bindParam(':thread_id', $thread_id, PDO::PARAM_STR);
$stmt->execute();
return $stmt->fetch(PDO::FETCH_ASSOC);
}

// URLからスレッドページか判断する

function isResponseForm(){
    return strstr(getRequestURL() ,"/thread/");
}


// 指定されたスレッドのレスポンス件数を取得する
function getResponseCount($thread_id){
    $sql = "SELECT count(*) FROM gs_res_data WHERE disabledFlg != 1 AND thread_id=:thread_id";
    $stmt = getDbh()->prepare($sql);
    $stmt->bindParam(':thread_id', $thread_id ,PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    return $count;
}

// 指定されたスレッドのレスポンス一覧を取得する
function getResponseList($thread_id){
    /* メッセージ取得 */
    $sql = "SELECT
        TD.thread_id,
        RD.serial_id,
        RD.message_id,
        RD.userType,
        TD.title,
        RD.name,
        RD.message,
        TD.updateTime
    FROM
        gs_thread_data TD,
        gs_res_data RD
    WHERE
        TD.thread_id=RD.thread_id AND
        RD.disabledFlg != 1 AND
        TD.thread_id = :thread_id
    ORDER BY
        TD.thread_id,RD.message_id";
$stmt = getDbh()->prepare($sql);
$stmt->bindParam(':thread_id', $thread_id, PDO::PARAM_STR);
$stmt->execute();
$responseList = $stmt->fetchAll();

    return $responseList;
}

// 指定されたレスポンス情報を出力する。
function outputResponse($r){
    $serial_id = $r["serial_id"];
    $res_id = $r["message_id"];
    $name = $r["name"];
    $message = $r["message"];
    $updateTime = $r["updateTime"];
    $userType = $r["userType"];

    p('
        <div class="response">
        <p class="name">'.$name.'</p>
        <hr>
        <p class="message">'.nl2br($message).'</p>
        <p style="text-align:right;">'.date('Y/m/d(D) H:i', strtotime($updateTime)).'</p>
        </div>
        <span style="clear:both";></span>
    ');
}

// すべてのスレッドを取得する
function getFullThread(){
    $sql = "SELECT thread_id ,title ,updateTime
        FROM gs_thread_data
        WHERE disabledFlg != 1
        ORDER BY updateTime DESC,thread_id";
    $stmt = getDbh()->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}

// スレッド情報の出力
function outThreadList($result,$sideFlg = NULL){
    $first = getThreadFirst($result["thread_id"]);
    p('
        <div class="thread">
        <a href="/board/thread/'.$result["thread_id"].'/" class="transmission">
    ');
    p('
        <h4>'.$result["title"].'</h4>
        <p>'.getTrimString($first["message"],'50').'</p>
        </a>
        </div>
    ');
}

// メッセージが一定以上の場合に、3点リーダーに変更
function getTrimString($string, $trimLength){
    $count = mb_strlen($string);
    $string = mb_substr($string ,0 ,$trimLength);
    if($count > $trimLength){ $string = $string.'...'; }
    return $string;
    }


// 指定されたスレッドの最新のレスポンス情報を取得する
function getThreadFirst($thread_id){
    $sql = "SELECT
        TD.thread_id,
        RD.serial_id,
        RD.message_id,
        RD.userType,
        TD.title,
        RD.name,
        RD.message,
        RD.userType,
        RD.createTime,
        TD.updateTime
    FROM
        gs_thread_data TD,
        gs_res_data RD
    WHERE
        TD.thread_id=RD.thread_id AND
        RD.disabledFlg != 1 AND
        TD.thread_id = :thread_id
    ORDER BY
        RD.message_id ASC LIMIT 1";

$stmt = getDbh()->prepare($sql);
$stmt->bindParam(':thread_id', $thread_id ,PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

return $result;
}


function getSearchList($search) {
    $sql = "SELECT TD.thread_id, TD.title, TD.updateTime
            FROM gs_thread_data TD,
                 gs_res_data RD
            WHERE TD.thread_id = RD.thread_id AND
                    TD.disabledFlg != 1 AND
                    RD.message LIKE '%" . $search . "%'
            GROUP BY TD.thread_id
            ORDER BY TD.updateTime DESC, TD.thread_id";
    $stmt = getDbh()->prepare($sql);
    $stmt->execute();
    $searchResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $searchResult;
}

?>