<?php

class DB{
	
	protected $dbconn = null;
	protected $queryresutls = null;

	public function dbConnect(){

		// Connecting, selecting database
		$this->dbconn = pg_pconnect("host=localhost dbname=books user=postgres password=root")
		or die('Could not connect: ' . pg_last_error());

	}

	public function dbDisconnect(){
		// Free resultset
		if($this->queryresutls){
			pg_free_result($this->queryresutls);
		}
		
		// Closing connection
		pg_close($this->dbconn);
	}

	public function dbFindAll($table = null){
		
		if($table){
			// Performing SQL query
			$query = 'SELECT * FROM '. $this->formatString($table);		
					
			echo $query."</br>";
			
			$this->queryresutls = pg_query($query) or die('Query failed: ' . pg_last_error());
				
			return $this->queryresutls; 
		}else{
			return null;
		}
		
		
	}
	
	public function findOneById($table = null, $id = null){
		
		if($table && $id){
			// Performing SQL query
			$query = 'SELECT * FROM '. $this->formatString($table) . ' WHERE id = '. $this->formatString($id);
		
			echo $query."</br>";
			
			$this->queryresutls = pg_query($query) or die('Query failed: ' . pg_last_error());
				
			return $this->queryresutls;
			
		}else{
			return null;
		}	
	}	
		
	public function findBy($table = null, $jointable = null, $queryparameters = null){
				
		$searchstring = null;	
		$query = null;		
		
		if($table && $queryparameters){
			
			$parametercount = count($queryparameters);
			
			if($parametercount == 0){
				return null;
			}
			
			$i = 1;
			
			foreach($queryparameters as $key => $value){
				
				if($parametercount == 1){					
					$searchstring .= $this->checkDataTypeSearch($key, $value);					
				}else if($i < $parametercount){					
					$searchstring .= $this->checkDataTypeSearch($key, $value) . " AND ";
					$i++;
				}else{
					$searchstring .= $this->checkDataTypeSearch($key, $value);
					$i++;
				}											
			}					
			
			if($this->formatString($jointable)){
				$query = 'SELECT
								*
							FROM
								'. $this->formatString($table) .' 
							JOIN
								'. $this->formatString($jointable) .' 
									ON '. $this->formatString($table) .'.id = ' .$this->formatString($jointable) .'.author_id 
							WHERE
								' . $searchstring;												
			}else{
				// Performing SQL query
				$query = 'SELECT * FROM '. $this->formatString($table) . ' WHERE ' . $searchstring ;
			}
					
			//echo "<br/>".$query."<br/>";
			
			$this->queryresutls = pg_query($query) or die('Query failed: ' . pg_last_error());
			
			//print_r($results);echo "DATA<br/>";
			
			if($this->formatString($jointable)){
				
				return $this->queryresutls;
				
			}else{
							
				$results = pg_fetch_assoc($this->queryresutls);
				
				if(isset($results['id'])){
					return $results['id'];
				}
				
			}			
			
			return false; 
						
		}else{
			return false;
		}		
		
	}
	
	public function insert($table = null, $queryparameters = null){
		$fields = null;
		$values = null;
		
		if($table && $queryparameters){
			
			$parametercount = count($queryparameters);
			
			if($parametercount == 0){
				return null;
			}
			
			$i = 1;
			//echo $parametercount;
			
			foreach($queryparameters as $key => $value){
				
				if($parametercount == 1){
					$fields .= $this->formatString($key);
					
					$values .= $this->checkDataTypeInsert($key, $value);
					
				}else if($i < $parametercount){
					
					$fields .= " ". $this->formatString($key) . ", ";
					
					$values .= $this->checkDataTypeInsert($key, $value).",";
					
					$i++;
					
				}else{
					$fields .= " ". $this->formatString($key);
					
					$values .= $this->checkDataTypeInsert($key, $value);

					$i++;
						
				}
			}
			
			// Performing SQL query
			$query = 'INSERT INTO '. $this->formatString($table) .' ('. $fields .') VALUES ('. $values .') RETURNING id';
			
			//echo $query."</br>";
			
			$this->queryresutls = pg_query($query) or die('Query failed: ' . pg_last_error());
			
			$id = 0;
			
			if($this->queryresutls){
				$idresponse = pg_fetch_assoc($this->queryresutls);
				//print_r($idresponse['id'])."</br>";
				
				if(isset($idresponse['id'])){
					return $idresponse['id'];
				}
			}
					
			return null; 
						
		}else{
			return null;
		}		
		
	}
	
	private function formatString($string){
		
		return htmlspecialchars(trim($string));
	}
	
	private function checkDataTypeInsert($key, $value){
		$val = null;
		
		if(gettype($value) == "integer"){
			$val = " ". $this->formatString($value)." ";
		}else{
			$val = " '". $this->formatString($value)."' ";
		}
		
		return $val;
	}	
	
	private function checkDataTypeSearch($key, $value){
		$val = null;
		
		//echo gettype($value)."<br/>";
		
		if(gettype($value) == "integer"){
			$val = $this->formatString($key) . " = " . $this->formatString($value);
		}else{
			$val = 'lower('. $this->formatString($key) . ") LIKE '%" . $this->formatString(strtolower($value)). "%'";
		}
		
		return $val;
	}
}



