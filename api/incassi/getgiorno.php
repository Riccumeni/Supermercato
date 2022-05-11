<?php
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");

	include_once 'db.php';

	$database = new Database();    //connessione db
	$db = $database->getConnection();

	
	$info = json_decode(file_get_contents("php://input"));
	
	
	
	if(!empty($info->mese) && !empty($info->anno) && !empty($info->giorno) ){        // se ci sono i campi, fa la somma degli importi dove il mese e l'anno coincidono con quelli inseriti
		
		
		$sql = "SELECT SUM(valore) AS risultato FROM operazione WHERE MONTH(data) = '$info->mese' AND YEAR(data) = '$info->anno' AND DAY(data) = '$info->giorno'"; 
		$ris = $db->query($sql);
		if($ris -> num_rows >0){
			$ris = $ris -> fetch_assoc();
			
			//seconda query per visulazzare tutti gli importi
			$sql_2 = "SELECT * FROM operazione WHERE MONTH(data) = '$info->mese' AND YEAR(data) = '$info->anno' AND DAY(data) = '$info->giorno'"; 
			$ris_2 = $db->query($sql_2);
			
			$ordine = array();
			while ($row = $ris_2 -> fetch_assoc()){
				$ordini = array("ID: " => $row['id'] , "Nome: " => $row['valore'] ,"Codice Utente: " => $row['codice_utente'], "Data: " => $row['data']);
				array_push ($ordine,$ordini);
				echo "\n";
			}
			$r = array("Success" => "true" ,"ORDINI" => $ordine, "INCASSI" => "Incassi giornalieri: $ris[risultato] ");
            echo json_encode($r);
			
			
				
				
			}
		else{
			$r = array("Success" => "false", "Message" => "Non sono presenti operazioni per questo mese");
			echo json_encode($r);
		}
	}else{
		$r = array("Success"=>"false","Message" => "Data mancante");
		echo json_encode($r);
	}
	
?>