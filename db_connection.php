<?php
require_once('./core.php');
class connect{
    private $dbconnect;
    public function __construct(){
        try{
            $this->dbconnect  = new PDO("mysql:host=localhost;dbname=laptop", 'isiadeg', '0Ae:2pWPPP');
        
        }catch(EXCEPTION $e){
            die($e->getMessage);
        }
    }
    public function get datatbase(){
        
    }
   
}