<?php
include("../../../wp-load.php");

$deger = get_post_meta($_POST['id'],"begenbutonu");

if(!isset($_COOKIE["yazilike-".$_POST['id']])){
	setcookie("yazilike-".$_POST['id'], "begenildi", time() + (60*60*24));	
	
	if(empty($deger)){
	add_post_meta($_POST['id'],"begenbutonu",1);
	echo "Beğeniyi kaldır (1)";
	}else{	
		update_post_meta($_POST['id'],"begenbutonu",$deger[0]+1);
		echo "Beğeniyi kaldır (".($deger[0]+1).")";
	}	
}else{
		setcookie("yazilike-".$_POST['id'], "begenildi", time() - (60*60*24));
		update_post_meta($_POST['id'],"begenbutonu",$deger[0]-1);
		echo "Begen (".($deger[0]-1).")";	
		
}
?>