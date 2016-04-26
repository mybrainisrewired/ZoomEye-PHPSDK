<?php 

include("ze.sdk.php");

$conn = zeye::getInstance();

if(zeye::getLastError() != NUll){
 	var_dump(zeye::getLastError());		
 	die();
}

$res = $conn->getResourcesInfo();

if($res){	
	echo "角色:".$res["plan"]." 主机查询剩余:".$res["resources"]["host-search"]." 网站查询剩余".$res["resources"]["web-search"]."<br />";	
}else{
	var_dump(zeye::getLastError());
}


//Search Filters  see  https://www.zoomeye.org/api/doc#search-filters

$res  = $conn->searchHost("api:21",array("app","os"),1);
	
if($res){
	echo($res["total"]."<br />");	
	
	foreach($res["matches"] as $item){		 
		echo $item["ip"]."<br />";
	}
}else{	
	var_dump(zeye::getLastError());
}


$res  = $conn->searchWeb("discuz");

if($res){ 
	echo($res["total"]."<br />");	
	foreach($res["matches"] as $item){		 
		echo ( $item["ip"][0]);
		echo "<br />";
	}	 
}else{	
	var_dump(zeye::getLastError());
}




?>
 	