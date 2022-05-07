<?php
    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: POST");
    
    $data = json_decode(file_get_contents("php://input"));

    $codice = $data -> codice;

    $percorsoCarrello = $codice . '_chart.txt';
    $percorsoCarrello = "../../carrelli/" . $percorsoCarrello;

    $server = "localhost";
    $username = "root";
    $password = "";
    $db = "Supermercato";

    $conn = new mysqli($server, $username, $password, $db);

    if($conn){
        if(file_exists($percorsoCarrello)){
            $handler = fopen($percorsoCarrello, 'r');

            $size = 1024;

            while (!feof($handler)) {
                $content = fread($handler, $size);
            }

            fclose($handler);

            $array = array("success" => true, "data" => json_decode($content));

            echo json_encode($array);
        }else{
            echo "file non trovato";
        }
    }else{
        $array = array("success" => false, "message" => "Errore con la connessione con il database");
    }

    $conn->close();

?>