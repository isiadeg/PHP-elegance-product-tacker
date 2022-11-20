<?php
//require_once("./call_functions.php");
class DailyCount{
 static protected $database;
 static public function set_database($database){
    self::$database = $database;
 }   


 public function create($ar){
    foreach($ar->collections as $arr){
        //echo $arr->date;
    $sql="INSERT INTO daily_count (
    date,
    model,
    quantity
    ) VALUES(?,?,?)";

    $sql1="INSERT INTO touse_daily_count (
       model
    ) VALUES(?)";



    $stmnt=self::$database->prepare($sql);
    $stmnt1 = self::$database->prepare($sql1);
   
    self::$database->beginTransaction();
    $stmnt->execute([
   $arr->date,
    $arr->model,
    $arr->number]);
    if(isset($stmnt->errorInfo()[2])){
        self::$database->rollBack();
        //echo "cahi";
       // print_r($stmnt->errorInfo());
        http_response_code(500);
        $response = new stdClass();
        $response->message=$stmnt->errorInfo()[2];
        return json_encode($response);
        
    }else{
        $stmnt1->execute([
            $arr->model]);
        if(isset($stmnt1->errorInfo()[2])){
            self::$database->rollBack();
            http_response_code(500);
            $response = new stdClass();
            $response->message=$stmnt1->errorInfo()[2];
            return json_encode($response);
        }else{
            
    
                self::$database-> commit();
                

                
            
    }
    }
 }
 http_response_code(201);
 return json_encode($stmnt1->rowCount());
}


 public function find_all(){
    $sql1 = "SELECT  * FROM daily_count";
    $result = self::$database->query($sql1);
    
    if(self::$database->errorInfo()[2]){
        http_response_code(500);
        return json_encode(['message'=>self::$database->errorInfo()[2]]);
    }else{
        if(!$result->rowCount()){
            http_response_code(200);
            return json_encode(["message"=>"No result found"]);
        }else{
            http_response_code(200);
            return json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        }
    }

 }

 public function find_one($identity){
    $sql1="SELECT * from daily_count WHERE date = :date";
    $stmnt1 = self::$database->prepare($sql1);
    $stmnt1->bindParam(':date', $identity);
    $stmnt1->execute();
    if($stmnt1->errorInfo()[2]){
        http_response_code(500);
        return json_encode(["message"=>$stmnt1->errorInfo()[2]]);
    }else{
        if(!$stmnt1->rowCount()){
            http_response_code(200);
            return json_encode(['message'=>"No results found"]);
        }else{
        http_response_code(200);
    return json_encode($stmnt1->fetchall(PDO::FETCH_ASSOC));
        }
    }
 }

 public function use_model_count(){
    $sql1 = "SELECT DISTINCT model from touse_daily_count";
    $result = self::$database->query($sql1);
    if(self::$database->errorInfo()[2]){

    }else{
        if($result->rowCount()){
            http_response_code(200);
            return json_encode($result->fetchAll());

        }else{
            http_response_code(200);
            return json_encode(['message'=>"No results Found"]);
        }
    }
 }
}