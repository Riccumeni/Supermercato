<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"));
$email = $data->email;

include_once '../resources/db.php';

$database = new Database();    
$conn = $database->getConnection();

if($conn && !empty($email)){
    $comb = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $newpass = array(); 
    $combLen = strlen($comb) - 1; 

    for ($i = 0; $i < 8; $i++) { 
        $n = rand(0, $combLen);
        $newpass[] = $comb[$n];
    }
    $newpass = implode($newpass); 

    $pass_hashed = password_hash($newpass, PASSWORD_DEFAULT);

    $sql = "update utente set password = '$pass_hashed' where email='$email'";
    $conn->query($sql);
    if($conn->affected_rows > 0){
        mail($email, "Recupero password", "E' stata modificata la password in -> $newpass \nRicordarsi di modificarla il prima possibile");
        echo json_encode(array("success" => true, "message" => "mail inviata!"));
    }else{
        echo json_encode(array("success" => false, "message" => "Errore!"));
    } 
}else{
    echo json_encode(array("success" => false, "message" => "Errore con la connessione al database o campi mancanti!"));
}
?>