<?php
    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: POST");

    $server = "localhost";
    $username = "root";
    $password = "";
    $db = "Supermercato";

    $conn = new mysqli($server, $username, $password, $db);

    if($conn){
        $sql = "select * from prodotto";

        $result = $conn->query($sql);

        if($result->num_rows > 0){
            $prodotti = array();
            while($row = $result->fetch_assoc()){
                $prodotto = array("id" => $row["id"], "nome" => $row["nome"], "quantita" => $row["quantita"], "prezzo" => $row["prezzo"]);
                array_push($prodotti, $prodotto);
            }
            echo json_encode(array("success" => true, "data" => $prodotti));
        }else{
            echo json_encode(array("success" => false, "message" => "Nessun prodotto trovato"));
        }
    }else{
        echo json_encode(array("success" => false, "message" => "Errore con la connessione con il database"));
    }

    $conn->close();

?>