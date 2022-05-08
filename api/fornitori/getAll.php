<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

$server = "localhost";
$username = "root";
$password = "";
$db = "Supermercato";

$conn = new mysqli($server, $username, $password, $db);

if($conn){
    $sql = "select * from fornitore";

    $result = $conn->query($sql);

    if($result->num_rows>0){
        $fornitori = array();
        while($row = $result->fetch_assoc()){
            $fornitore = array("nome" => $row["nome"], "email" => $row["email"], "indirizzo" => $row["indirizzo"]);
            array_push($fornitori, $fornitore);
        }

        $response = array("success" => true, "data" => $fornitori);

        echo json_encode($response);
    }else{
        echo json_encode(array("success" => false, "message" => "Non esiste alcun fornitore"));
    }
}else{
    echo json_encode(array("success" => false, "message" => "Errore con il database"));
}
?>