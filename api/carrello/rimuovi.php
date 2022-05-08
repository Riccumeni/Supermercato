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
                    if($trovato){
                        $jsonCarrello[$element['posizione']-1]["posizione"] -= 1;
                    }
                    if ( $codiceProdotto == $element['codiceProdotto'] && $trovato == false) {
                        if($element['quantita'] > $quantita){
                            $element['quantita'] -= $quantita;
                            $jsonCarrello[$element['posizione']] = $element;
                        }else{
                            unset($jsonCarrello[$element['posizione']]);
                            $jsonCarrello = array_merge($jsonCarrello);
                            $trovato = true;
                        }
                        
                    }
                }

                $handler = fopen($percorsoCarrello, 'w');
                
                fwrite($handler, json_encode($jsonCarrello));
                
                fclose($handler);

                echo json_encode(array("success" => true, "message" => "prodotto rimosso correttamente dal carrello"));
                
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