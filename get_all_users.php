<?php
 
/*
 * O seguinte codigo retorna para o usuario a lista de usuarios 
 * armazenados no servidor. Essa e uma requisicao do tipo GET. 
 * Nao sao necessarios nenhum tipo de parametro.
 * A resposta e no formato JSON.
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
 
// Realiza uma consulta ao BD e obtem todos as votacoes.
$result = pg_query($con, "SELECT *FROM usuarios");
 

if (pg_num_rows($result) > 0) {
    // Caso existam usuarios no BD, eles sao armazenados na 
	// chave "usuarios". O valor dessa chave e formado por um 
	// array onde cada elemento e um usuario.
    $response["usuario"] = array();
 
    while ($row = pg_fetch_array($result)) {
        // Para cada usuario, sao retornados somente o 
		// id (id do candidato)e o usuario_email(email do usuario). Nao ha necessidade 
		// de retornar nesse momento todos os campos de todos do usuario
		// pois a app cliente, inicialmente, so precisa do nome do mesmo para 
		// exibir na lista de usuarios. O campo id e usado pela app cliente 
		// para buscar os detalhes de um candidato especifico quando o usuario 
		// o seleciona. Esse tipo de estrategia poupa banda de rede, uma vez 
		// os detalhes de um candidato somente serao transferidos ao cliente 
		// em caso de real interesse.
        $usuario = array();
        $usuario["nome"] = $row["nome"];
        $usuario["email"] = $row["email"];

 
        // Adiciona o usuario no array de usuarios.
        array_push($response["usuario"], $usuario);
    }
    // Caso haja usuario no BD, o cliente 
	// recebe a chave "success" com valor 1.
    $response["success"] = 1;
	
	pg_close($con);
 
    // Converte a resposta para o formato JSON.
    echo json_encode($response);
	
} else {
    // Caso nao haja usuarios no BD, o cliente 
	// recebe a chave "success" com valor 0. A chave "message" indica o 
	// motivo da falha.
    $response["success"] = 0;
    $response["message"] = "Nao ha votacoes";
	
	// Fecha a conexao com o BD
	pg_close($con);
 
    // Converte a resposta para o formato JSON.
    echo json_encode($response);
}
?>
