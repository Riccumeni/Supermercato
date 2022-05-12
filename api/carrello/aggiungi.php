<?php
// UPDATE `utente` SET `carrello` = '[{\"codice_prodotto\" : 1, \"quantita\" : 3}]' WHERE `utente`.`id` = 1;
    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: POST");
    
    $data = json_decode(file_get_contents("php://input"));

    $codiceUtente = $data -> codiceUtente;
    $codiceProdotto = $data -> codiceProdotto;
    $quantita = $data -> quantita;

    $server = "localhost";
    $username = "root";
    $password = "";
    $db = "Supermercato";

    $conn = new mysqli($server, $username, $password, $db);

    if($conn){

        $sql = "select carrello from utente where id='$codiceUtente'";

        $result = $conn->query($sql);

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $carrello = $row["carrello"];
                $carrello = json_decode($carrello);
            }
            
            $trovato = false;
            for($i = 0; $i<count($carrello); $i++){
                if($carrello[$i]->codice_prodotto == $codiceProdotto){
                    $trovato = true;
                    $carrello[$i]->quantita += $quantita;
                    $carrello = json_encode($carrello);
                    $sql = "UPDATE `utente` SET `carrello` = '$carrello' WHERE id = '$codiceUtente'";
                    $conn->query($sql);
                    if($conn->affected_rows > 0){
                        echo json_encode(array("success" => true, "message" => "Quantita modificata correttamente"));
                    }else{
                        echo json_encode(array("success" => false, "message" => "Errore, quantita non modificata"));
                    }
                }
            }

            if($trovato == false){
                $sql = "select id from prodotto where id = '$codiceProdotto'";
                $result = $conn->query($sql);
                if($result->num_rows > 0){
                    array_push($carrello, array("codice_prodotto" => $codiceProdotto, "quantita" => $quantita));
                    $carrello = json_encode($carrello);
                    $sql = "UPDATE `utente` SET `carrello` = '$carrello' WHERE id = '$codiceUtente'";
                    $conn->query($sql);
                    if($conn->affected_rows > 0){
                        echo json_encode(array("success" => true, "message" => "Prodotto aggiunto nel carrello correttamente"));
                    }else{
                        echo json_encode(array("success" => false, "message" => "Errore, prodotto non aggiunto"));
                    }
                }else{
                    echo json_encode(array("success" => false, "message" => "Prodotto non esistente"));
                }
            }
        }else{
            echo json_encode(array("success" => false, "message" => "Nessun carrello trovato"));
        }
    }else{
        $array = array("success" => false, "message" => "Errore con la connessione con il database");
    }

    $conn->close();

?>