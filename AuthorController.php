<?php 
include "DB.php";
			  
//Author controller and actions
class AuthorController{
	
	//Return authors data from DB
	public function getAuthorsData($table, $jointable, $search){
		
		//Local var
		$db = new DB();
		$db->dbConnect();				
		$books = null;
		$data = array();
		
		//Find authors
		$results = $db->findBy($table, $jointable, $search);	
		//print_r($results);

		//If results is not empty fetch data and store it in array for JSON treatment
		if($results){
			while($book = pg_fetch_assoc($results)){				
				$data[] = array('id' => $book['id'], "author" => $book['author'], "name" => $book['name']);
			}
			
			$db->dbDisconnect();
			
			
			//return response		
			echo json_encode($data);
		
		}else{
			//Return empty JSON response
			$data = array();			
			$data[] = array('id' => 0, "author" => "EMPTY", "name" => "EMPTY");
				
			echo json_encode($data);
		}
		
	}
}


