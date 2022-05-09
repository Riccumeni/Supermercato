<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$server = "localhost";
$username = "root";
$password = "";
$db = "Supermercato";

$data = json_decode(file_get_contents("php://input"));
$codice_utente = $data->id;

$conn = new mysqli($server, $username, $password, $db);

if($conn){
    $sql = "select carrello from utente where id = '$codice_utente'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $carrello = $row["carrello"];

    $carrello = json_decode($carrello);

    $fattura = array("totale" => 0);
    $not_success = array();
    foreach($carrello as $prodotto){
        $sql = "select nome, quantita, prezzo, prezzo*'$prodotto->quantita' as calcolo from prodotto where id='$prodotto->codice_prodotto'";
        $result = $conn->query($sql);
        $row = $result -> fetch_assoc();
        if($row["quantita"] >= $prodotto->quantita){
            $fattura['totale'] += $row["calcolo"];
            array_push($fattura, array("nome" => $row["nome"], "quantita" => $prodotto->quantita, "prezzo" => $row["prezzo"]));
        }else{
            array_push($not_success, array("nome" => $row["nome"], "quantita disponibile" => $row["quantita"], "quantita desiderata" => $prodotto->quantita));
        }
    }

    if(count($not_success) > 0){
        echo json_encode(array("success" => false, "message" => "ordine fallito perché era presente una quantita superiore di alcuni prodotti rispetto a quelli in magazzino", "data" => ($not_success)));
    }else{
        // todo: dare una ricevuta
        $data_oggi = date("Y/m/d");
        $ordine = json_encode($carrello);
        $totale = $fattura["totale"];
        $conn->begin_transaction();
        try{
            $sql = "insert into operazione (valore, codice_utente, data, ordine) values ('$totale', '$codice_utente', '$data_oggi', '$ordine')";
            $conn->query($sql);
            $sql = "update utente set carrello = '[]' where id='$codice_utente'";
            $conn->query($sql);
            foreach($carrello as $prodotto){
                $sql = "update prodotto set quantita = quantita - '$prodotto->quantita' where id='$prodotto->codice_prodotto'";
                $conn->query($sql);
                // todo chiedere alla facchini sui trigger
            }


            $conn->commit();

        }catch (mysqli_sql_exception $exception) {
            $mysqli->rollback();
            throw $exception;
        }
        // $sql = "insert into operazione (valore, codice_utente, data, ordine) values ('$totale', '$codice_utente', '$data_oggi', '$ordine')";

        // $result = $conn->query($sql);
        // if($result){
        //     $sql = "update utente set carrello = '[]' where id='$codice_utente'";
        //     $conn->query($sql);
        //     if($conn->affected_rows > 0){
        //         echo json_encode(array("success" => true, "data" => $fattura));
        //     }else{
        //         echo json_encode(array("success" => false, "message" => "Errore nella pulizia del carrello"));
        //     }
        // }else{
        //     echo json_encode(array("success" => false, "message" => "Errore nell'inserimento dell'operazione"));
        // }
    }
}else{
    echo json_encode(array("success" => false, "message" => "Errore con il database"));
}
?>