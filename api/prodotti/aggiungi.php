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
		try{
			$result = $db->query($query);

			if($result->affected_rows > 0){
				$r = array("Success"=>"true","Message" => "Quantita' aggiunta");
				echo json_encode($r);
			}else{
				$r = array("Success"=>"true","Message" => "Quantita' non aggiunta");
				echo json_encode($r);
			}
		}catch(Exception $e){
			echo $e->getMessage();
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
			$db->begin_transaction();
			// todo: inserire sia il prodotto che inserire nelle ordinazioni
			try{
				$query = "insert into prodotto (nome, quantita, categoria, prezzo, nome_fornitore) values ('$data->nome', '$data->quantita', '$data->categoria', '$data->prezzo', '$data->nome_fornitore')";
				
				$db->query($query);

				$valore = $data->prezzo * $data->$quantita;
				$data_oggi = date("Y-m-d");
				$ordine = json_encode($data);
				$query = "insert into operazione (valore, nome_fornitore, data, ordine) values ('$valore', '$data->nome_fornitore', '$data_oggi', '$ordine')";

				$db->query($query);
				
				echo json_encode(["success" => true, "message" => "operazione riuscita"]);

				$conn->commit();
			}catch(Exception $exception) {
				echo $exception->getMessage();
				echo json_encode(["success" => false, "message" => "errore con l'inserimento"]);
				$db->rollback();
				throw $exception;
			}
		}
	}
	else{
		$r = array("Success"=>"false","Message" => "Campi mancanti");
		echo json_encode($r);
	}
?>