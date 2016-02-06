<?php
/**
 * @author https://ZE3kr.com
**/

$explode = explode('/',substr(strstr($_GET['url'],'http://www.bilibili.com/'),24));
if( $explode[0] == 'video' ) {
	$aid = substr($explode[1],2);
	if(substr($explode[3],6,-5)>0) {
		$pid = substr($explode[3],6,-5);
	} else {
		$pid = 1;
	}
	echo 'https://bilidown.tlo.xyz/link.php/'.$aid.'.mp4?page='.$pid;
} else {
	echo '不是有效的链接';
}
?>