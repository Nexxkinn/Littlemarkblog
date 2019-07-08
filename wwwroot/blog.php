<?php 
$file = json_decode(file_get_contents('posts.json'));

switch($_SERVER['REQUEST_METHOD']){
    case "GET": {
        //generate post list.
        if(isset($_GET)&&empty($_GET)){
            $out = array();
            foreach($file as $data){
                $out[] = array(
                    "hash" => $data->hash,
                    "name" => $data->name,
                    "date" => $data->date
                );
            }
            header('Content-type:application/json;charset=utf-8');
            echo json_encode($out);
        }
        //generate post from url
        else if(isset($_GET['post'])){
            include('index.html');
        }
        else calltheghost();
        break;
    }
    case "POST":{
        // Error checking 1
        // Make sure that the content type of the POST request
        // has been set to application/json
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if(strcasecmp($contentType, 'application/json') != 0) break;

        $data = trim(file_get_contents("php://input"));
        $content = json_decode($data, true);
        generatepost($content["post"],$file);
        break;
    }
}

function calltheghost(){
    http_response_code(404);
    include('404.shtml'); 
    die();
}

function generatepost($hash,&$file){
    try{
        $key = "about";
        
        if($hash != "about"){
            //load hash only, for better performance
            $hashset = array_column($file,'hash');
            $key = array_search($hash,$hashset);
        }
        
        if(false !== $key){
            include('./lib/parsedown.php');
            $out = new Parsedown();
            //md -> html conversion
            $file = ($key !== "about") ? "./posts/{$file[$key]->filename}" : "./lib/about.md";
            $data = file_get_contents($file);
            echo $out->text($data);
        }
        else {
            // it is a mystery <=(o.o)=>
            calltheghost();
        }
    }
    catch(Exception $e){
        calltheghost();
    }
    
}
?>
