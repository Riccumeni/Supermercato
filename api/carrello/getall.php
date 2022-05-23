<?php
    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: POST");
    
    $data = json_decode(file_get_contents("php://input"));

    $codiceUtente = $data -> codice;

    include_once '../resources/db.php';

    $database = new Database();    
    $conn = $database->getConnection();

    if($conn){
        $sql = "select carrello from utente where id='$codiceUtente'";

        $result = $conn->query($sql);

        if($result->num_rows > 0){
            
            $row = $result->fetch_assoc();
            $carrello = $row["carrello"];
            $carrello = json_decode($carrello);
            
            echo json_encode(array("success" => true, "data" => $carrello));
        }else{
            echo json_encode(array("success" => false, "message" => "Nessun carrello trovato"));
        }
    }else{
        echo json_encode(array("success" => false, "message" => "Errore con la connessione con il database"));
    }

    $conn->close();

?>