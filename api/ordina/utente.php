<?php
// insert into operazione (valore, codice_utente, data, ordine) values (50, 1, STR_TO_DATE('2022-03-15', '%Y-%m-%d'), '[]');
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

    $totale = 0;
    $not_success = array();
    foreach($carrello as $prodotto){
        $sql = "select nome, quantita, prezzo*'$prodotto->quantita' as calcolo from prodotto where id='$prodotto->codice_prodotto'";
        $result = $conn->query($sql);
        $row = $result -> fetch_assoc();
        if($row["quantita"] >= $prodotto->quantita){
            $totale += $row["calcolo"];
        }else{
            array_push($not_success, array("nome" => $row["nome"], "quantita disponibile" => $row["quantita"], "quantita desiderata" => $prodotto->quantita));
        }
    }

    if(count($not_success) > 0){
        echo json_encode(array("success" => false, "message" => "ordine fallito perché era presente una quantita superiore di alcuni prodotti rispetto a quelli in magazzino", "data" => ($not_success)));
    }else{
        // todo inserire il record nel database, pulire il carrello, dare una ricevuta
        $data_oggi = date("Y/m/d");
        $ordine = json_encode($carrello);
        $sql = "insert into operazione (valore, codice_utente, data, ordine) values ('$totale', '$codice_utente', '$data_oggi', '$ordine')";
        $result = $conn->query($sql);
        if($result){
            echo "yeee";
        }else{
            echo "noo";
        }
    }
}else{
    echo json_encode(array("success" => false, "message" => "Errore con il database"));
}
?>