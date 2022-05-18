<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"));

$nome_categoria = $data->nome;

include_once("../resources/db.php");
$database = new Database();
$db = $database->getConnection();

if($db && !empty($nome_categoria)){
    $sql = "insert into categoria (titolo) values ('$nome_categoria')";

    try{
        $db->query($sql);
        echo json_encode(["success" => true, "message" => "Operazione riuscita"]);
    }catch(Exception $e){
        echo json_encode(["success" => false, "message" => "Categoria già presente"]);
    }
}else{
    echo json_encode(["success" => false, "message" => "Errore con il database o campi mancanti"]);
}
?>