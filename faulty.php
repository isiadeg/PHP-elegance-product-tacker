<?php //require_once("./call_functions.php");
class Faulty{
    private static $database;
    private $date ;
     private $productTag;
     private $repaired;
    private $faultLevel;
    private $withEngineer;
    private $faultDescription;

    public function __construct(){
         $this->date = "";
         $this->productTag  = "";
         $this->repaired = "";
         $this->faultLevel = "";
         $this->withEngineer = "";
         $this->faultDescription = "";
    }

    public static function set_database($database){
        self::$database =$database;
    }


    public static function exec_error($prepared_statement){
        try{
        $prepared_statement->execute();
        $row = $prepared_statement->fetchall(PDO::FETCH_ASSOC);
        if(isset($prepared_statement->errorInfo()[2])){
           return $prepared_statement;
        }else{
            return $row;
        }
        


        }catch(EXCEPTION $e){
            http_response_code(500);
            $response = new stdClass();
            $response->message = $e->getMessage();
            return json_encode($response);
        }
        
    }


    public function create($arr){
        $sql1= "INSERT INTO faulty (date,
        productTag,
        repaired,
       faultLevel,
       withEngineer,
       faultDescription) VALUES
      (:date, :productTag, :repaired, :faultLevel, :withEngineer, :faultDescription)";

      //Go and get the product from status and check if the dateis past the the date of this 
      //transaction
      //$sql2 = "SELECT * from product_status WHERE tag = ?";

      //$sql3 = "UPDATE product_status SET status = 'sold',
      //date = :date WHERE
      //tag = :productTag";


      ///// preparing
        $stmnt1 = self::$database->prepare($sql1);
        // $stmnt2 = self::$database->prepare($sql2);
        // $stmnt3 = self::$database->prepare($sql3);

        ////Bind Values;
            $stmnt1->bindValue(':date', $arr->date);
            $stmnt1->bindValue(':productTag', $arr->productTag); 
            $stmnt1->bindValue(':repaired', $arr->repaired);
            $stmnt1->bindValue(':faultLevel', $arr->faultLevel);
            $stmnt1->bindValue(':withEngineer', $arr->withEngineer);   
            $stmnt1->bindValue('faultDescription', $arr->withEngineer);


            // $stmnt2->bindValue(1, $arr->productTag);

            // $stmnt3->bindValue(':productTag', $arr->productTag); 
            // $stmnt3->bindValue(':date', $arr->date);

        ///begin Transaction

        //self::$database->beginTransaction();
        //if($row1 = this->exec_error($stmnt1))
        
        $row1 = $this->exec_error($stmnt1);
        if(is_object($row1)){
            if($row1->errorInfo()[2]){
                http_response_code(500);
                return json_encode($row1->errorInfo()[2]);
            }}else{
                http_response_code(201);
                return json_encode(['message'=> "Fault for {$arr->productTag} entered successfully "]);
            }
        



    }
 
    public function prepare_sqlerror($error_stmnt){
        
        $response = new stdClass();
        $response->message = $error_stmnt->errorInfo()[2];
        echo $response->message;
        http_response_code(500);
        return json_encode($response);
    }



    public function find_one($identity){
        $sql1 = "SELECT * from faulty where productTag = :productTag";
        $stmnt1 = self::$database->prepare($sql1);
        $stmnt1->bindParam(':productTag', $identity);
        $single_result = new self();
        $stmnt1->execute();
        $stmnt1->setFetchMode(PDO::FETCH_INTO, $single_result);
        //$row1 = $stmnt1->fetchall(PDO::FETCH_ASSOC);
        $stmnt1->fetch();

        if(isset($stmnt1->errorInfo()[2])){
        $this->prepare_sqlerror($stmnt1);   
        }else{
            if($stmnt1->rowCount()){
                http_response_code(200);
                return json_encode($single_result);

            }else{
                return json_encode(['response'=>"No results found"]);
            }
        }
    }

    public function find_all(){
        $sql1 = "SELECT *  from faulty";
        $result = self::$database->query($sql1);
      //  $result->execute();
        if(isset(self::$database->errorInfo()[2])){
            $this->prepare_sqlerror(self::$database);
        }else{
            if($result->rowCount()){
               // echo $result->rowCount()."\n";
               http_response_code(200);
                return json_encode($result->fetchall(PDO::FETCH_ASSOC));
            }else{
                http_response_code(200);
                return json_encode(['message'=>"No results found"]);
            }
        }
    }

    public function update($arr){
        $sql1= "UPDATE faulty SET 
        date = :date,
        productTag = :productTag,
        repaired = :repaired,
       faultLevel = :faultLevel,
       withEngineer = :withEngineer,
       faultDescription = :faultDescription
      WHERE productTag = :productTag AND date = :date ";

$stmnt1 = self::$database->prepare($sql1);


////Bind Values;
    $stmnt1->bindValue(':date', $arr->date);
    $stmnt1->bindValue(':productTag', $arr->productTag); 
    $stmnt1->bindValue(':repaired', $arr->repaired);
    $stmnt1->bindValue(':faultLevel', $arr->faultLevel);
    $stmnt1->bindValue(':withEngineer', $arr->withEngineer);   
    $stmnt1->bindValue('faultDescription', $arr->withEngineer);

    $row1 = $this->exec_error($stmnt1);
    if(is_object($row1)){
        if($row1->errorInfo()[2]){
            http_response_code(500);
            return json_encode($row1->errorInfo()[2]);
        }else{
            http_response_code(201);
            return json_encode(['message'=> "Fault for {$arr->productTag} updated successfully "]);
        }
    }
    }
}