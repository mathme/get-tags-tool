<?php
$path = "/var/www/html/jupiter/result/*.txt"; // 画像のパス
$rmTime = time() - 60*60*24*30; // 30日前の時間を求める
 
foreach (glob($path) as $filename) {
    // 30日より前のファイルなら
    if (filemtime($filename) < $rmTime) {
        // 削除
        @unlink($filename);
    }
}
?>