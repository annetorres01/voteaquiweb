<?php
 
/*
 * O codigo seguinte retorna os dados detalhados de uma votacao.
 * Essa e uma requisicao do tipo GET. Uma votacao e identificado 
 * pelo campo id.
 */
 
// array que guarda a resposta da requisicao
$response = array();
 
// Verifica se o parametro pid foi enviado na requisicao
if (isset($_GET["id"])) {
	
	// Aqui sao obtidos os parametros
    $id = $_GET['id'];
	
	// Abre uma conexao com o BD.
	// DATABASE_URL e uma variavel de ambiente definida pelo Heroku, servico 
	// utilizado para fazer o deploy dessa aplicacao web. Ela 
	// contem a string de conexao necessaria para acessar o BD fornecido pelo 
	// Heroku. Caso voce nao utilize o servico Heroku, voce deve alterar a 
	// linha seguinte para realizar a conexao correta com o BD de sua escolha.
	$con = pg_connect(getenv("DATABASE_URL"));
 
    // Obtem do BD os detalhes da votacao com id especificado na requisicao GET
    $result = pg_query($con, "SELECT *FROM votacao WHERE id = $id");
 
    if (!empty($result)) {
        if (pg_num_rows($result) > 0) {
 
			// Se a votacao existe, os dados de detalhe da votacao 
			// sao adicionados no array de resposta.
            $result = pg_fetch_array($result);
 
            $votacao = array();
            $votacao["titulo"] = $result["titulo"];
            $votacao["descricao"] = $result["descricao"];
            
            // Caso a votacao exista no BD, o cliente 
			// recebe a chave "success" com valor 1.
            $response["success"] = 1;
 
            $response["votacao"] = array();
 
			// Converte a resposta para o formato JSON.
            array_push($response["votacao"], $votacao);
			
			// Fecha a conexao com o BD
			pg_close($con);
 
            // Converte a resposta para o formato JSON.
            echo json_encode($response);
        } else {
            // Caso a votacao nao exista no BD, o cliente 
			// recebe a chave "success" com valor 0. A chave "message" indica o 
			// motivo da falha.
            $response["success"] = 0;
            $response["message"] = "Votacao não encontrado";
			
			// Fecha a conexao com o BD
			pg_close($con);
 
            // Converte a resposta para o formato JSON.
            echo json_encode($response);
        }
    } else {
        // Caso a votacao nao exista no BD, o cliente 
		// recebe a chave "success" com valor 0. A chave "message" indica o 
		// motivo da falha.
        $response["success"] = 0;
        $response["message"] = "Votação não encontrado";
 
		// Fecha a conexao com o BD
		pg_close($con);
 
        // Converte a resposta para o formato JSON.
        echo json_encode($response);
    }
} else {
    // Se a requisicao foi feita incorretamente, ou seja, os parametros 
	// nao foram enviados corretamente para o servidor, o cliente 
	// recebe a chave "success" com valor 0. A chave "message" indica o 
	// motivo da falha.
    $response["success"] = 0;
    $response["message"] = "Campo requerido não preenchido";
 
    // Converte a resposta para o formato JSON.
    echo json_encode($response);
}
?>
