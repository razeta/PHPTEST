<?php 
include "DB.php";
			  
class AuthorController{
	
	public function getAuthorsData($table, $jointable, $search){
		
		$db = new DB();
		$db->dbConnect();
		$results = $db->findBy($table, $jointable, $search);	
		//print_r($results);
		
		$books = null;
		$data = array();
		
		if($results){
			while($book = pg_fetch_assoc($results)){
				  //print_r($book);
				  /*$books .= 
					  '<tr class="bookslide">
						<td>'. $book['id'] .'</td>
						<td>'. $book['author'] .'</td>
						<td>'. $book['name'] .'</td>
					  </tr>';*/
					  
					  $data[] = array('id' => $book['id'], "author" => $book['author'], "name" => $book['name']);
			}
			
			$db->dbDisconnect();
			
			//return $books;
			//header('Content-Type: application/json; charset=utf-8');
			echo json_encode($data);
		
		}else{
			
			$data = array();			
			$data[] = array('id' => 0, "author" => "EMPTY", "name" => "EMPTY");
				
			echo json_encode($data);
			  /*'<tr class="bookslide">
				<td>0</td>
				<td>Nothing</td>
				<td>to show.</td>
			  </tr>';*/
		}
		
	}
}


