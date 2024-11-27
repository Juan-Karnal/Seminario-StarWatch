<?php
	//Esse arquivo contém funções referentes ao cadastro dos Conteúdos
	require_once "funcoes_banco.php";
	require_once "funcoes_atualizar.php";

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
	);

	switch ($data['funcao'])
	{
		case 'cadastrarFilmeSerie':
		{
			$retorno = cadastrarFilmeSerie($data['titulo'], $data['tipo'], $data['anoLancamento'], $data['genero'], $data['sinopse'], $data['opiniao'], $data['pontuacao'], $data['fotoCapa']);
			echo json_encode($retorno);
			break;
		}
	}

	//Faz o INSERT dos Conteúdos separadamente.
	function cadastrarFilmeSerie( $titulo, $tipo, $anoLancamento, $genero, $sinopse, $opiniao, $pontuacao, $fotoCapa)
	{
		$genero = explode(",", $genero);

		$sql = "INSERT INTO 
					conteudos (titulo, tipo, ano_lancamento, sinopse, opiniao, pontuacao)
				VALUES (
					'".escapeString($titulo)."',
					'".escapeString($tipo)."',
					'".escapeString($anoLancamento)."',
					'".escapeString($sinopse)."',
					'".escapeString($opiniao)."',
					'".escapeString($pontuacao)."'
				)";
		$result = bd_atualiza($sql);

		//Cadastro da relação dos conteúdos com os gêneros
		for($i = 0; $i < count($genero); $i++)
		{
			if(!empty($genero[$i]))
			{
				$sqlRelacaoConteudoGenero =
				"INSERT INTO
					conteudos_generos (conteudo_id, genero_id)
				VALUES (
					".escapeString($result['id']).",
					".escapeString($genero[$i])."
				)";
				bd_atualiza($sqlRelacaoConteudoGenero);
			}
		}

		if(!empty($fotoCapa))
		{
			atualizaFoto($fotoCapa, $result['id']);
		}

		return $result;
	}
?>