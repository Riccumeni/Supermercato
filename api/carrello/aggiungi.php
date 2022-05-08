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
        
        if($result->num_rows>0){
            if(file_exists($percorsoCarrello)){
                $handler = fopen($percorsoCarrello, 'r');

                $size = 1024;
                
                while(!feof($handler)){
                    $content = fread($handler, $size);
                }
    
                fclose($handler);

                $jsonCarrello = json_decode($content, true);
                
                $trovato = false;

                // controlla se l'elemento è già presente nel carrello
                foreach ( $jsonCarrello as $element ) {
                    if ( $codiceProdotto == $element['codiceProdotto'] ) {
                        $element['quantita'] += $quantita;
                        $jsonCarrello[$element['posizione']] = $element;
                        $trovato = true;
                    }
                }

                // se non è presente lo crea
                if($trovato == false){
                    echo array_key_last($jsonCarrello) + 1;
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