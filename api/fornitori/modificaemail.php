<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

//includiamo configurazioni e oggetti
include_once '../resources/db.php';

//Connessione db
$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$email = $data->email;
$nome_fornitore = $data->nome_fornitore;

if($db && (!empty($email) && !empty($nome_fornitore))){
    $sql = "update fornitore set email = '$email' where nome like '$nome_fornitore'";
    $db->query($sql);
    
    if($db->affected_rows > 0){
        echo json_encode(["success" => true, "message" => "email modificata correttamente"]);
    }else{
        echo json_encode(["success" => false, "message" => "errore"]);
    }
}else{
    echo json_encode(["success" => false, "message" => "errore con il db"]);
}
?>