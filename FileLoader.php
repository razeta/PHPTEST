<?php

include "DB.php";

class FileLoader{
	
	public static function dirloader(){	
		
		$path = 'data/';

		$files = array_diff(scandir($path), array('.', '..'));

		//var_dump($files);
		
		FileLoader::dirIterator($files, $path);

	}


	private static function dirIterator($dirarray, $path){
		
		$db = new DB();
		$db->dbConnect();
		$authorId = 0;
		$bookId = 0;
		
		foreach($dirarray as $folder){
			
			//echo $path.$folder."<br/>";
			
			if(is_dir($path.$folder)){
				
				$newPath = $path.$folder."/";
				$files = array_diff(scandir($newPath), array('.', '..'));
				
				/*echo "Folder=";
				echo $folder;
				echo "<br/>";*/

				FileLoader::dirIterator($files, $newPath);
				
			}else if(is_file($path.$folder)){
				
				echo "<h2>Load file=";
				echo $path.$folder;
				echo "<br/>";
				echo "<br/></h2>";
				
				if (file_exists($path.$folder)) {
					$dir = $path.$folder;
					$xml = simplexml_load_file($dir);
					
					//print_r($xml);echo "<br/>";	

					foreach($xml->children() as $book){
						
						//print_r($book);echo "<br/>";
						echo "Author = ".$book->author."</br>";					
						echo "Name = ". $book->name."</br>";
						echo "<br/>";

						$authorId = $db ->findBy("books.author", null, array("author" => $book->author));
						
						if(!$authorId){
							
							$authorId = $db->insert("books.author", array("author" => $book->author));											
						
							//echo "AuthorID=".$authorId."</br>";
							
						}else{
							//echo "Author Found!<br/>";
						}
							
						$bookId = $db ->findBy("books.book", null, array("author_id" => intval($authorId), "name" => $book->name));
						
						if(!$bookId){
							$bookId = $db->insert("books.book", array("name" => $book->name, 'author_id' => intval($authorId)));
							//echo "BookID=".$bookId."</br>";
						}else{
							//echo "Book Found!<br/>";
						}
						
					}
															
				} else {
					exit('Failed to open xml.');
				}
				
			}else{
				echo "Unknown file!";
				echo "<br/>";
			}	
		}			
	
		
		$db->dbDisconnect();
	}
	
}




