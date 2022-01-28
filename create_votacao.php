<?php
 
/*
 * O seguinte codigo abre uma conexao com o BD e adiciona uma votação nele.
 * As informacoes de uma votação sao recebidas atraves de uma requisicao POST.
 */
 
// array que guarda a resposta da requisicao
$response = array();

// Abre uma conexao com o BD.
// DATABASE_URL e uma variavel de ambiente definida pelo Heroku, servico 
// utilizado para fazer o deploy dessa aplicacao web. Ela 
// contem a string de conexao necessaria para acessar o BD fornecido pelo 
// Heroku. Caso voce nao utilize o servico Heroku, voce deve alterar a 
// linha seguinte para realizar a conexao correta com o BD de sua escolha.
$con = pg_connect(getenv("DATABASE_URL"));

$username = NULL;
$password = NULL;
$isAuth = false;

// Método para mod_php (Apache)
if(isset( $_SERVER['PHP_AUTH_USER'])) {
    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];
} // Método para demais servers
elseif(isset( $_SERVER['HTTP_AUTHORIZATION'])) {
    if(preg_match( '/^basic/i', $_SERVER['HTTP_AUTHORIZATION']))
		list($username, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
}

// Se a autenticação não foi enviada
if(!is_null($username)){
    $query = pg_query($con, "SELECT senha FROM usuarios WHERE email='$username'");

	if(pg_num_rows($query) > 0){
		$row = pg_fetch_array($query);
		if($password == $row['senha']){
			$isAuth = true;
		}
	}
} //verifica se ta logado
 
if ($isAuth){
// Primeiro, verifica-se se todos os parametros foram enviados pelo cliente.
// A criacao de uma votacao precisa dos seguintes parametros:
// titulo - titulo da votacao
// descricao - descricao da votacao
// data_hora_criacao - data de inicio da votacao
// data_hora_fim - data de fim da votacao
	if (isset($_POST['data_hora_criacao']) && isset($_POST['data_hora_fim']) && isset($_POST['titulo']) && isset($_POST['descricao'])) {
	 

	 
		// Aqui sao obtidos os parametros
		$data_hora_criacao = date("Y-m-d H:i:s", strtotime($_POST['data_hora_criacao']));
		$data_hora_fim = date("Y-m-d H:i:s", strtotime($_POST['data_hora_fim']));
		$titulo = $_POST['titulo'];
		$datafim = $_POST['descricao'];

		
		// A proxima linha insere um novo produto no BD.
		// A variavel result indica se a insercao foi feita corretamente ou nao.
		$result = pg_query($con, "INSERT INTO votacao(data_hora_criacao, data_hora_fim, titulo, descricao) VALUES('$data_hora_criacao', '$data_hora_fim', '$titulo', '$descricao') RETURNING id");
	 
		
		if ($result) {
			// Se o produto foi inserido corretamente no servidor, o cliente 
			// recebe a chave "success" com valor 1
			$ultimoID = pg_fetch_array($result,0)[0];
			$response["success"] = 1;
			$response["message"] = "Produto criado com sucesso";
			$response["idvotacao"] = $ultimoID; //fornece o serial que foi criado para votação


		} else {
			// Se o produto nao foi inserido corretamente no servidor, o cliente 
			// recebe a chave "success" com valor 0. A chave "message" indica o 
			// motivo da falha.
			$response["success"] = 0;
			$response["message"] = "Erro ao criar produto no BD";
			
			
		}
	} else {
		// Se a requisicao foi feita incorretamente, ou seja, os parametros 
		// nao foram enviados corretamente para o servidor, o cliente 
		// recebe a chave "success" com valor 0. A chave "message" indica o 
		// motivo da falha.
		$response["success"] = 0;
		$response["message"] = "Campo requerido nao preenchido";
	}
}
else{
	$response["success"] = 0;
	$response["error"] = "falha de autenticação";
}
// Fecha a conexao com o BD
// Converte a resposta para o formato JSON.
pg_close($con);
echo json_encode($response);
?>
