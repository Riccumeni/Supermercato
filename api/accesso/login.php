<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$server = "localhost";
$username = "root";
$pass = "";
$db = "Supermercato";

$data = json_decode(file_get_contents("php://input"));
$email = $data->email;
$password = $data->password;

$conn = new mysqli($server, $username, $pass, $db);

if($conn && (!empty($email) && !empty($password))){
    
    $sql = "select id, email, password from utente where email like '$email' and permessi like 'u'";

    try{
        $result = $conn->query($sql);
    }catch(Exception $e){
        $e->getMessage();
    }

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        if(password_verify($password, $row["password"])){
            echo json_encode(array("success" => true, "message" => "Utente loggato correttamente", "id" => $row['id']));
        }else{
            echo json_encode(array("success" => false, "message" => "Credenziali non corrette"));
        }
    }else{
        echo json_encode(array("success" => false, "message" => "Utente non trovato o permessi non sufficiente"));
    }

}else{
    echo json_encode(array("success" => false, "message" => "Errore con il database o campi mancanti"));
}

$conn->close();
?>