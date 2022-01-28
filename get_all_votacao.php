<?php
 
/*
 * O seguinte codigo retorna para o usuario a lista de votacoes 
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
$result = pg_query($con, "SELECT *FROM votacao");
 

if (pg_num_rows($result) > 0) {
    // Caso existam produtos no BD, eles sao armazenados na 
	// chave "votacoes". O valor dessa chave e formado por um 
	// array onde cada elemento e uma votacao.
    $response["votacao"] = array();
 
    while ($row = pg_fetch_array($result)) {
        // Para cada votacao, sao retornados somente o 
		// pid (id da votacao), o titulo da votacao e a descricao da votacao. Nao ha necessidade 
		// de retornar nesse momento todos os campos de todos as votacoes 
		// pois a app cliente, inicialmente, so precisa do nome do mesmo para 
		// exibir na lista de votacoes. O campo pid e usado pela app cliente 
		// para buscar os detalhes de uma votacao especifica quando o usuario 
		// o seleciona. Esse tipo de estrategia poupa banda de rede, uma vez 
		// os detalhes de um produto somente serao transferidos ao cliente 
		// em caso de real interesse.
        $votacao = array();
        $votacao["id"] = $row["id"];
        $votacao["titulo"] = $row["titulo"];
        $votacao["descricao"] = $row["descricao"];

 
        // Adiciona a votacao no array de votacoes.
        array_push($response["votacao"], $votacao);
    }
    // Caso haja produtos no BD, o cliente 
	// recebe a chave "success" com valor 1.
    $response["success"] = 1;
	
	pg_close($con);
 
    // Converte a resposta para o formato JSON.
    echo json_encode($response);
	
} else {
    // Caso nao haja produtos no BD, o cliente 
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
