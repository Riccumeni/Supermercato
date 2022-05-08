<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$server = "localhost";
$username = "root";
$password = "";
$db = "Supermercato";

$data = json_decode(file_get_contents("php://input"));
$id = $data->id;

$conn = new mysqli($server, $username, $password, $db);

if($conn){
    $sql = "select permessi from utente where id='$id'";

    $result = $conn->query($sql);

}else{
    echo json_encode(array("success" => false, "message" => "Errore con il database"));
}
?>