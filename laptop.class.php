<?php
//require_once("./call_functions.php");
class Laptop{
    static protected $database;
    //public $testing = "testing";
    static public function set_database($connectobject){
        self::$database = $connectobject;
    }

 

    static public function execute_query($prepared){

        $result = $prepared->execute();
        if(isset($prepared->errorInfo()[2])){
        $error =  $prepared->errorInfo()[2];
        throw new EXCEPTION($error);
        }else{
            return $result;
        }

    }
    static public function find_one($id){
        $prepared = "select L.*, Ps.* from laptop L INNER JOIN product_status Ps where L.tag = :tag AND 
        L.tag = Ps.tag";

        $prepared = self::$database->prepare($prepared);
        $prepared->bindParam(':tag', $id);
        try{
         self::execute_query($prepared);
         $prepared->setFetchMode(PDO::FETCH_CLASS, 'Laptop');
        // var_dump($prepared);
         $lapi = $prepared->fetch();

        if($lapi){
            return json_encode($lapi);
        }else{return "No results found";}
         /* if(self::$database->rowCount !== 0){
       
         return $lapi;}
         else{
            return "No results Found";
         }

        }catch(EXCEPTION $e){
            return $e->getMessage();
        }*/

    }catch(EXCEPTION $e){
        return $e->getMessage();
    }
}

static public function find_all(){
    $prepared = "select p.*, ps.* from laptop p INNER JOIN product_status ps ON p.tag = ps.tag";

    $prepared = self::$database->prepare($prepared);

    try{
     self::execute_query($prepared);
    // $prepared->setFetchMode(PDO::FETCH_CLASS, 'Laptop');
    // var_dump($prepared);
     $lapi = $prepared->fetchAll(PDO::FETCH_ASSOC);

    if($lapi){
        return json_encode($lapi);
    }else{return "No results found";}
     /* if(self::$database->rowCount !== 0){
   
     return $lapi;}
     else{
        return "No results Found";
     }

    }catch(EXCEPTION $e){
        return $e->getMessage();
    }*/

}catch(EXCEPTION $e){
    return $e->getMessage();
}
}


static public function create($arr){
    $rr = $arr->collection;
   
    foreach($rr as $eachlaptop){
        
        
$sql = "INSERT INTO laptop(dateBrought,
tag,
serialNumber,
brand,
model,
intelCore,
intelGeneration,
storageType,
storageCapacity,	
rAMProcessor,
speed,
touchScreen,
keypadLight,
revolvable,
averageBattery,
batteryBackup,
description,
imageUrl,
price) VALUES (:dateBrought,
    :tag,
:serialNumber,
:brand,
:model,
:intelCore,
:intelGeneration,
:storageType,
:storageCapacity,	
:rAMProcessor,
:speed,
:touchScreen,
:keypadLight,
:revolvable,
:averageBattery,
:batteryBackup,
:description,
:imageUrl,
:price)";

$prepared = self::$database->prepare($sql);
$prepared->bindValue(':dateBrought', $eachlaptop->dateBrought ?? null);
$prepared->bindValue(':tag', $eachlaptop->tag ?? null);
$prepared->bindValue(':serialNumber', $eachlaptop->serialNumber ?? null);
$prepared->bindValue(':brand', $eachlaptop->brand ?? null);
$prepared->bindValue(':model', $eachlaptop->model ?? null);
$prepared->bindValue(':intelCore', $eachlaptop->intelCore ?? null);
$prepared->bindValue(':intelGeneration', $eachlaptop->intelGeneration ?? null);
$prepared->bindValue(':storageType', $eachlaptop->storageType ?? null);
$prepared->bindValue(':storageCapacity', $eachlaptop->storageCapacity ?? null);
$prepared->bindValue(':rAMProcessor', $eachlaptop->rAMProcessor ?? null);
$prepared->bindValue(':speed', $eachlaptop->speed ?? null);
$prepared->bindValue(':touchScreen', $eachlaptop->touchScreen ?? null);
$prepared->bindValue(':keypadLight', $eachlaptop->keypadLight ?? null);
$prepared->bindValue(':revolvable', $eachlaptop->revolvable ?? null);
$prepared->bindValue(':averageBattery', $eachlaptop->averageBattery ?? null);
$prepared->bindValue(':batteryBackup', $eachlaptop->batteryBackup ?? null);
$prepared->bindValue(':description', $eachlaptop->description ?? null);
$prepared->bindValue(':imageUrl', $eachlaptop->imageUrl ?? null);

$prepared->bindValue(':price', $eachlaptop->price ?? null);

$sql2="INSERT INTO product_status (date, tag, status) VALUES(?,?,?)";
$stmnt2 = self::$database->prepare($sql2);
$stmnt2->bindParam(1, $eachlaptop->dateBrought);
$stmnt2->bindParam(2, $eachlaptop->tag);
$stmnt2->bindValue(3, "On Shelf");


self::$database->beginTransaction();
try{
$result = self::execute_query($prepared);
if($prepared->errorInfo()[2]){
    self::$database->rollBack();
    throw new EXCEPTION($prepared->errorInfo()[2]);
}else{
    try{
        $result = self::execute_query($stmnt2);
        if($stmnt2->errorInfo()[2]){
            self::$database->rollBack();
            throw new EXCEPTION($stmnt2->errorInfo()[2]);
        }else{
            self::$database->commit();
        }


    }catch(EXCEPTION $e){
        
        throw new EXCEPTION($e);
    }
}}catch(EXECPTION $e){
    
    http_response_code(500);
    return json_encode($e->getMessage());
}


    }

    http_response_code(201);
    return json_encode(self::$database->lastInsertId());



}
}