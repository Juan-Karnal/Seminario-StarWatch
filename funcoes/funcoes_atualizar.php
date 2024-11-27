<?php
	//Esse arquivo contém funções referentes a atualização dos Conteúdos
	require_once "funcoes_banco.php";

	$data = array(
		'funcao' => isset($_POST['funcao']) ? $_POST['funcao'] : "",
		'titulo' => isset($_POST['titulo']) ? $_POST['titulo'] : "",
		'tipo' => isset($_POST['tipo']) ? $_POST['tipo'] : "",
		'anoLancamento' => isset($_POST['anoLancamento']) ? $_POST['anoLancamento'] : "",
		'genero' => isset($_POST['genero']) ? $_POST['genero'] : "",
		'sinopse' => isset($_POST['sinopse']) ? $_POST['sinopse'] : "",
		'opiniao' => isset($_POST['opiniao']) ? $_POST['opiniao'] : "",
		'pontuacao' => isset($_POST['pontuacao']) ? $_POST['pontuacao'] : "",
		'fotoCapa' => isset($_POST['fotoCapa']) ? $_POST['fotoCapa'] : "",
		'idConteudo' => isset($_POST['idConteudo']) ? $_POST['idConteudo'] : ""
	);

	switch ($data['funcao'])
	{
		case 'atualizaFilmeSerie':
		{
			$retorno = atualizarFilmeSerie($data['idConteudo'], $data['titulo'], $data['tipo'], $data['anoLancamento'], $data['genero'], $data['sinopse'], $data['opiniao'], $data['pontuacao'], $data['fotoCapa']);
			echo json_encode($retorno);
			break;
		}

		case 'excluiFilmeSerie':
		{
			$retorno = excluiFilmeSerie($data['idConteudo']);
			echo json_encode($retorno);
			break;
		}
	}

	//Faz o UPDATE dos Conteúdos separadamente.
	function atualizarFilmeSerie( $idConteudo, $titulo, $tipo, $anoLancamento, $genero, $sinopse, $opiniao, $pontuacao, $fotoCapa)
	{
		$genero = explode(",", $genero);

		$sql = 
			"UPDATE
				conteudos 
			SET 
				titulo = '" . escapeString($titulo) . "',
				tipo = '" . escapeString($tipo) . "',
				ano_lancamento = '" . escapeString($anoLancamento) . "',
				sinopse = '" . escapeString($sinopse) . "',
				opiniao = '" . escapeString($opiniao) . "',
				pontuacao = '" . escapeString($pontuacao) . "'
			WHERE 
				id = $idConteudo";

		$result = bd_atualiza($sql);


		//Atualização da relação dos conteúdos com os gêneros
		$sqlDeleteRelacaoConteudoGenero = "DELETE FROM conteudos_generos WHERE conteudo_id = $idConteudo";
		bd_delete($sqlDeleteRelacaoConteudoGenero);

		for($i = 0; $i < count($genero); $i++)
		{
			if(!empty($genero[$i]))
			{
				$sqlRelacaoConteudoGenero =
					"INSERT INTO
						conteudos_generos (conteudo_id, genero_id)
					VALUES (
						".escapeString($idConteudo).",
						".escapeString($genero[$i])."
					)";
				bd_atualiza($sqlRelacaoConteudoGenero);
			}
		}

		if(!empty($fotoCapa))
		{
			atualizaFoto($fotoCapa, $idConteudo);
		}

		return $result;
	}

	//Função que adiciona as imagens de capa dos Conteúdos.
	function atualizaFoto($foto, $idConteudo)
	{
		$base64 = explode(';base64,', $foto);
		$binario = base64_decode($base64[1]);
		$finfo = new \finfo(FILEINFO_MIME);
		$mimeType = $finfo->buffer($binario, FILEINFO_MIME_TYPE);

		$sql = "UPDATE conteudos 
				SET foto_capa_tipo = ?, foto_capa = ? 
				WHERE id = ?";

		bd_atualiza_imagem($sql, [$mimeType, $binario, $idConteudo], 'sbi');
	}

	//Função que faz a exclusão dos Conteúdos
	function excluiFilmeSerie($idConteudo)
	{
		$sqlDeleteConteudo = "DELETE FROM conteudos WHERE id = $idConteudo";
		$result = bd_delete($sqlDeleteConteudo);

		return $result;
	}
?>