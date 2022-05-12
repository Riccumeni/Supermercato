<?php
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");

	//includiamo configurazioni e oggetti
	include_once '../resources/db.php';

	//Connessione db
	$database = new Database();
	$db = $database->getConnection();

	$data = json_decode(file_get_contents("php://input"));

	if(!empty($data->codice) && !empty($data->quantita) && $data->quantita >= 1){

        $query = "UPDATE prodotto SET quantita=quantita-'$data->quantita' WHERE id='$data->codice' and (quantita-'$data->quantita'>0)"; // ho aggiunto che la quantita dopo la sottrazione deve essere maggiore di 0
		$q = $db->prepare($query);
        if($q->affected_rows > 0){ // ho sostituito prepare() con affected_rows
            $r = array("Success"=>"true","Message" => "Quantita' rimossa");
            echo json_encode($r);
        }else{
            $r = array("Success"=>"false","Message" => "quantità non rimossa");
            echo json_encode($r);
        }
        
	}else{
		$r = array("Success"=>"false","Message" => "Campi mancanti");
		echo json_encode($r);
	}
?>