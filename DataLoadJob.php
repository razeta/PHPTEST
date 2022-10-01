<?php


FileLoader::dirloader();

//Something to write to txt log
$log  = "Load File".' - '.date("F j, Y, g:i a").PHP_EOL.        
        "-------------------------".PHP_EOL;
//Save string to log, use FILE_APPEND to append.
file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);


?>