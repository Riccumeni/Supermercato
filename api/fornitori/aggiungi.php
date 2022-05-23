<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

include_once '../resources/db.php';

$database = new Database();    
$conn = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));
$nome_fornitore = $data->nome;
$email_fornitore = $data->email;
$indirizzo_fornitore = $data->indirizzo;

if($conn && !empty($nome_fornitore) && !empty($email_fornitore) && !empty($indirizzo_fornitore)){
    $sql = "insert into fornitore (nome, email, indirizzo) values ('$nome_fornitore', '$email_fornitore', '$indirizzo_fornitore')";

    try{
        if($result){
            $response = array("success" => true, "message" => "fornitore inserito correttamente");

            echo json_encode($response);
        }else{
            echo json_encode(array("success" => false, "message" => "fornitore non inserito correttamente"));
        }
    }catch(Exception $e){
        echo json_encode(array("success" => false, "message" => "fornitore non inserito correttamente"));
    }
    $result = $conn->query($sql);

    
}else{
    echo json_encode(array("success" => false, "message" => "Errore con il database o campi mancanti"));
}
?>