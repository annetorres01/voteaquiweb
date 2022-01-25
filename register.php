<?php
 
/*
 * Following code will create a new product row
 * All product details are read from HTTP Post Request
 */
 
// connecting to db
$con = pg_connect(getenv("DATABASE_URL"));
 
// array for JSON response
$response = array();
 
// check for required fields
if (isset($_POST['email']) && isset($_POST['senha']) && isset($_POST['telefone']) && isset($_POST['nome'])){
 
	$email = trim($_POST['email']);
	$senha = trim($_POST['senha']);
	$telefone = trim($_POST['telefone']);
	$nome = trim($_POST['nome']);
		
	$usuario_existe = pg_query($con, "SELECT email FROM usuarios WHERE email='$email'");
	// check for empty result
	if (pg_num_rows($usuario_existe) > 0) {
		$response["success"] = 0;
		$response["error"] = "usuario ja cadastrado";
	}
	else {
		// mysql inserting a new row
		$result = pg_query($con, "INSERT INTO usuarios(email, nome, telefone, senha ) VALUES('$email', '$nome', '$telefone', '$senha')");
	 
		if ($result) {
			$response["success"] = 1;
		}
		else {
			$response["success"] = 0;
			$response["error"] = "Error BD: ".pg_last_error($con);
		}
	}
}
else {
    $response["success"] = 0;
	$response["error"] = "faltam parametros";
}

pg_close($con);
echo json_encode($response);
?>
