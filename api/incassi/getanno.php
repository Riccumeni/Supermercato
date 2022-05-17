<?php
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");

	include_once '../resources/db.php';

	$database = new Database();    //connessione db
	$db = $database->getConnection();

	
	$info = json_decode(file_get_contents("php://input"));
	
	
	
	if(!empty($info->anno)){        // se ci sono i campi, fa la somma degli importi dove il mese e l'anno coincidono con quelli inseriti
		
		
		$sql = "SELECT SUM(valore) AS risultato FROM operazione WHERE YEAR(data) = '$info->anno'"; 
		$ris = $db->query($sql);
		if($ris -> num_rows >0){
			$ris = $ris -> fetch_assoc();
			
            
			
			//seconda query per visulazzare tutti gli importi
			$sql_2 = "SELECT * FROM operazione WHERE YEAR(data) = '$info->anno'"; 
			$ris_2 = $db->query($sql_2);
			
			$ordine = array();
			
			while ($row = $ris_2 -> fetch_assoc()){
				$ordini = array("success" => true , "ID: " => $row['id'] , "Nome: " => $row['valore'] ,"Codice Utente: " => $row['codice_utente'], "Data: " => $row['data']);
				array_push($ordine, $ordini);
			}
			$r = array("success"=>true,"ORDINI" => $ordine , "INCASSI" =>"Incassi annuali: $ris[risultato] ");
			echo json_encode($r);	
				
			}
		else{
			$r = array("success" => false, "Message" => "Non sono presenti operazioni per questo mese");
			echo json_encode($r);
		}
	}else{
		$r = array("success"=> false,"Message" => "Data mancante");
		echo json_encode($r);
	}
	
?>