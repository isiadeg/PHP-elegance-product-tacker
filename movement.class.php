<?php
//require_once("./call_functions.php");
class Movement{
 static protected $database;
 static public function set_database($database){
    self::$database = $database;
 }   


 public function create($arr){
     if(htmlentities($arr->movementType) == "" || 
     htmlentities($arr->date) == "" ||
     htmlentities($arr->productTag) == "" ||
     htmlentities($arr->productType) == "" ||
     htmlentities($arr->personName) == ""){
        http_response_code(500);
        return json_encode(['message'=>'You have to fill all required fields']);
     }else{
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
    if(!($stmnt->execute([$arr->movementType,
   $arr->date,
    $arr->productTag,
    $arr->productType,
    $arr->personName,
    $arr->purpose,
    $arr->extraInfo])))
    {
        self::$database->rollBack();
        http_response_code(500);
        $response = new stdClass();
        $response->message="An error ocurred";
        return json_encode($response);
        
    }
    if(isset($stmnt->errorInfo()[2])){
        self::$database->rollBack();
        http_response_code(500);
        $response = new stdClass();
        $response->message=$stmnt->errorInfo()[2];
        return json_encode($response);
        
    }else{
       if(!($stmnt1->execute([
            $arr->movementType,
    
    $arr->productTag,
    $arr->productType,
    $arr->personName,
    $arr->purpose
    
        ]))){
            self::$database->rollBack();
            http_response_code(500);
            $response = new stdClass();
            $response->message=$stmnt1->errorInfo()[2];
            return json_encode($response);
        }
        if(isset($stmnt1->errorInfo()[2])){
            self::$database->rollBack();
            http_response_code(500);
            $response = new stdClass();
            $response->message=$stmnt1->errorInfo()[2];
            return json_encode($response);
        }else{
            $result = $stmnt2->execute();
            if(!$result){
                self::$database->rollBack();
                http_response_code(500);
                $response = new stdClass();
                $response->message="An error ocurred";
                return json_encode($response);
            }
            if(isset($stmnt2->errorInfo()[2])){
                self::$database->rollBack();
            http_response_code(500);
            $response = new stdClass();
            $response->message=$stmnt2->errorInfo()[2];
            return json_encode($response);
            }else{
                if($row = $stmnt2->fetch() ){
                    
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
                        if(!($stmnt3->execute([
                         ':productTag' => $arr->productTag,
                         ':date'=>$arr->date
                        ]))){
                            self::$database->rollBack();
                            http_response_code(500);
                            $response = new stdClass();
                            $response->message="An error occured";
                            return json_encode($response);
                        }
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
                        if(strpos($row['status'], $arr->personName) !== false ||
                        $row['status'] === "sold" || htmlentities(strtolower(trim($arr->movementType))) == "brought"){
                             
                        $sql5 = "update product_status SET 
                        status = 'On Shelf',
                        date = :date
                        WHERE tag = :productTag";
                        $stmnt5 = self::$database->prepare($sql5);
                        if(!($stmnt5->execute([
                         ':productTag'=>$arr->productTag,
                         ':date'=>$arr->date
                        ]))) {
                           // print_r($stmnt5);
                           self::$database->rollBack();
                            http_response_code(500);
                            $response = new stdClass();
                            $response->message="An error occured";
                            return json_encode($response);
                        }
                        if(isset($stmnt5->errorInfo()[2])){
                            self::$database->rollBack();
                            http_response_code(500);
                            $response = new stdClass();
                            $response->message=$stmnt5->errorInfo()[2];
                            return json_encode($response);
                        }else{
                            if($row['status'] == "sold"){
                                $sql299 = "update sales SET 
                        Returned = 'Yes',
                        Date_Returned = :date,
                        Unsuccessful_sale = 'Yes'
                        WHERE productTag = :productTag";
                        $stmnt299 = self::$database->prepare($sql299);
                        if(!($stmnt299->execute([
                         ':productTag'=>$arr->productTag,
                         ':date'=>$arr->date
                        ]))) {
                            // print_r($stmnt5);
                            self::$database->rollBack();
                             http_response_code(500);
                             $response = new stdClass();
                             $response->message="An error on the server occured";
                             return json_encode($response);
                         }
                         if(isset($stmnt299->errorInfo()[2])){
                             self::$database->rollBack();
                             http_response_code(500);
                             $response = new stdClass();
                             $response->message=$stmnt299->errorInfo()[2];
                             return json_encode($response);
                         }else{
                            self::$database-> commit();
                            http_response_code(201);
                            return json_encode(['message'=>"Product returned after an Unsuccessful sale has been recorded"]);//$stmnt1->rowCount());
                           
                         }


                            }else{
                            self::$database-> commit();
                            http_response_code(201);
                            if(  htmlentities(strtolower(trim($arr->movementType))) == "brought" ){
                            return json_encode(["message"=>"This product has been recorded as being brought"]);
                           // return json_encode(["message"=>"Product's First Movement from the shelf has been entered correctly"]);//$stmnt4->rowCount());
                                
                                }
                            return json_encode(['message'=>"Product returned has been recorded accordingly"]);//$stmnt1->rowCount());
                            }
                        }}else{
                            if(!(isset($arr->confirmed))){
                            self::$database->rollBack();
                            http_response_code(500);
                            $response = new stdClass();
                            $response->message="The person who collected the laptop is not the one returning it";
                            return json_encode($response);
                        }else{
                            $sql5 = "update product_status SET 
                        status = 'On Shelf',
                        date = :date
                        WHERE tag = :productTag";
                        $stmnt5 = self::$database->prepare($sql5);
                        if(!($stmnt5->execute([
                         ':productTag'=>$arr->productTag,
                         ':date'=>$arr->date
                        ]))) {
                           // print_r($stmnt5);
                           self::$database->rollBack();
                            http_response_code(500);
                            $response = new stdClass();
                            $response->message="An error occured";
                            return json_encode($response);
                        }
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
                        }
                        }
                    }

                    }
                    }
                    // else{
                    //     if(htmlentities(strtolower(trim($arr->movementType))) == "returned" ){
                    //         http_response_code(500);
                    //         return json_encode(['message'=> "You cannot return something before you collect it"]);
                    //     }
                    // }
                }else{
                    if(  htmlentities(strtolower(trim($arr->movementType))) == "brought" ){
                    $sql4 = "INSERT INTO product_status (tag, status, date)
                    VALUES(?,?, ?)";
                    $stmnt4 = self::$database->prepare($sql4);
                    if(!($stmnt4->execute([$arr->productTag, "On Shelf",
                $arr->date]))){
                    self::$database->rollBack();
                        http_response_code(500);
                        $res = new stdClass();
                        $res->message = "An error ocurred";
                        return json_encode($res);
                }
                    if(isset($stmnt4->errorInfo()[2])){
                        self::$database->rollBack();
                        http_response_code(500);
                        $res = new stdClass();
                        $res->message = $stmnt4->errorInfo()[2];
                        return json_encode($res);
                    }else{
                self::$database-> commit();
                

                http_response_code(201);
                return json_encode(["message"=>"Since this product has never been recorded before, it seems it's only being brought
                to stay on the shelf temporarily NOT as product brought in for sale. If this is not the case, You have to enter this product 
                under 'Enter laptops Info'"]);
               // return json_encode(["message"=>"Product's First Movement from the shelf has been entered correctly"]);//$stmnt4->rowCount());
                    }
                    }else{
                        self::$database->rollBack();
                        http_response_code(500);
                        $res = new stdClass();
                        $res->message = "How can you collect or return a product that does not exist on records. You have 
                        to enter this product first under 'Enter Laptops Info' ";
                        return json_encode($res);
                    }}
                self::$database-> commit();
                

                http_response_code(201);
                return json_encode(["message"=>"Product's  Movement has been entered correctly but nothing was changed in the product status
                since the date you provided is earlier than the date for the product's status"]);//$stmnt4->rowCount());
                
            }
    }
    }
 }}


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


 public function get_previous_people(){
     $sql = "SELECT DISTINCT personName FROM tousemovement";
     $result = self::$database->query($sql);
     $allres = $result->fetchAll(PDO::FETCH_ASSOC);
    
     if(count($allres) > 0){
         http_response_code(200);
         return json_encode(["message"=>$allres]);
     }
     else{
         http_response_code(200);
         return json_encode(["message"=> []]);
     } 
 }
}