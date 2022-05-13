<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"));
$email = $data->email;
$messaggio = $data->messaggio;
$oggetto = $data->oggetto;

if(!empty($email) && !empty($messaggio) && !empty($oggetto)){
    if(mail($email, $oggetto, $messaggio)){
        echo json_encode(["success" => true, "message" => "mail inviata"]);
    }else{
        echo json_encode(["success" => false, "message" => "mail non inviata"]);
    }
}else{
    echo json_encode(["success" => false, "message" => "errore con il db"]);
}
?>