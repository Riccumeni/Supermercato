<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$server = "localhost";
$username = "root";
$pass = "";
$db = "Supermercato";

$data = json_decode(file_get_contents("php://input"));
$email = $data->email;
$password = $data->password;

$conn = new mysqli($server, $username, $pass, $db);

if($conn && (!empty($email) && !empty($password))){
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $sql = "insert into utente (email, password, permessi, carrello) values ('$email', '$password_hashed', 'u', '[]')";
    $result = $conn->query($sql);
    if($result){
        echo json_encode(array("success" => true, "message" => "Utente inserito correttamente"));
    }else{
        echo json_encode(array("success" => false, "message" => "Errore con i campi"));
    }
}else{
    echo json_encode(array("success" => false, "message" => "Errore con il database o campi mancanti"));
}

?>