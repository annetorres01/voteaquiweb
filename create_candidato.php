<?php
 
/*
 * O seguinte codigo abre uma conexao com o BD e adiciona os candidatos em uma votação criada.
 * As informacoes de um candidato sao recebidas atraves de uma requisicao POST.
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

// Primeiro, verifica-se se todos os parametros foram enviados pelo cliente.
// A criacao de um cabdidato precisa dos seguintes parametros:
// usuario_email - email do candidato

	if (isset($_POST['usuario_email']) && isset ($_POST['votacao_id'])){
	 
		// Aqui sao obtidos os parametros
		$usuario_email = $_POST['usuario_email'];
		$votacao_id = $_POST['votacao_id'];
		
		
		// A proxima linha insere um novo candidato no BD referente a votação criada.
		// A variavel result indica se a insercao foi feita corretamente ou nao.
		$result = pg_query($con, "INSERT INTO votacao_candidato(usuario_email, votacao_id) VALUES('$usuario_email', '$votacao_id');
		
	 
		
		if ($result) {
			// Se o candiato foi inserido corretamente no servidor, o cliente 
			// recebe a chave "success" com valor 1
			$ultimoID = pg_fetch_array($result,0)[0];
			$response["success"] = 1;
			$response["message"] = "candidato criado com sucesso";
			$response["id"] = $ultimoID; //fornece o serial que foi criado para o candidato


		} else {
			// Se o candiato nao foi inserido corretamente no servidor, o cliente 
			// recebe a chave "success" com valor 0. A chave "message" indica o 
			// motivo da falha.
			$response["success"] = 0;
			$response["message"] = "Erro ao criar candidato no BD";
			
			
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
