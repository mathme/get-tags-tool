<?php

ini_set( "log_errors", "On" );
ini_set( "error_log", "/var/www/html/php.log" );

$csv_file = $argv[1];
$sPath = $argv[2];
$up_file = $argv[3];
$mode = $argv[4];
$mail = $argv[5];

	$fp = fopen("upfiles/" . $up_file, "r"); //読み込み専用でURLCSVを開く
	$i = 0;
	while ($handle = fgetcsv($fp)) { //＄handleがFALSEを返すまで以下の処理をループ
	
		//配列の定義と初期化
		$tags = array();
		
		if($mode == 'PC'){
			$opts = array('http' =>
				array(
					'timeout' => '10',
					'max_redirects' => '1'
					//'ignore_errors' => '1'
				)
			);
			stream_context_create($opts);
			$default_opts = array(
				'http' => array(
					'timeout' => '10',
					'max_redirects' => '1'
				)
			);
			stream_context_get_default($default_opts);
		} elseif($mode == 'mobile'){
			$opts = array('http' =>
				array(
					'timeout' => '10',
					'max_redirects' => '1',
					'user_agent' => 'User-Agent: DoCoMo/2.0 P903i(c100;TB;W24H12)'
					//'ignore_errors' => '1'
				)
			);
			stream_context_create($opts);
			$default_opts = array(
				'http' => array(
					'timeout' => '10',
					'max_redirects' => '1',
					'user_agent' => 'User-Agent: DoCoMo/2.0 P903i(c100;TB;W24H12)'
				)
			);
			stream_context_get_default($default_opts);
		}

		$header = get_headers($handle[0], 1);
		preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matche);
		$status_code = $matche[1];
		switch ($status_code) { //ステータスコードによって処理を分岐
			case '200':
				// 200の場合
				$context = stream_context_create($opts);
				$html = file_get_contents($handle[0], false, $context);
				$st = array('status' => "200 OK");
				$tags += $st;
				$html = mb_convert_encoding($html, "SJIS", "auto"); 
				if ( preg_match( "/<title>(.*?)<\/title>/i", $html, $matches) ) { 
					// return $matches[1];
					$title = array('title' => $matches[1]);
					$tags += $title;
				} else {
					$title = array('title' => "no title");
					$tags += $title;
				}
				// metaタグの抽出と代入
				if($mode == 'PC'){
					$meta = get_meta_tags( $handle[0] );
				} elseif($mode == 'mobile'){
					$meta = array();
					preg_match_all("|<meta[^>]+name=\"([^\"]*)\"[^>]" . "+content=\"([^\"]*)\"[^>]+>|i",$html, $out,PREG_PATTERN_ORDER);
					for ($i=0;$i < count($out[1]);$i++) {
						if (strtolower($out[1][$i]) == "keywords") $meta['keywords'] = $out[2][$i];
						if (strtolower($out[1][$i]) == "description") $meta['description'] = $out[2][$i];
					}
				}
				$kw = array('keywords' => mb_convert_encoding($meta['keywords'],"SJIS", "auto"));
				$desc = array('description' => mb_convert_encoding($meta['description'],"SJIS", "auto"));
				$tags += $kw;
				$tags += $desc;
		
				// 書き込み用CSVデータの初期化
				$csv_data = "";
			
				$csv_data .= $handle[0]. "\t";
				$csv_data .= $tags["status"]. "\t";
				$csv_data .= $tags["title"]. "\t";
				$csv_data .= $tags["keywords"]. "\t";
				$csv_data .= $tags["description"];
				$csv_data .= "\n";
				break;
			case '404':
				// 404の場合
				$st = array('status' => "404 Not Found");
				$tags += $st;
				$csv_data = "";
				$csv_data .= $handle[0]. "\t";
				$csv_data .= $tags["status"]. "\n";
				break;
			case '403':
				// 403の場合
				$st = array('status' => "403 Forbidden");
				$tags += $st;
				$csv_data = "";
				$csv_data .= $handle[0]. "\t";
				$csv_data .= $tags["status"]. "\n";
				break;
			case '400':
				// 400の場合
				$st = array('status' => "400 Bad Request");
				$tags += $st;
				$csv_data = "";
				$csv_data .= $handle[0]. "\t";
				$csv_data .= $tags["status"]. "\n";
				break;
			case '401':
				// 401の場合
				$st = array('status' => "401 Unauthorized");
				$tags += $st;
				$csv_data = "";
				$csv_data .= $handle[0]. "\t";
				$csv_data .= $tags["status"]. "\n";
				break;
			case '301':
				// 301の場合
				$st = array('status' => "301 Moved Permanently");
				$tags += $st;
				if(isset($header['Location'])){
					$loc = $header['Location'];
					if(is_array($loc)){ 
						$loc = end($loc);
					}
				}
				$locate = array('location' => $loc );
				$tags += $locate;
				$csv_data = "";			
				$csv_data .= $handle[0]. "\t";
				$csv_data .= $tags["status"]. "\t";
				$csv_data .= $tags["location"]. "\n";
				break;
			case '302':
				// 302の場合
				$st = array('status' => "302 Moved Temporarily");
				$tags += $st;
				if(isset($header['Location'])){
					$loc = $header['Location'];
					if(is_array($loc)){ 
						$loc = end($loc);
					}
				}
				$locate = array('location' => $loc );
				$tags += $locate;
				$csv_data = "";			
				$csv_data .= $handle[0]. "\t";
				$csv_data .= $tags["status"]. "\t";
				$csv_data .= $tags["location"]. "\n";
				break;
			default:
				// その他のエラーの場合
				$title = array('title' => "Other Error");
				$tags += $title;
				$csv_data = "";
				$csv_data .= $handle[0]. "\t";
				$csv_data .= $tags["title"]. "\n";
				break;
		}
		
		//ファイルへの書き込み処理とインクリメント
		$fop = fopen($sPath, 'ab');
		flock($fop, LOCK_EX);
		fwrite($fop, $csv_data);
		fclose($fop);
		$i++;
	}
		
	fclose($fp); //URLCSVを閉じる
	unlink("upfiles/" . $up_file);
	
	//$sfp = fopen("./status/status.txt", "ab"); //追記モードでステータス記述用ファイルを開く
	//$sts = '<a href="./result/'. $csv_file. '">'. $csv_file. '</a>'. "\n";
	//flock($sfp, LOCK_EX);
	//fwrite($sfp, $sts);
	//fclose($sfp); //ステータス記述用ファイルを閉じる
	
	//メール送信処理
	mb_language("Japanese");
	mb_internal_encoding("UTF-8");
	
	$domain = 'http://54.225.198.222/jupiter/';
	$file_link = $domain. 'result/'. $csv_file;
	$mailadd = $mail. '@loftwork.com';
	if (mb_send_mail($mailadd, "タグ抽出処理が終了しました：じゅぴたー（α版）", "タグ抽出処理が終了しました。\nファイルを以下のリンクから保存してください。\n\n$file_link\n\n※リンク先の文字エンコードはShift_JISです。URLをブラウザで直接開いた場合に日本語が文字化けしていることがありますが、バグではなく正しい挙動となります。\n※処理結果ファイルは30日間保存されます。処理完了から30日を過ぎたファイルは削除されますので注意してください。", "From: system@loftwork.com")) {
		echo "メールが送信されました。";
	} else {
		echo "メールの送信に失敗しました。";
	}
	
?>