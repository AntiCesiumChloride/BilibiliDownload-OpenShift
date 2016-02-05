<?php
/**
 * @author https://ZE3kr.com
 * Usage: To get a download URL: $url = GetBilibiliUrl('http://www.bilibili.com/mobile/video/av1234567/index_2.html');
**/

define( 'APPKEY', '85eb6835b0a1034e', true );
define( 'APPSEC', '2ad42749773c441109bdc0191257a664', true );
define( 'APPKEY2', '95acd7f6cc3392f3', true );

function GetSign($params,$app_key=APPKEY,$app_sec=false) {
	$params['appkey'] = $app_key;
	$data = "";
	ksort($params);
	$data = http_build_query($params);
	if($app_sec) {
		return $data.'&sign='.md5($data.$app_sec);
	} else {
		return $data;
	}
}

function urlfetch($url) {
	$ip = $_SERVER['REMOTE_ADDR'];
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$req_headers = [
		'Accept-Encoding: gzip',
		'Client-IP: '.$ip,
	];
	$ch = curl_init() ;
	curl_setopt( $ch , CURLOPT_URL , $url ) ;
	curl_setopt( $ch , CURLOPT_REFERER, "http://www.bilibili.com/" );
	curl_setopt( $ch , CURLOPT_HTTPHEADER, $req_headers );
	curl_setopt( $ch , CURLOPT_USERAGENT, $ua); 
	curl_setopt( $ch , CURLOPT_TIMEOUT, 60 );
	curl_setopt( $ch , CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch , CURLOPT_ENCODING, 'gzip' );
	$http_data = curl_exec( $ch ) ;
	curl_close($ch);
	return $http_data;
}

function GetUrl($aid, $pid) {
	$url_get_media = 'http://interface.bilibili.com/playurl?';
	$cid = apc_fetch('cid-'$aid.$pid);
	if(!$cid){
		$cid_args = [
			'type' => 'json',
			'id' => $aid,
			'page' => $pid,
		];
		$resp_cid = urlfetch('http://api.bilibili.com/view?'.GetSign($cid_args,APPKEY,APPSEC));
		$resp_cid = json_decode($resp_cid,true);
		$cid = $resp_cid['cid'];
		apc_store( 'cid-'$aid.$pid, $cid );
	}
	$url = apc_fetch('url-'$cid);
	if($url){
		$resp_media['durl'][0]['url'] = $url;
	} else {
		$media_args = [
			'otype' => 'json',
			'cid' => $cid,
			'type' => 'flv',
			'quality' => 4,
		];
		$appkeyf = [APPKEY,APPKEY2];
		$resp_media = urlfetch($url_get_media.GetSign($media_args,$appkeyf[rand(0,1)]));
		$resp_media = json_decode($resp_media,true);
		apc_store( 'url-'$cid, $resp_media['durl'][0]['url'] );
	}
	if(isset($resp_media['durl'][0]['url'])) {
		return [
			'success' => true,
			'url' => $resp_media['durl'][0]['url'],
			'aid' => $aid,
			'pid' => $pid
		];
	} else {
		return [
			'success' => false,
			'code' => 1,
			'aid' => $aid,
			'pid' => $pid
		];
	}
}

function GetBilibiliUrl($url) {
	$explode = explode('/',substr(strstr($url,'http://www.bilibili.com/'),24));
	if( $explode[0] == 'video' ) {
		$aid = substr($explode[1],2);
		if($explode[3]) {
			$pid = substr($explode[3],6,-5);
		} else {
			$pid = 1;
		}

		return GetUrl($aid, $pid);
	} elseif( $explode[0] == 'mobile' ){
		$aid = substr($explode[2],2,-5);
		echo <<<HTML
<script>	
var params = function () {
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('#') + 1).split('&');
	for(var i = 0; i < hashes.length; i++) {
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
	return vars;
	}()

	var end = '';
	if( typeof( params.page )!="undefined" ) {
		var end = "index_" + params.page + ".html";
	}
window.location="/bilibilidownload.php/video/av" + {$aid} + "/" + end + "?type=mobile";
</script>
HTML;
	return [
		'success' => false,
		'code' => 3,
	];
	} else {
		return [
			'success' => false,
			'code' => 2,
		];
	}
}
if(isset($_GET['url'])){
	$url = $_GET['url'];
} else {
	$url = 'http://www.bilibili.com'.$_SERVER['REQUEST_URI'];
}
$return = GetBilibiliUrl($url);
if( $return['success'] ) {
	if( $_GET['type'] == 'mobile' ) {
		echo <<<HTML
<script>
window.location="{$return['url']}";
window.location="http://www.bilibili.com/mobile/video/av{$return['aid']}.html#page={$return['pid']}";
</script>
HTML;
	} else {
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '.$return['url']);
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
			<h2 class="note">Ver. 1.0 大更新！页面由：<a target="_blank" href="https://weibo.com/pa001024">pa001024</a>设计<br /><br />书签支持: 拖动<a title="拖动我到书签栏" href="javascript:void (function(a,d){d=document.createElement('script');d.src=a;document.body.appendChild(d)})('http://www.bilibili.download/bookmark.js')">❤下载</a>到书签栏 书签点击更方便, 快来使用吧~</h2>
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