<?php
//require_once("./call_functions.php");
class Movement{
 static protected $database;
 static public function set_database($database){
    self::$database = $database;
 }   


 public function create($arr){
    $sql="INSERT INTO movement (
        movementType,
    date,
    productTag,
    productType,
    personName,
    purpose,
    extraInfo
    ) VALUES(?,?,?,?,?,?,?
        
    )";

    $sql1="INSERT INTO tousemovement (
        movementType,
    type,
    productTag,
    productType,
    personName,
    purpose
  
    ) VALUES(
        ?,
        'movement',
        ?,
        ?,
        ?,
        ?
        
    )";

    $sql2 = "SELECT * FROM product_status WHERE  tag = :tag ";


    $stmnt=self::$database->prepare($sql);
    $stmnt1 = self::$database->prepare($sql1);
    $stmnt2 = self::$database->prepare($sql2);
    $stmnt2->bindValue(":tag", $arr->productTag);
    self::$database->beginTransaction();
    $stmnt->execute([$arr->movementType,
   $arr->date,
    $arr->productTag,
    $arr->productType,
    $arr->personName,
    $arr->purpose,
    $arr->extraInfo]);
    if(isset($stmnt->errorInfo()[2])){
        self::$database->rollBack();
        http_response_code(500);
        $response = new stdClass();
        $response->message=$stmnt->errorInfo()[2];
        return json_encode($response);
        
    }else{
        $stmnt1->execute([
            $arr->movementType,
    
    $arr->productTag,
    $arr->productType,
    $arr->personName,
    $arr->purpose
    
        ]);
        if(isset($stmnt1->errorInfo()[2])){
            self::$database->rollBack();
            http_response_code(500);
            $response = new stdClass();
            $response->message=$stmnt1->errorInfo()[2];
            return json_encode($response);
        }else{
            $result = $stmnt2->execute();
            if(isset($stmnt2->errorInfo()[2])){
                self::$database->rollBack();
            http_response_code(500);
            $response = new stdClass();
            $response->message=$stmnt2->errorInfo()[2];
            return json_encode($response);
            }else{
                if($row = $stmnt2->fetch()){
                    if(strtotime($arr->date) > strtotime($row['date'])){
                        if(htmlentities(strtolower(trim($arr->movementType))/* I am putting a bracket after this comment but it may caused error in the future*/) == "collect" ||
                        htmlentities(strtolower(trim($arr->movementType))) === "repair"
                        ) /*
                        )  -> I am removing this bracket but it may caused error in the future
                        
                        */
                        {
                        $sql3=" update product_status SET 
                        status = 'with ".$arr->personName."',
                        date = :date
                        WHERE tag = 
                        :productTag";
                        $stmnt3 = self::$database->prepare($sql3);
                        $stmnt3->execute([
                         ':productTag' => $arr->productTag,
                         ':date'=>$arr->date
                        ]);
                         if(isset($stmnt3->errorInfo()[2])){
                            self::$database->rollBack();
                            http_response_code(500);
                            $response = new stdClass();
                            $response->message=$stmnt3->errorInfo()[2];
                            return json_encode($response);
                        }else{
                            self::$database-> commit();
                

                http_response_code(201);
                return json_encode(['message'=>"Product collected has been entered successfully"]);//$stmnt1->rowCount());
                        }
                    }else{
                        if(strpos($row['status'], $arr->personName) !== false){
                        $sql5 = "update product_status SET 
                        status = 'On Shelf',
                        date = :date
                        WHERE tag = 
                        :productTag";
                        $stmnt5 = self::$database->prepare($sql5);
                        $stmnt5->execute([
                         ':productTag'=>$arr->productTag
                        ]);
                        if(isset($stmnt5->errorInfo()[2])){
                            self::$database->rollBack();
                            http_response_code(500);
                            $response = new stdClass();
                            $response->message=$stmnt5->errorInfo()[2];
                            return json_encode($response);
                        }else{
                            self::$database-> commit();
                            http_response_code(201);
                            return json_encode(['message'=>"Product returned has been recorded accordingly"]);//$stmnt1->rowCount());
                        }}else{
                            self::$database->rollBack();
                            http_response_code(500);
                            $response = new stdClass();
                            $response->message="The person who collected the laptop is not the one returning it";
                            return json_encode($response);
                        }

                    }
                    }
                }else{
                    if(htmlentities(strtolower(trim($arr->movementType)) !== "returned")){
                    $sql4 = "INSERT INTO product_status (tag, status, date)
                    VALUES(?,?, ?)";
                    $stmnt4 = self::$database->prepare($sql4);
                    $stmnt4->execute([$arr->productTag, "with ".$arr->personName, 
                $arr->date]);
                    if(isset($stmnt4->errorInfo()[2])){
                        self::$database->rollBack();
                        http_response_code(500);
                        $res = new stdClass();
                        $res->message = $stmnt4->errorInfo()[2];
                        return json_encode($res);
                    }else{
                self::$database-> commit();
                

                http_response_code(201);
                return json_encode(["message"=>"Product's First Movement from the shelf has been entered correctly"]);//$stmnt4->rowCount());
                    }
                    }else{
                        self::$database->rollBack();
                        http_response_code(500);
                        $res = new stdClass();
                        $res->message = "Someone cannot return something he did not collect";
                        return json_encode($res);
                    }}
                self::$database-> commit();
                

                http_response_code(201);
                return json_encode(["message"=>"Product's  Movement has been entered correctly"]);//$stmnt4->rowCount());
                
            }
    }
    }
 }


 public function find_all(){
    $sql1 = "SELECT  * FROM movement";
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
    $sql1="SELECT * from movement WHERE productTag = :productTag";
    $stmnt1 = self::$database->prepare($sql1);
    $stmnt1->bindParam(':productTag', $identity);
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
}