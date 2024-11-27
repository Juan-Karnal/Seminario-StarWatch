window.onload = function(){
	// Extrai o parâmetro 'id' da URL (usado para identificar o filme ou série a ser editado)
	const params = new URLSearchParams(window.location.search);
	const itemId = params.get('id');

	// Chama a função para buscar os detalhes do filme/série usando o ID obtido
	buscaFilmeSerie(itemId);

	// Adiciona um evento de clique ao botão "Escolher Arquivo", que simula um clique no input de arquivo "fotoCapa"
	document.getElementById('escolherArquivo').onclick = function() {
		document.getElementById('fotoCapa').click();
	};

	const fotoCapa = document.getElementById('fotoCapa');
	const fotoCapaCarregada = document.getElementById('fotoCapaCarregada');

	// Quando o usuário escolhe um arquivo (imagem) para upload
	fotoCapa.onchange = function () {
		var input = this;

		// Verifica se há um arquivo selecionado
		if(input.files && input.files[0])
		{
			var reader = new FileReader();

			// Quando o arquivo é carregado, define o conteúdo da imagem como o src do elemento "fotoCapaCarregada"
			reader.onload = function (e) {
				fotoCapaCarregada.setAttribute('src', e.target.result)
			}

			reader.readAsDataURL(input.files[0]);
		}
	};

	// Ao clicar no botão "Atualizar", chama a função para atualizar os detalhes do filme/série
	document.getElementById('btnAtualizar').onclick = function() {
		atualizaFilmeSerie(itemId);
	};

	// Ao clicar no botão "Excluir", ativa a exclusão do filme/série
	//A exclusão só ocorre quando clicado em OK na Modal com a mensagem de confirmação
	document.getElementById('btnExcluir').onclick = function() {
		document.getElementById("btnExcluirToggle").click();
		excluiFilmeSerie(itemId);
	};
};

// Função para buscar os detalhes de um filme ou série usando o ID
function buscaFilmeSerie(itemId)
{
	fetch('funcoes/funcoes_listar.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body: new URLSearchParams({
			'funcao': 'buscaDetalhesFilmeSerie',
			'idConteudo': itemId
		})
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Erro na rede');
		}
		return response.json();
	})
	.then(data => {
		montaFilmeSerie(data)
	})
	.catch(error => {
		console.error('Erro:', error);
	});
}

// Função para preencher o formulário com os detalhes do filme/série
function montaFilmeSerie(filmeSerie)
{
	// Preenche os campos de texto com os valores recebidos do backend
	document.getElementById('titulo').value = filmeSerie[0].titulo;
	document.getElementById('anoLancamento').value = filmeSerie[0].ano_lancamento;
	document.getElementById('sinopse').value = filmeSerie[0].sinopse;
	document.getElementById('opiniao').value = filmeSerie[0].opiniao;

	// Atualiza o rótulo de "filme" ou "série" com base no tipo
	const tipo = {id: filmeSerie[0].tipo}
	updateLabel(tipo);

	// Preenche o dropdown de gêneros com as opções recebidas
	const select = document.getElementById('genero');
	select.innerHTML = '<option disabled>Gêneros</option>';
	filmeSerie[0].generos.forEach(genero => {
		var option = document.createElement("option");
		option.value = genero.id;
		option.selected = genero.checked; // Marca o gênero se estiver selecionado
		option.textContent = genero.nome;
		select.appendChild(option);
	});

	// Atualiza o sistema de avaliação (pontuação) com estrelas
	const ratingDiv = document.querySelector('.rating');
	for (let i = 5; i >= 1; i--) {
		const input = ratingDiv.querySelector(`#star${i}`);
		input.name = 'rating_' + filmeSerie[0].id;
		input.checked = (i === filmeSerie[0].pontuacao);
	}

	// Carrega a imagem de capa se disponível
	const img = document.getElementById('fotoCapaCarregada');
	if(typeof(filmeSerie[0].imagem_base64) !== "undefined")
	{
		img.src = filmeSerie[0].imagem_base64;
	}
}

// Função para atualizar os detalhes de um filme ou série
function atualizaFilmeSerie(itemId)
{
	const genero = document.getElementById('genero');
	const generos = Array.from(genero.selectedOptions).map(option => option.value);

	const filmeSerie = document.querySelectorAll('[name=tipoFilmeSerie]')[0];
	let tipo = "";
	if(filmeSerie.checked)
	{
		tipo = "filme";
	}
	else
	{
		tipo = "serie";
	}

	fetch('funcoes/funcoes_atualizar.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body: new URLSearchParams({
			'funcao': 'atualizaFilmeSerie',
			'titulo': document.getElementById('titulo').value,
			'tipo': tipo,
			'anoLancamento': document.getElementById('anoLancamento').value,
			'genero': generos,
			'sinopse': document.getElementById('sinopse').value,
			'opiniao': document.getElementById('opiniao').value,
			'pontuacao': document.querySelector('.rating input[type="radio"]:checked').value,
			'fotoCapa': document.getElementById('fotoCapaCarregada').getAttribute('src'),
			'idConteudo': itemId
		})
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Erro na rede');
		}
		return response.json();
	})
	.then(data => {
		if(data.result)
			{
				alert("Atualizado com sucesso!");
				window.location.href = "listar.phtml";
			}
	})
	.catch(error => {
		console.error('Erro:', error);
	});
}

// Função para excluir um filme ou série
function excluiFilmeSerie(itemId)
{
	fetch('funcoes/funcoes_atualizar.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body: new URLSearchParams({
			'funcao': 'excluiFilmeSerie',
			'idConteudo': itemId
		})
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Erro na rede');
		}
		return response.json();
	})
	.then(data => {
		if(data)
		{
			alert("Excluído com sucesso!");
			window.location.href = "listar.phtml";
		}
	})
	.catch(error => {
		console.error('Erro:', error);
	});
}