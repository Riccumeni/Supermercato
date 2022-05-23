<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"));
$email = $data->email;
$password = $data->password;

include_once '../resources/db.php';

$database = new Database();    
$conn = $database->getConnection();

if($conn && (!empty($email) && !empty($password))){
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $sql = "insert into utente (email, password, permessi, carrello) values ('$email', '$password_hashed', 'u', '[]')";
    try{
        $result = $conn->query($sql);
        if($result){
            echo json_encode(array("success" => true, "message" => "Utente inserito correttamente, fare il login"));
        }else{
            echo json_encode(array("success" => false, "message" => "Errore con i campi"));
        }
    }catch(Exception $e){
        echo json_encode(array("success" => false, "message" => "Errore con i campi")); 
    }
    
}else{
    echo json_encode(array("success" => false, "message" => "Errore con il database o campi mancanti"));
}

?>