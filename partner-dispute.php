<?php
class Dispute{
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
    
    $prepared = "select * from dispute";

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
$sql = "INSERT INTO dispute (
    `status-before`,
    `status-changed-to`, 
    `status-changed-by`,
    `extra-info`,
     `date-returned`,
     `date-change-entered`)VALUES(
         :before,
         :changed,
         :changedby,
         :extrainfo,
         :dateR,
         :dateCR)";
 $prepared = self::$database->prepare($sql);
        $prepared->bindValue(':before', $arr->statusBefore ?? null);
        $prepared->bindValue(':changed', $arr->statusChangedTo ?? null);
        $prepared->bindValue(':changedby', $arr->statusChangedBy ?? null);
        $prepared->bindValue(':extrainfo', $arr->extrainfo ?? null);
        $prepared->bindValue(':dateR', $arr->dateReturned ?? null);
        $prepared->bindValue(':dateCR', $arr->dateChangeEntered ?? null);
        //     '". $arr->statusChangedTo."',
    //   '".$arr->statusChangedBy?$arr->statusChangedBy:null."',
    //   '".$arr->extraInfo?$arr->extraInfo:null."',
    //  '".$arr->dateReturned?$arr->dateReturned:null."',
    //   '".$arr->dateChangeEntered?$arr->dateReturned:null."' )";
    
    //echo $sql;
    
     
self::$database->beginTransaction();
try{
$result = self::execute_query($prepared);
if($prepared->errorInfo()[2]){
    self::$database->rollBack();
    throw new EXCEPTION($prepared->errorInfo()[2]);
}else{
    
            self::$database->commit();
        


    
}}catch(EXECPTION $e){
    
    http_response_code(500);
    return json_encode($e->getMessage());
}


    

    http_response_code(201);
    return json_encode(self::$database->lastInsertId());



}
}