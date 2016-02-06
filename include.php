<?php
/**
 * @author https://ZE3kr.com
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

function GetUrl($aid, $pid=1, $type='mp4') {
	$url_get_media = 'http://interface.bilibili.com/playurl?';
	$cid = apc_fetch('cid-'.$aid.$pid);
	if(!$cid){
		$cid_args = [
			'type' => 'json',
			'id' => $aid,
			'page' => $pid,
		];
		$resp_cid = urlfetch('http://api.bilibili.com/view?'.GetSign($cid_args,APPKEY,APPSEC));
		$resp_cid = json_decode($resp_cid,true);
		$cid = $resp_cid['cid'];
		apc_store( 'cid-'.$aid.$pid, $cid );
	}
	$url = apc_fetch('url-'.$cid);
	if($url){
		$resp_media['durl'][0]['url'] = $url;
	} else {
		$media_args = [
			'otype' => 'json',
			'cid' => $cid,
			'type' => $type,
			'quality' => 4,
		];
		$appkeyf = [APPKEY,APPKEY2];
		$resp_media = urlfetch($url_get_media.GetSign($media_args,$appkeyf[rand(0,1)]));
		$resp_media = json_decode($resp_media,true);
		apc_store( 'url-'.$cid, $resp_media['durl'][0]['url'], 10800 );
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
		if( substr($explode[3],6,-5)>0 ) {
			$pid = substr($explode[3],6,-5);
		} else {
			$pid = 1;
		}

		return GetUrl($aid, $pid, 'flv');
	} elseif( $explode[0] == 'mobile' ){
		$aid = substr($explode[2],2,-5);
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
		<h2>已经开始下载，5 秒钟后自动返回之前的页面</h2>
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
		window.location="/video/av" + {$aid} + "/" + end + "?type=mobile";
		setTimeout(function(){
			history.go(-1);
		},5000)
		</script>
	</body>
</html>
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
?>