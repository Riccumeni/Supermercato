<?php
    header("Content-Type: application/json; charset=utf-8");
    header("Access-Control-Allow-Methods: POST");
    
    $data = json_decode(file_get_contents("php://input"));

    $codiceUtente = $data -> codiceUtente;
    $codiceProdotto = $data -> codiceProdotto;
    $quantita = $data -> quantita;


    $percorsoCarrello = $codiceUtente . '_chart.txt';
    $percorsoCarrello = "../../carrelli/" . $percorsoCarrello;

    $server = "localhost";
    $username = "root";
    $password = "";
    $db = "Supermercato";

    $conn = new mysqli($server, $username, $password, $db);

    if($conn){

        $sql = "select * from prodotto where id='$codiceProdotto'";

        $result = $conn->query($sql);

        // TODO Aggiungere che se la quantita desiderata è maggiore di quella effettiva del prodotto fallisce la richiesta

        if($result->num_rows>0){
            if(file_exists($percorsoCarrello)){
                $handler = fopen($percorsoCarrello, 'r');

                $size = 1024;
                
                while(!feof($handler)){
                    $content = fread($handler, $size);
                }
    
                fclose($handler);
                // echo $content;
                $jsonCarrello = json_decode($content, true);
                // Modificare la quantita di uno specifico elemento
                
                $trovato = false;
                foreach ( $jsonCarrello as $element ) {
                    if ( $codiceProdotto == $element['codice'] ) {
                        $element['quantita'] += $quantita;
                        $jsonCarrello[$element['posizione']] = $element;
                        $trovato = true;
                    }
                }

                if($trovato == false){
                    $nuovoProdotto = array("posizione" => array_key_last($jsonCarrello) + 1, "codiceProdotto" => $codiceProdotto, "quantita" => $quantita);
                    array_push($jsonCarrello, $nuovoProdotto);
                }

                $handler = fopen($percorsoCarrello, 'w');
                
                fwrite($handler, json_encode($jsonCarrello));
                
                fclose($handler);

                echo json_encode(array("success" => true, "message" => "prodotto inserito correttamente nel carrello"));
                
            }else{
                echo "file non trovato";
            }
        }else{
            echo json_encode(array("success" => false, "message" => "prodotto non esistente"));
        }
    }else{
        $array = array("success" => false, "message" => "Errore con la connessione con il database");
    }

    $conn->close();

?>