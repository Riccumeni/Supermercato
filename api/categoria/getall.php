<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

include_once("../resources/db.php");
$database = new Database();
$db = $database->getConnection();

if($db){
    $sql = "select * from categoria";

    try{
        $result = $db->query($sql);
        $data = array();
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                array_push($data, $row["titolo"]);
            }

            echo json_encode(["success" => true, "data" => $data]);
        }else{
            echo json_encode(["success" => false, "message" => "nessuna categoria"]);
        }
    }catch(Exception $e){
        echo json_encode(["success" => false, "message" => "Errore"]);
    }
}else{
    echo json_encode(["success" => false, "message" => "Errore con il database"]);
}
?>