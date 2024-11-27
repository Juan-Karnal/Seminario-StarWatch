<?php
	//Função que faz conexão com o banco de dados
	function bd_conecta()
	{
		$host = 'localhost';		// Host do banco de dados
		$user = 'root';				// Usuário do banco
		$password = '';				// Senha do banco
		$database = 'star-watch';	// Nome do banco

		$conn = new mysqli($host, $user, $password, $database);

		// Verifica se há erro na conexão
		if ($conn->connect_error) {
			die("Falha na conexão: " . $conn->connect_error);
		}

		return $conn;
	}

	//Escapa caracteres especiais em uma string para uso em uma instrução SQL
	//@param $parametro Parâmetro que será escapado
	function escapeString($parametro)
	{
		$conn = bd_conecta();
		$parametro = mysqli_real_escape_string($conn, $parametro);

		return $parametro;
	}

	//Função padrão para fazer SELECT nas tabelas do banco
	//@param string $query Comando SQL para fazer o SELECT
	//@return array $data Valores retornados da consulta
	function bd_consulta($query)
	{
		$conn = bd_conecta();
		$stmt = $conn->prepare($query);

		$stmt->execute();
		$result = $stmt->get_result();

		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		$stmt->close();
		$conn->close();

		return $data;
	}

	//Função padrão para fazer UPDATE e INSERT nas tabelas do banco
	//@param string $query Comando SQL para fazer o UPDATE
	//@return array $success Retorna true se o UPDATE foi feito com sucesso ou false se não
	function bd_atualiza($query)
	{
		$conn = bd_conecta();
		$stmt = $conn->prepare($query);

		$success['result'] = $stmt->execute();
		$success['id'] = $conn->insert_id;

		$stmt->close();
		$conn->close();

		return $success;
	}

	//Função padrão para fazer inserção das imagens no banco
	//@param string $query Comando SQL para fazer a inserção
	//@return array $success Retorna true se a inserção foi feito com sucesso ou false se não
	function bd_atualiza_imagem($query, $params, $types)
	{
		$conn = bd_conecta();
		$stmt = $conn->prepare($query);
		
		if ($stmt === false) {
			die(json_encode(['error' => $conn->error]));
		}
		
		// Adiciona os parâmetros dinamicamente
		$stmt->bind_param($types, ...$params);
		
		// Se o segundo parâmetro é um binário (BLOB)
		if (strpos($types, 'b') !== false) {
			$stmt->send_long_data(1, $params[1]);
		}
		
		$success = $stmt->execute();

		if (!$success) {
			die(json_encode(['error' => $stmt->error]));
		}
		
		$stmt->close();
		$conn->close();

		return $success;
	}

	//Função padrão para fazer DELETE nas tabelas do banco
	//@param string $query Comando SQL para fazer o DELETE
	//@return array $success Retorna true se o DELETE foi feito com sucesso ou false se não
	function bd_delete($query)
	{
		$conn = bd_conecta();
		$stmt = $conn->prepare($query);

		$success = $stmt->execute();

		$stmt->close();
		$conn->close();

		return $success;
	}

?>
