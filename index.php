<?php
/**
 * @author https://ZE3kr.com
**/

require_once( __DIR__.'/include.php' );

if(isset($_GET['url'])){
	$url = $_GET['url'];
} else {
	$url = 'http://www.bilibili.com'.$_SERVER['REQUEST_URI'];
}
$return = GetBilibiliUrl($url);
if( $return['success'] ) {
	if( $_GET['type'] == 'mobile' ) {
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '.$return['url']);
	} else {
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '.$return['url']);
		echo <<<HTML
<script>
history.go(-1);
</script>
HTML;
	}
} elseif($return['code'] == 3) {
	header('HTTP/1.1 200 OK');
} elseif($return['code'] == 2) {
	header('HTTP/1.1 200 OK');
	echo <<<HTML
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<title>BilibiliDownload</title>
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<link rel="stylesheet" href="main.css">
	</head>
	<body>
		<div class="css3dskin bili22">
			<div class="css3dskin-profile">
				<div class="m"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="h"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="lh"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="rh"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="d"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="lf"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="rf"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
			</div>
		</div>
		<div class="css3dskin bili33">
			<div class="css3dskin-profile reverse">
				<div class="m"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="h"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="lh"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="rh"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="d"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="lf"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
				<div class="rf"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
			</div>
		</div>
		<div id="container">
			<form id="downform" name="downform" action="/">
				<input type="text" class="urlinput" name="url" id="domain_input" placeholder="http://www.bilibili.com/video/av12450/" autofocus autocomplete="off" onchange="short()">
			</div>
			<h2 class="note">书签支持: 拖动<a title="拖动我到书签栏" href="javascript:void (function(a,d){d=document.createElement('script');d.src=a;document.body.appendChild(d)})('http://www.bilibili.download/bookmark.js')">❤下载</a>到书签栏 书签点击更方便, 快来使用吧~</h2>
		</div>
		<footer id="footer">
			Copyleft 2016, <a target="_blank" href="http://www.superfashi.com">SuperFashi</a><br>
			Associated with, <a target="_blank" href="https://weibo.com/pa001024">pa001024</a>, 
			<a target="_blank" href="https://www.cnbeining.com/">cnBeining</a>,
			<a target="_blank" href="https://keybase.io/zyu">Zac Yu</a>,
			<a target="_blank" href="https://ze3kr.tlo.xyz/">ZE3kr</a><br>
			A subsite belongs to <a href="http://www.fuckbilibili.com">FuckBilibili</a>
		</footer>
	</body>
</html>
HTML;
} else {
	echo <<<HTML
<script>
alert("Parameter is missing or invalid");
history.go(-1);
</script>
HTML;
}
?>