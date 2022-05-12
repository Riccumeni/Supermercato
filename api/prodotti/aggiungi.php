<?php
	header("Content-Type: application/json; charset=utf-8");
	header("Access-Control-Allow-Methods: POST");

	//includiamo configurazioni e oggetti
	include_once '../resources/db.php';

	//Connessione db
	$database = new Database();
	// $db = $database->getConnection();
	$db = new mysqli("localhost", "root", "", "Supermercato");

	$data = json_decode(file_get_contents("php://input"));

	if((!empty($data->codice) && !empty($data->quantita))){

		// aggiunto l'aggiunta di un prodotto
		
		$query = "UPDATE prodotto SET quantita=quantita+'$data->quantita' WHERE id='$data->codice'";
		$result = $db->query($query);

		if($result->affected_rows > 0){
			$r = array("Success"=>"true","Message" => "Quantita' aggiunta");
			echo json_encode($r);
		}else{
			$r = array("Success"=>"true","Message" => "Quantita' non aggiunta");
			echo json_encode($r);
		}
		
		// Codice originale Ferrone
        // $query = "UPDATE prodotto SET quantita=quantita+'$data->quantita' WHERE id='$data->codice'";
		// $q = $db->prepare($query);
        // if($q->execute()){
        //     $r = array("Success"=>"true","Message" => "Quantita' aggiunta");
        //     echo json_encode($r);
        // }else{
        //     $r = array("Success"=>"false","Message" => "quantità non aggiunta");
        //     echo json_encode($r);
        // }

	}else if((!empty($data->nome) && !empty($data->quantita) && !empty($data->categoria) && !empty($data->prezzo) && !empty($data->nome_fornitore))){
		$result = $db -> query("select * from prodotto where nome like '$data->nome'");
		if($result->num_rows > 0){
			echo json_encode(["success" => false, "message" => "prodotto già presente"]);
		}else{
			$query = "insert into prodotto (nome, quantita, categoria, prezzo, nome_fornitore) values ('$data->nome', '$data->quantita', '$data->categoria', '$data->prezzo', '$data->nome_fornitore')";
			try{
				$result = $db->query($query);
				echo json_encode(["success" => true, "message" => "operazione riuscita"]);
			}catch(Exception $e){
				echo json_encode(["success" => false, "message" => "errore con l'inserimento"]);
			}
		}
		
	}
	else{
		$r = array("Success"=>"false","Message" => "Campi mancanti");
		echo json_encode($r);
	}
?>