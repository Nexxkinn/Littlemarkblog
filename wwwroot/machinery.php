<?php 
/* The idea is that you use the api 
to fetch all files inside the /post/ 
folder and return it as a json */

// get files and parse to arrays. create new array otherwise.
$jsonFile = (file_exists('posts.json')) ? json_decode(file_get_contents('posts.json'),true) : array();
echo "file:<br>";

// debug
foreach($jsonFile as $pr){
    echo implode(" ",$pr);
    echo '<br>';
}

// list all files in the directory
$files = glob('./posts/*.md');
usort( $files, function( $a, $b ) { return filemtime($b) - filemtime($a); } );

$newFile = array();
foreach($files as $file){
    echo "yay ";
    $IsAdded = FALSE;
    foreach ($jsonFile as $json) {
        if(pathinfo($file,PATHINFO_BASENAME) == $json['filename']){
            
            echo pathinfo($file,PATHINFO_BASENAME);
            echo ' ';
            $newFile[] = $json;
            $IsAdded = TRUE;
        }
    }
    if(!$IsAdded){
        echo "IsAdded triggered";
        $name = str_replace("_"," ",pathinfo($file,PATHINFO_FILENAME));
        $newFile[] = array(
            "hash"=> hash("md5",$name,false),
            "name"=> $name,
            "filename"=>pathinfo($file,PATHINFO_BASENAME),
            "date"=>date("d/m", filectime($file)),
            "timestamp"=>filectime($file)
        );
    }
}

usort( $newFile, function( $a, $b ) { return $b['timestamp'] - $a['timestamp']; } );

//debug
echo "updated file:<br>";
foreach($newFile as $pr){
    echo implode(" ",$pr);
    echo '<br>';
}
file_put_contents("posts.json",json_encode($newFile));
exit();
?>