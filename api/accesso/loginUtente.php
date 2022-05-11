<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$server = "localhost";
$username = "root";
$password = "";
$db = "Supermercato";

$data = json_decode(file_get_contents("php://input"));
$email = $data->email;
$password = $data->password;

$conn = new mysqli($server, $username, $password, $db);

if($conn && (!empty($email) && !empty($password))){
    
}else{
    echo json_encode(array("success" => false, "message" => "Errore con il database o campi mancanti"));
}

$conn->close();
?>