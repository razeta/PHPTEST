<?php
include "AuthorController.php";

if(array_key_exists('searchbox', $_GET) && array_key_exists('table', $_GET) && array_key_exists('jointable', $_GET)){

	$search = json_decode($_GET['searchbox'], true);
	$table = $_GET['table'];
	$jointable = $_GET['jointable'];
	
	$authorcontroller = new AuthorController();

	$authorcontroller->getAuthorsData($table, $jointable, $search);
	
}else{
	
	$data = array();
	$data[] = array('id' => 0, "author" => "EMPTY", "name" => "EMPTY");
	//$data[] = array('id' => 0, "author" => "EMPTY2", "name" => "EMPTY2");
	
	echo json_encode($data);
}