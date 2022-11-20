<?php

function database_connect(){
    try{
    $database = new PDO("mysql:host=localhost;dbname=elegance", 'isiadeg', '0Ae:2pWPPP');
    return $database;
    }catch(EXCEPTION $e){
        echo $e->getMessage();
    }
}