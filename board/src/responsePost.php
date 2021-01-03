<?php 
$webRoot = $_SERVER['DOCUMENT_ROOT'];

include_once($webRoot."/board/src/function.php");
$dbh = getDbh();

// 文字化け防止
header("Content-type: text/plain; charset=UTF-8");

// タイムゾーンを東京に設定
date_default_timezone_set('Asia/Tokyo');

// スレッド作成者フラグ
$isMain = $_POST['isMain'];$pass="";$name="";

// スレッド作成者の場合
if($isMain == 1){
  // クローズかどうか
  $closeFlg = $_POST['close'];

  // パスワード代入
  $pass = $_POST['pass'];
  // XSS対応
  $pass = htmlspecialchars($pass, ENT_QUOTES, 'UTF-8');
  // ハッシュ化
  $pass = hash("sha256",$pass);

// スレッド作成者以外
}else{
  // 名前
  $name = $_POST['name'];
  $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
  // スレッド作成者以外はクローズするかどうかの決定権がないので0で固定。
  $closeFlg = "0";
}

// スレッドIDを代入
$thread_id = $_POST['thread_id'];
// メッセージIDを代入
$message = $_POST['message'];
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// スペースだけの投稿を防止
$blank = array('　',' ');
$checkName = str_replace($blank, "", $name);
$checkMessage = str_replace($blank, "", $message);
$checkPass = str_replace($blank, "", $pass);

// スレッド作成者以外かつ名前が空の場合
if($isMain != 1 && $checkName == ""){
  p('noName');
  return ;
}
// メッセージが空の場合
if($checkMessage==""){
  p('noMsg');
  return ;
}
// スレッド作成者かつパスワードが空の場合
if($isMain == 1 && $checkPass == ""){
  p('noPass');
  return ;
}


if($isMain == 1){
  // パスワードをチェック。
  $sql="SELECT RD.name,TD.pass
      FROM gs_thread_data TD,gs_res_data RD
        WHERE TD.thread_id = RD.thread_id
          AND TD.thread_id = :thread_id
          AND RD.userType = 1";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':thread_id', $thread_id, PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  // パスワードがスレッド作成時のものと一致しているかチェック
  if($pass != $result['pass']){
      p('passDif');
      return;
  }
  
  // 名前は固定なので、結果から取得する
  $name = $result['name'];
}

// メッセージIDを投稿時間から生成
$message_id = date(YmdHis);

try{
  // トランザクション開始
  $dbh->beginTransaction();

  // メッセージ用テーブル(gs_res_data)への登録
  $sql="INSERT INTO gs_res_data(thread_id,message_id,userType,name,message,disabledFlg,createTime,updateTime)VALUES(:thread_id,:message_id,:userType,:name,:message,'0',sysdate(), sysdate())";

  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':thread_id', $thread_id, PDO::PARAM_STR);
  $stmt->bindParam(':message_id', $message_id, PDO::PARAM_STR);
  $stmt->bindParam(':userType', $isMain, PDO::PARAM_STR);
  $stmt->bindParam(':name', $name, PDO::PARAM_STR);
  $stmt->bindParam(':message', $message, PDO::PARAM_STR);
  $stmt->execute();

  // スレッド用テーブル（gs_thread_data）の更新日を更新（一覧の表示順を更新）
  $sql="UPDATE gs_thread_data
        SET updateTime = :updateTime,
          closeFlg = :closeFlg
        WHERE thread_id = :thread_id";
  $stmt = $dbh->prepare($sql);
  $nowDate = date("Y-m-d H:i:s");
  $stmt->bindParam(':updateTime', $nowDate,  PDO::PARAM_STR);
  $stmt->bindParam(':closeFlg', $closeFlg, PDO::PARAM_STR);
  $stmt->bindParam(':thread_id', $thread_id, PDO::PARAM_STR);
  $stmt->execute();

  // コミット
  $dbh->commit();
} catch (Exception $e) {
  $dbh->rollBack();
  echo "失敗しました。" . $e->getMessage();
}

?>