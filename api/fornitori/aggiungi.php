<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$server = "localhost";
$username = "root";
$password = "";
$db = "Supermercato";

$data = json_decode(file_get_contents("php://input"));
$nome_fornitore = $data->nome;
$email_fornitore = $data->email;
$indirizzo_fornitore = $data->indirizzo;

$conn = new mysqli($server, $username, $password, $db);

if($conn && !empty($nome_fornitore) && !empty($email_fornitore) && !empty($indirizzo_fornitore)){
    $sql = "insert into fornitore (nome, email, indirizzo) values ('$nome_fornitore', '$email_fornitore', '$indirizzo_fornitore')";

    $result = $conn->query($sql);

    if($result){
        

        $response = array("success" => true, "message" => "fornitore inserito correttamente");

        echo json_encode($response);
    }else{
        echo json_encode(array("success" => false, "message" => "fornitore non inserito correttamente"));
    }
}else{
    echo json_encode(array("success" => false, "message" => "Errore con il database o campi mancanti"));
}
?>