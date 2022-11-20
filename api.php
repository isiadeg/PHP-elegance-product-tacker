<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once('./daily_count.php');
require_once('./movement.class.php');
require_once('./laptop.class.php');
require_once('./sales.php');
require_once("./faulty.php");
require_once("./partner-dispute.php");


require_once('./call_functions.php');

//echo json_encode("how are you");

$request = file_get_contents('php://input');

$json = json_decode($request);

if($json){
$mode = htmlspecialchars($json->mode);
if($mode === "create" ){
    
 
    echo Laptop::create($json);
}
else if($mode === "create Dispute"){
    echo Dispute::create($json);
}
else if($mode === "createMovement"){
    $new_movement = new Movement();
    echo $new_movement->create($json);
}
else if($mode === "get_laptops"){
    echo Laptop::find_all();
}elseif ($mode === "createsales"){
    $newsales = new Sales();
   echo $newsales->create($json);
}elseif($mode === "create_dailycount"){
    $newcount = new DailyCount();
    echo $newcount->create($json);
}elseif($mode === "create_faulty"){
    $newfault = new Faulty();
    echo $newfault->create($json);
}
}

$anyget = $_GET;

if(count($_GET)){
    if(key_exists("mode", $_GET)){
        if(htmlentities($_GET['mode']) === "get_one_laptop"){
            
            echo Laptop::find_one($_GET['tag']);
        }else if(htmlentities($_GET['mode']) === "get_laptops"){
            echo Laptop::find_all();
        }else if(htmlentities($_GET['mode']) === "get_dispute"){
            echo Dispute::find_all();
        }else if(htmlentities($_GET['mode']) === "get_one_sales"){
            $sales = new Sales();
            echo $sales->find_one($_GET['tag']);
        }else if(htmlentities($_GET['mode']) === "get_all_sales"){
            $sales = new Sales();
            echo $sales->find_all();
        }elseif(htmlentities($_GET['mode']==="get_all_movement")){
            $sales = new Movement();
            echo $sales->find_all();
        }elseif(htmlentities($_GET['mode']==="get_one_movement")){
            $sales = new Movement();
            echo $sales->find_one($_GET['productTag']);
        }
    }
}