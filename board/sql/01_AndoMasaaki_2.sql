-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost:3306
-- 生成日時: 2021 年 1 月 07 日 00:36
-- サーバのバージョン： 5.7.30
-- PHP のバージョン: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- データベース: `gs_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `gs_thread_data`
--

CREATE TABLE `gs_thread_data` (
  `thread_id` int(11) NOT NULL COMMENT 'スレッドID',
  `title` varchar(30) NOT NULL COMMENT 'スレッドタイトル',
  `pass` varchar(100) NOT NULL COMMENT 'パスワード',
  `closeFlg` varchar(1) NOT NULL COMMENT '閉じフラグ（0:開、1:閉）',
  `disabledFlg` varchar(1) NOT NULL COMMENT '無効フラグ（0:開、1:閉）	',
  `createTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日',
  `updateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新日'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `gs_thread_data`
--

INSERT INTO `gs_thread_data` (`thread_id`, `title`, `pass`, `closeFlg`, `disabledFlg`, `createTime`, `updateTime`) VALUES
(1, 'b', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '1', '0', '2021-01-01 22:26:40', '2021-01-04 01:33:54'),
(2, 'タイトルテスト', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '0', '0', '2021-01-02 16:11:24', '2021-01-02 16:11:24'),
(3, 'タイトルテスト2', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '0', '0', '2021-01-02 16:15:36', '2021-01-04 00:14:33'),
(4, 'タイトルテストタイトルテスト', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '0', '0', '2021-01-02 18:44:58', '2021-01-02 18:44:58'),
(5, 'タイトルテストタイトルテストタイトルテストタイトルテスト２', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '0', '0', '2021-01-02 19:04:25', '2021-01-02 19:06:13'),
(6, 'タイトル', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '1', '0', '2021-01-04 13:25:38', '2021-01-04 13:26:27'),
(7, 'タイトルテスト＆タイトルテスト', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '0', '0', '2021-01-07 01:08:20', '2021-01-07 01:08:20');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `gs_thread_data`
--
ALTER TABLE `gs_thread_data`
  ADD PRIMARY KEY (`thread_id`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `gs_thread_data`
--
ALTER TABLE `gs_thread_data`
  MODIFY `thread_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'スレッドID', AUTO_INCREMENT=8;
