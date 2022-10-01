<?php

include "DB.php";

//File loader class
class FileLoader{
	
	//Try to load the folder and files
	public static function dirloader(){	
		
		//Folder path
		$path = 'data/';

		$files = array_diff(scandir($path), array('.', '..'));

		//var_dump($files);
		
		//If there are folders and files load them
		if(!empty($files)){
			FileLoader::dirIterator($files, $path);
		}

	}


	//Iterate folders and files
	private static function dirIterator($dirarray, $path){
		
		//Local var
		$db = new DB();
		$db->dbConnect();
		$authorId = 0;
		$bookId = 0;
		
		//Iterate folder and files
		foreach($dirarray as $folder){
			
			//echo $path.$folder."<br/>";
			
			//Check if is folder
			if(is_dir($path.$folder)){
				
				//Scan dir again
				$newPath = $path.$folder."/";
				$files = array_diff(scandir($newPath), array('.', '..'));
				
				/*echo "Folder=";
				echo $folder;
				echo "<br/>";*/

				//Load new dir and files
				FileLoader::dirIterator($files, $newPath);
			
			//If is a file
			}else if(is_file($path.$folder)){
				
				//Get file extension, only XML
				$extension = pathinfo($path.$folder, PATHINFO_EXTENSION);
				
				if($extension == "xml" || $extension == "XML"){
				
					echo "<h2>Load file=";
					echo $path.$folder;
					echo "<br/>";
					echo "<br/></h2>";
					
					//Check if file exist
					if (file_exists($path.$folder)) {
						
						$dir = $path.$folder;
						//Load XML
						$xml = simplexml_load_file($dir);
						
						//print_r($xml);echo "<br/>";	
						
						//Foreach record in file
						foreach($xml->children() as $book){
							
							//print_r($book);echo "<br/>";
							echo "Author = ".$book->author."</br>";					
							echo "Name = ". $book->name."</br>";
							//echo "<br/>";

							//Find if author is already in DB
							$authorId = $db ->findBy("books.author", null, array("author" => $book->author));
							
							//If not insert author
							if(!$authorId){
								
								$authorId = $db->insert("books.author", array("author" => $book->author));											
							
								//echo "AuthorID=".$authorId."</br>";
								
								echo "New Author!<br/>";
								
							}else{
								echo "Author Found!<br/>";
							}
								
							//Find if current book is no DB	
							$bookId = $db ->findBy("books.book", null, array("author_id" => intval($authorId), "name" => $book->name));
							
							//If not insert book
							if(!$bookId){
								$bookId = $db->insert("books.book", array("name" => $book->name, 'author_id' => intval($authorId)));
								//echo "BookID=".$bookId."</br>";
								
								echo "New Book!<br/><br/>";
							}else{
								echo "Book Found!<br/><br/>";
							}
							
						}
																
					} else {
						exit('Failed to open xml.');
					}
				}	
			}else{
				echo "Unknown file!";
				echo "<br/>";
			}	
		}			
	
		
		$db->dbDisconnect();
	}
	
}




