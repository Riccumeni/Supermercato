<?php
    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: POST");
    
    $data = json_decode(file_get_contents("php://input"));

    $codiceUtente = $data -> codiceUtente;
    $codiceProdotto = $data -> codiceProdotto;
    $quantita = $data -> quantita;

    include_once '../resources/db.php';

    $database = new Database();    
    $conn = $database->getConnection();

    if($conn){

        $sql = "select carrello from utente where id='$codiceUtente'";

        $result = $conn->query($sql);

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $carrello = $row["carrello"];
                $carrello = json_decode($carrello);
            }
            // echo json_encode(array("success" => true, "data" => $carrello));
            $trovato = false;
            for($i = 0; $i<count($carrello); $i++){
                if($carrello[$i]->codice_prodotto == $codiceProdotto){
                    $trovato = true;
                    if($carrello[$i]->quantita<$quantita){
                        echo json_encode(array("success" => false, "message" => "Quantita superiore a quella presente nel carrello"));
                        exit;
                    }else if ($quantita == 0){
                        unset($carrello[$i]);
                        $carrello = json_encode($carrello);
                        $sql = "UPDATE `utente` SET `carrello` = '$carrello' WHERE id = '$codiceUtente'";
                        $conn->query($sql);
                        if($conn->affected_rows > 0){
                            echo json_encode(array("success" => true, "message" => "Prodotto eliminato correttamente"));
                        }else{
                            echo json_encode(array("success" => false, "message" => "Errore, prodotto non eliminato"));
                        }
                    }else{
                        $carrello[$i]->quantita -= $quantita;
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
            }

            if($trovato == false){
                echo json_encode(array("success" => false, "message" => "Nessun prodotto trovato"));
            }
        }else{
            echo json_encode(array("success" => false, "message" => "Nessun carrello trovato"));
        }
    }else{
        $array = array("success" => false, "message" => "Errore con la connessione con il database");
    }

    $conn->close();

?>