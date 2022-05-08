<?php
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");

	//includiamo configurazioni e oggetti
	include_once 'db.php';

	//Connessione db
	$database = new Database();
	$db = $database->getConnection();

	
	$data = json_decode(file_get_contents("php://input"));
	if(!empty($data->codice) && !empty($data->quantita)){

        $query = "UPDATE prodotto SET quantita=quantita+'$data->quantita' WHERE id='$data->codice'";
		$q = $db->prepare($query);
        if($q->execute()){
            $r = array("Success"=>"true","Message" => "Quantita' aggiunta");
            echo json_encode($r);
        }else{
            $r = array("Success"=>"false","Message" => "quantità non aggiunta");
            echo json_encode($r);
        }

	}else{
		$r = array("Success"=>"false","Message" => "Campi mancanti");
		echo json_encode($r);
	}
?>