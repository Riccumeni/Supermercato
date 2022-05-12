<?php
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");

	//includiamo configurazioni e oggetti
	include_once '../resources/db.php';

	//Connessione db
	$database = new Database();
	$db = $database->getConnection();

	
	$data = json_decode(file_get_contents("php://input"));
	if(!empty($data->codice) && !empty($data->prezzo)){

        $query = "UPDATE prodotto SET prezzo='$data->prezzo' WHERE id='$data->codice'";
		$q = $db->prepare($query);
        if($q->execute()){
            $r = array("Success"=>"true","Message" => "Prezzo modificato");
            echo json_encode($r);
        }else{
            $r = array("Success"=>"false","Message" => "Prezzo non modificato");
            echo json_encode($r);
        }

	}else{
		$r = array("Success"=>"false","Message" => "Campi mancanti");
		echo json_encode($r);
	}
?>