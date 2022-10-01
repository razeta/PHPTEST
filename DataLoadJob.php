<?php
include "FileLoader.php";

//CRON job load method
FileLoader::dirloader();

//Log info when CRON job executed, create a log for each execution
$log  = "Load File".' - '.date("F j, Y, g:i a").PHP_EOL.        
        "-------------------------".PHP_EOL;
		
//Save string to log, use FILE_APPEND to append.
file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);


?>