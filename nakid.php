<?php
/*-----------------------------------------------------------------------
//THIS FILE NEEDS TO BE INCLUDED AT THE TOP OF EVERY PAGE USING NAKID CMS
-----------------------------------------------------------------------*/
require dirname(__FILE__) . "/config.php";
/*
NAKID_GET
The nakid_get function is called from the user's website, looking something like this:

nakid_get("content",array('block'=>2));

The function passes the "tool" being used (content, gallery, catalog, form...) as well as parameters about the tool such as the id of the entry being pulled or optional settings given by the user

The function will use CURL or file_get_contents to call this page from the "connector" controller and output the page's contents
*/
function nakid($options){
	if(!is_array($options)){
		$options = array("keyword"=>$options);
	}
	//$options = array_map("urlencode", $options);
	$option_string = base64_encode( serialize($options));
	/*
	//Make option string
	$option_strings = array();
	foreach($options as $ok => $ov){
		$option_strings[] = $ok."-".$ov;
	}
	$option_string = implode("_",$option_strings);
	*/
	//
	$url = NAKID_URL."index.php/connector?options=".$option_string;
	//$url = NAKID_URL."index.php/connector/".$tool."/".$option_string;
	$content = "";
	//Get content
	if (function_exists('curl_init')) {
		//USE CURL FOR GETTING DATA
		$curl_handle=curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$url);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		$content = curl_exec($curl_handle);
		curl_close($curl_handle);
	} else {
		//USE file_get_contents FOR GETTING DATA
		$content = file_get_contents($url);
	}
	echo($content);
}
?>