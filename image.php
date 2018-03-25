<?php
header("content-type: image/gif");
	$img = "./images/".$_GET['img'].".gif";
	readfile($img);
?>