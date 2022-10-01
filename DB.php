<?php

//Database class
class DB{
	
	//Databse connection
	protected $dbconn = null;
	//Database results
	protected $queryresutls = null;

	//Databse connection method
	public function dbConnect(){

		// Connecting, selecting database
		$this->dbconn = pg_pconnect("host=ls-770ad92cb4f54f363e6d03ef5d4da826de889099.cdrc4ah2k9aq.us-west-2.rds.amazonaws.com dbname=books user=dbmasteruser password=N)hb`&}8^2$[eQ+IW3=6p=9+G;qDh:ib")
		or die('Could not connect: ' . pg_last_error());

	}

	//Database disconnect method
	public function dbDisconnect(){
		
		// Free resultset
		if($this->queryresutls){
			pg_free_result($this->queryresutls);
		}
		
		// Closing connection
		pg_close($this->dbconn);
	}

	//Find all from table method
	public function dbFindAll($table = null){
		
		//If table var is not null then query
		if($table){
			
			// Performing SQL query
			$query = 'SELECT * FROM '. $this->formatString($table);		
					
			//echo $query."</br>";
			
			//Query and store resutls
			$this->queryresutls = pg_query($query) or die('Query failed: ' . pg_last_error());
				
			return $this->queryresutls; 
			
		}else{
			return null;
		}
		
		
	}
	
	//Find on by id method
	public function findOneById($table = null, $id = null){
		
		//If table and id not null query
		if($table && $id){
			
			// Performing SQL query
			$query = 'SELECT * FROM '. $this->formatString($table) . ' WHERE id = '. $this->formatString($id);
		
			//echo $query."</br>";
			
			//Query and store resutls
			$this->queryresutls = pg_query($query) or die('Query failed: ' . pg_last_error());
				
			return $this->queryresutls;
			
		}else{
			return null;
		}	
	}	
		
	//Find on by any parameter
	public function findBy($table = null, $jointable = null, $queryparameters = null){
		
		//Local var for storing
		$searchstring = null;	
		$query = null;		
		
		//If table and queryparameters are not null
		if($table && $queryparameters){
			
			//Count number of query parameters
			$parametercount = count($queryparameters);
			
			//If null stop query
			if($parametercount == 0){
				return null;
			}
			
			//Parameter counter start
			$i = 1;
			
			//Create searchstring base on query parameters var
			foreach($queryparameters as $key => $value){
				
				//If parameter count is 1 or more (add AND)
				if($parametercount == 1){
					//Check type of value data, strings or integer					
					$searchstring .= $this->checkDataTypeSearch($key, $value);					
				}else if($i < $parametercount){
					//Check type of value data, strings or integer					
					$searchstring .= $this->checkDataTypeSearch($key, $value) . " AND ";
					$i++;
				}else{
					//Check type of value data, strings or integer
					$searchstring .= $this->checkDataTypeSearch($key, $value);
					$i++;
				}											
			}					
			
			//If join table is needed or else normal query
			//Verify jointable is not null
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
			
			//Query and store resutls
			$this->queryresutls = pg_query($query) or die('Query failed: ' . pg_last_error());
			
			//print_r($results);echo "DATA<br/>";
			
			//Return results if jointable is not null or else normal query results
			if($this->formatString($jointable)){
				
				//Return all fields from table
				return $this->queryresutls;
				
			}else{
				//Return only ID from query results			
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
	
	//Insert method
	public function insert($table = null, $queryparameters = null){
		
		//Local var for query
		$fields = null;
		$values = null;
		
		//If table and queryparameters are not null
		if($table && $queryparameters){
			
			//Count number of query parameters
			$parametercount = count($queryparameters);
			
			//If null stop query
			if($parametercount == 0){
				return null;
			}
			
			$i = 1;
			//echo $parametercount;
			
			//Create searchstring base on query parameters var
			foreach($queryparameters as $key => $value){
				
				//If parameter count is 1 or more
				if($parametercount == 1){
					
					//Add field to insert to
					$fields .= $this->formatString($key);
					
					//Check type of value data, strings or integer
					$values .= $this->checkDataTypeInsert($key, $value);
					
				}else if($i < $parametercount){
					
					//Add field to insert to
					$fields .= " ". $this->formatString($key) . ", ";
					
					//Check type of value data, strings or integer
					$values .= $this->checkDataTypeInsert($key, $value).",";
					
					$i++;
					
				}else{
					
					//Add field to insert to
					$fields .= " ". $this->formatString($key);
					
					//Check type of value data, strings or integer
					$values .= $this->checkDataTypeInsert($key, $value);

					$i++;
						
				}
			}
			
			// Performing SQL query
			$query = 'INSERT INTO '. $this->formatString($table) .' ('. $fields .') VALUES ('. $values .') RETURNING id';
			
			//echo $query."</br>";
			
			//Query and store resutls
			$this->queryresutls = pg_query($query) or die('Query failed: ' . pg_last_error());
			
			$id = 0;
			
			//Returl id of inserted query
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
	
	//Format string to avoid SQL injection
	private function formatString($string){
		
		return htmlspecialchars(trim($string));
	}
	
	//Method to verify type of data to insert query (string or integer)
	private function checkDataTypeInsert($key, $value){
		$val = null;
		
		if(gettype($value) == "integer"){
			$val = " ". $this->formatString($value)." ";
		}else{
			$val = " '". $this->formatString($value)."' ";
		}
		
		return $val;
	}	
	
	//Method to verify type of data to search query (string or integer)
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



