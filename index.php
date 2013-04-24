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
		<a class="brand" href="#">じゅぴたー（α）</a>
		<ul class="nav">
		<li class="active"><a href="#">Home</a></li>
		<li><a href="#">How to use</a></li>
		</ul>
	</div>
	</div>
	</div>
	</div>
	<div class="hero-unit">
    <h1>じゅぴたー（α）</h1>
    <p>WEBページタグ情報取得ツールです。ただし、これはGithubテスト用なので正しく動作しません。注意して下さいね。</p>
  </div>
	<div class="row">
        <div class="span8"><!--h2>ファイル登録</h2-->
		<form action="upload.php" method="post" enctype="multipart/form-data" class="form-horizontal">
			<div class="control-group">
				<label class="control-label">ファイル</label>
				<div class="controls"><input type="file" name="upfile" size="30" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">UAモード</label>
				<div class="controls">
				<input name="radiobutton" type="radio" value="PC" checked />PC
				<input name="radiobutton" type="radio" value="mobile" />mobile
				</div>
				<div class="alert span4">
					<a class="close" data-dismiss="alert">×</a>
        			<strong>UAモードとは？</strong><br />
        			UAモードを「mobile」にすることで、ブラウザのUser-AgentをDoCoMo/2.0 P903iに偽装します。<br/>
					通常は「PC」のままでOKです。
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">通知先アドレス</label>
				<div class="controls"><input name="mail" type="text" class="span2" value="" />＠loftwork.com</div>
			</div>
			<div class="form-actions">
			<input type="submit" class="btn btn-primary btn-large" value="タグ取得開始" />
			</div>
		</form>
        </div>
		<div class="span4"><h3>使い方</h3>
			<p>このツールの詳しい使い方はこちらから。</p>
			<a class="btn btn-primary">How to use</a>
			<h3>フィードバック</h3>
			<p>動作に関するフィードバックはこちらまで。</p>
			<a class="btn btn-primary">Feedback</a>
		</div>
	</div>
    <div id="footer">
	
    </div>
</div>
 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="/jupiter/js/bootstrap.min.js"></script>
</body>
</html>