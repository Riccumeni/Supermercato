<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"));
$id = $data->id;
$newpass = $data->nuova_password;

include_once '../resources/db.php';

$database = new Database();    
$conn = $database->getConnection();

if($conn && (!empty($id) && !empty($newpass))){ 

    $pass_hashed = password_hash($newpass, PASSWORD_DEFAULT);

    $sql = "update utente set password = '$pass_hashed' where id='$id'";
    $conn->query($sql);

    if($conn->affected_rows > 0){
        echo json_encode(array("success" => true, "message" => "Password modificata con successo!"));
    }else{
        echo json_encode(array("success" => false, "message" => "Errore con la modifica!"));
    } 
}else{
    echo json_encode(array("success" => false, "message" => "Errore con la connessione al database o campi mancanti!"));
}
?>