<?php
	//Esse arquivo contém funções referentes a listagem e busca dos Conteúdos
	require_once "funcoes_banco.php";

	$data = array(
		'funcao' => isset($_POST['funcao']) ? $_POST['funcao'] : "",
		'titulo' => isset($_POST['titulo']) ? $_POST['titulo'] : "",
		'tipo' => isset($_POST['tipo']) ? $_POST['tipo'] : "",
		'pagina' => isset($_POST['pagina']) ? $_POST['pagina'] : "",
		'limite' => isset($_POST['limite']) ? $_POST['limite'] : "",
		'idConteudo' => isset($_POST['idConteudo']) ? $_POST['idConteudo'] : ""
	);

	switch ($data['funcao'])
	{
		case 'buscaFilmesSeries':
		{
			if(empty($data['tipo']))
			{
				$data['tipo'] = "'filme', 'serie'";
			}

			$filmesSeries = buscaFilmesSeries($data['titulo'], $data['tipo'], $data['pagina'], $data['limite']);

			echo json_encode($filmesSeries);
			break;
		}
		
		case 'buscaGeneros':
		{
			$generos = buscaGeneros();

			echo json_encode($generos);
			break;
		}

		case 'buscaDetalhesFilmeSerie':
		{
			$detalhesFilmeSerie = buscaDetalhesFilmeSerie($data['idConteudo']);
			echo json_encode($detalhesFilmeSerie);
		}
	}

	//Busca os Conteúdos pelo Título e Tipo (Filme ou Série)
	function buscaFilmesSeries($titulo = "", $tipo, $pagina, $limite)
	{
		//Se não for enviado o título, busca todos os conteúdo, levando em consideração apenas o tipo escolhido
		//Se não for selecionado um tipo, são buscados todos os conteúdos, filmes e séries
		if(empty($titulo))
		{
			$sqlBuscaTitulos =
				"SELECT 
					conteudos.id AS conteudo_id,
					conteudos.*,
					GROUP_CONCAT(generos.nome SEPARATOR ', ') AS generosLista
				FROM 
					conteudos
				LEFT JOIN 
					conteudos_generos ON conteudos.id = conteudos_generos.conteudo_id
				LEFT JOIN 
					generos ON conteudos_generos.genero_id = generos.id
				WHERE
					conteudos.tipo IN ($tipo)
				GROUP BY 
					conteudos.id
				ORDER BY 
					conteudos.id";
		}
		else
		{
			$titulo = escapeString($titulo);
			$sqlBuscaTitulos = 
				"SELECT 
					conteudos.id AS conteudo_id,
					conteudos.*,
					GROUP_CONCAT(generos.nome SEPARATOR ', ') AS generosLista
				FROM 
					conteudos
				LEFT JOIN 
					conteudos_generos ON conteudos.id = conteudos_generos.conteudo_id
				LEFT JOIN 
					generos ON conteudos_generos.genero_id = generos.id
				WHERE
					conteudos.titulo LIKE '%$titulo%' AND
					conteudos.tipo IN ($tipo)
				GROUP BY 
					conteudos.id
				ORDER BY 
					conteudos.id";
		}

		$filmesSeries = bd_consulta($sqlBuscaTitulos);

		//Ajusta imagem recebida do banco pra carregar na tela
		for($i = 0; $i < count($filmesSeries); $i++)
		{
			if (isset($filmesSeries[$i]['foto_capa']) && !empty($filmesSeries[$i]['foto_capa'])) {
				$filmesSeries[$i]['imagem_base64'] = 'data:' . $filmesSeries[$i]['foto_capa_tipo'] . ';base64,' . base64_encode($filmesSeries[$i]['foto_capa']);
				unset($filmesSeries[$i]['foto_capa']);
			}
		}

		//Detalhes da paginação
		$pagina = isset($pagina) ? (int)$pagina : 1;
		$limite = isset($limite) ? (int)$limite : 2;

		$start = ($pagina - 1) * $limite;
		$itensPaginados = array_slice($filmesSeries, $start, $limite);

		$totalPaginas = ceil(count($filmesSeries) / $limite);

		$filmesSeries['items'] = $itensPaginados;
		$filmesSeries['totalPaginas'] = $totalPaginas;
		$filmesSeries['paginaAtual'] = $pagina;

		return empty($filmesSeries) ? array() : $filmesSeries;
	}

	//Busca todos os gêneros existentes no banco para ser listado na página de UPDATE e INSERT
	function buscaGeneros()
	{
		$sql = "SELECT * FROM generos";
		$generos = bd_consulta($sql);

		return $generos;
	}

	//Busca os detalhes de um Conteúdo em específico quando selecionado para UPDATE baseado em seu ID
	function buscaDetalhesFilmeSerie($idConteudo)
	{
		$sqlBuscaTitulos =
			"SELECT
				conteudos.*,
				 GROUP_CONCAT(generos.id SEPARATOR ', ') AS generosSelecionados
			FROM 
				conteudos
			LEFT JOIN 
				conteudos_generos ON conteudos.id = conteudos_generos.conteudo_id
			LEFT JOIN 
				generos ON conteudos_generos.genero_id = generos.id
			WHERE
				conteudos.id = $idConteudo
			GROUP BY 
				conteudos.id
			ORDER BY 
				conteudos.id";

		$filmeSerie = bd_consulta($sqlBuscaTitulos);

		if(isset($filmeSerie[0]['foto_capa']) && !empty($filmeSerie[0]['foto_capa']))
		{
			$filmeSerie[0]['imagem_base64'] = 'data:' . $filmeSerie[0]['foto_capa_tipo'] . ';base64,' . base64_encode($filmeSerie[0]['foto_capa']);
			unset($filmeSerie[0]['foto_capa']);
		}

		$generosSelecionados = explode(', ', $filmeSerie[0]['generosSelecionados']);
		$filmeSerie[0]['generos'] = buscaGeneros();

		foreach($filmeSerie[0]['generos'] as &$genero)
		{
			$genero['checked'] = in_array($genero['id'], $generosSelecionados);
		}

		return empty($filmeSerie) ? array() : $filmeSerie;
	}