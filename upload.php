<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>WEBページタグ情報取得ツール「じゅぴたー（α）」</title>
	<link href="/jupiter/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
<div id="header">
<div class="navbar">
<div class="navbar-inner">
<div class="container">
<a class="brand" href="#">
  じゅぴたー（α）
</a>
<ul class="nav">
<li class="active"><a href="#">Home</a></li>
<li><a href="#">How to use</a></li>
</ul>
</div>
</div>
</div>
</div>
<div>
<p><?php

if (is_uploaded_file($_FILES["upfile"]["tmp_name"])) {
  if (move_uploaded_file($_FILES["upfile"]["tmp_name"], "upfiles/" . $_FILES["upfile"]["name"])) {
	chmod("upfiles/" . $_FILES["upfile"]["name"], 0644);
		// echo $_FILES["upfile"]["name"] . "をアップロードしました。";
	
	// 書き込み用CSVファイル名の設定と生成・初期化
	$up_file = $_FILES["upfile"]["name"];
	$csv_file = date('YmdHis').'_'.$_FILES["upfile"]["name"];
	$sPath ='/var/www/html/jupiter/result/'.$csv_file;
	
	//ファイルの存在確認
	if(file_exists($sPath)){
		echo '・指定ファイルが既に存在しております。';
		exit;
	}else{
		//echo '・ファイルの存在確認完了。<br/>';
	}

	//ファイルを作成
	if(touch($sPath)){
		//echo '・ファイル作成完了。<br/>';
	}else{
		echo '・ファイル作成失敗。<br/>';
		exit;
	}

	if(chmod($sPath,0644)){
		//echo '・ファイルパーミッション変更完了。<br/>';
	}else{
		echo '・ファイルパーミッション変更失敗。<br/>';
		exit;
	}

	//ファイルをオープン
	if($filepoint = fopen($sPath,"w")){
		// echo '・ファイルオープン完了。<br/>';
	}else{
		echo '・ファイルオープン失敗。<br/>';
		exit;
	}

	//ファイルのロック
	if(flock($filepoint, LOCK_EX)){
		// echo '・ファイルロック完了。<br/>';
	}else{
		echo '・ファイルロック失敗。<br/>';
		exit;
	}

	//ファイルのアンロック
	if(flock($filepoint, LOCK_UN)){
		// echo '・ファイルアンロック完了。<br/>';
	}else{
		echo '・ファイルアンロック失敗。<br/>';
		exit;
	}

	//ファイルを閉じる
	if(fclose($filepoint)){
   	// echo '・ファイルクローズ完了。<br/>';
	}else{
		echo '・ファイルクローズ失敗。<br/>';
		exit;
	}
	
	$mode = $_POST['radiobutton'];
	$mail = $_POST['mail'];
	$cmd = '/usr/bin/php -q get.php '. $csv_file .' '. $sPath .' '. $up_file .' '. $mode .' '. $mail .' > /dev/null &'; 
	exec($cmd); 

	echo '<h2>処理を開始しました。</h2>
	<p>処理が完了すると、'. htmlspecialchars($mail). '@loftwork.com 宛に通知メールが送信されます。</p>
	<div class="alert"><a class="close" data-dismiss="alert">×</a>
        	<strong>処理にかかる時間のめやす</strong><br />
        	コンテンツのあるサーバの応答スピードあるいは回線状況などにより、処理完了までにかかる時間は異なりますが、1URLあたり5秒前後が目安となります。<br />
			1000URLの場合、おおよそ1時間半程度を想定してください。</div>
			<a class="btn btn-primary" href="./index.php">Topに戻る</a>';
	
  } else {
	echo "ファイルをアップロードできません。";
  }
} else {
  echo "ファイルが選択されていません。";
}
?></p>
</div>
</div>
 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="/jupiter/js/bootstrap.min.js"></script>
</body>
</html>