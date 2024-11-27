window.onload = function(){
	carregaGeneros();

	// Adiciona um evento de clique ao botão "Adicionar", que chama a função para cadastrar um novo filme ou série
	document.getElementById('btnAdicionar').onclick = function() {
		cadastraFilmeSerie()
	};

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
};

// Função que cadastra um novo filme ou série
function cadastraFilmeSerie()
{
	// Obtém os gêneros selecionados pelo usuário
	const genero = document.getElementById('genero');
	const generos = Array.from(genero.selectedOptions).map(option => option.value);
	
	// Verifica se uma foto diferente da imagem padrão foi adicionada
	let fotoAdicionada = "";
	if(document.getElementById('fotoCapaCarregada').getAttribute('src') != "img\\img-padrao.png")
	{
		fotoAdicionada = document.getElementById('fotoCapaCarregada').getAttribute('src');
	}

	// Verifica se o tipo selecionado é "filme" ou "série"
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

	// Envia os dados do formulário para o backend usando o método POST
	fetch('funcoes/funcoes_cadastrar.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body: new URLSearchParams({
			'funcao': 'cadastrarFilmeSerie',
			'titulo': document.getElementById('titulo').value,
			'tipo': tipo,
			'anoLancamento': document.getElementById('anoLancamento').value,
			'genero': generos,
			'sinopse': document.getElementById('sinopse').value,
			'opiniao': document.getElementById('opiniao').value,
			'pontuacao': document.querySelector('.rating input[type="radio"]:checked').value,
			'fotoCapa': fotoAdicionada
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
			alert("Inserido com sucesso!");
			window.location.href = "listar.phtml";
		}
	})
	.catch(error => {
		console.error('Erro:', error);
	});
}

// Função que carrega os gêneros disponíveis do backend
function carregaGeneros()
{
	fetch('funcoes/funcoes_listar.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body: new URLSearchParams({
			'funcao': 'buscaGeneros'
		})
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Erro na rede');
		}
		return response.json();
	})
	.then(data => {
		const select = document.getElementById('genero');
		select.innerHTML = '<option disabled>Gêneros</option>';
		data.forEach(genero => {
			var option = document.createElement("option");
			option.value = genero.id;
			option.textContent = genero.nome;
			select.appendChild(option);
		});
	})
	.catch(error => {
		console.error('Erro:', error);
	});
}