<?php
include "AuthorController.php";

//Ajax call script
//If searchbox and table and jointable are present in GET
if(array_key_exists('searchbox', $_GET) && array_key_exists('table', $_GET) && array_key_exists('jointable', $_GET)){

	//Get response data and dencode searchbox
	$search = json_decode($_GET['searchbox'], true);
	$table = $_GET['table'];
	$jointable = $_GET['jointable'];
	
	//New author controller instance
	$authorcontroller = new AuthorController();

	//Get authors for table rows
	$authorcontroller->getAuthorsData($table, $jointable, $search);
	
}else{
	
	//Return empty JSON response
	$data = array();
	$data[] = array('id' => 0, "author" => "EMPTY", "name" => "EMPTY");	
	
	echo json_encode($data);
}