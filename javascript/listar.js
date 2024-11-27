// Inicializa a variável para controlar a página atual, começando pela primeira página
let paginaAtual = 1;

window.onload = function() {
	// Busca filmes ou séries na página atual (inicialmente a página 1)
	buscaFilmesSeries(paginaAtual);
};

// Configura os eventos após o conteúdo do DOM ter sido totalmente carregado
document.addEventListener("DOMContentLoaded", (event) => {
	// Evento para o botão de busca
	document.getElementById('botaoBusca').addEventListener('click', function(event) {
		event.preventDefault();
		buscaFilmesSeries(paginaAtual);
	});

	// Evento para o botão de página anterior
	document.getElementById('prevButton').addEventListener('click', () => {
		// Verifica se a página atual é maior que 1 antes de retroceder
		if (paginaAtual > 1) {
			paginaAtual--; // Decrementa a página atual
			buscaFilmesSeries(paginaAtual); // Busca novamente os resultados para a nova página
		}
	});

	// Evento para o botão de próxima página
	document.getElementById('nextButton').addEventListener('click', () => {
		paginaAtual++; // Incrementa a página atual
		buscaFilmesSeries(paginaAtual); // Busca os resultados para a nova página
	});
});

// Função para buscar filmes ou séries no backend com base na página e filtros selecionados
function buscaFilmesSeries(pagina) {
	// Atualiza a variável 'paginaAtual' com a página passada como argumento
	paginaAtual = pagina;

	// Obtém os checkboxes que indicam o tipo (filme ou série)
	const checkboxes = document.querySelectorAll('[name=tipoFilmeSerie]');

	// Itera sobre cada checkbox para verificar se está marcado
	let valoresSelecionados = [];
	checkboxes.forEach(function(checkbox) {
		if (checkbox.checked) {
			valoresSelecionados.push("'" + checkbox.id + "'"); // Adiciona o ID do checkbox ao array
		}
	});

	const limite = 10; // Define o limite de itens por página

	// Envia uma requisição para o backend para buscar filmes/séries
	fetch('funcoes/funcoes_listar.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body: new URLSearchParams({
			'funcao': 'buscaFilmesSeries',
			'titulo': document.getElementById('buscaTitulo').value,
			'tipo': valoresSelecionados.join(', '),
			'pagina': pagina,
			'limite': limite
		})
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Erro na rede');
		}
		return response.json();
	})
	.then(data => {
		montaListaFilmesSeries(data.items); // Popula a lista com os dados retornados
		renderPaginationLinks(data.paginaAtual, data.totalPaginas); // Renderiza os links de paginação
	})
	.catch(error => {
		console.error('Erro:', error);
	});
}

// Função para montar a lista de filmes/séries na interface
function montaListaFilmesSeries(filmesSeries) {
	const container = document.querySelector('.row'); // Seleciona o contêiner onde os cards serão inseridos
	const modeloCard = document.getElementById('modeloCard'); // Modelo de card a ser clonado

	container.innerHTML = ''; // Limpa o contêiner antes de adicionar novos itens

	filmesSeries.forEach(item => {
		const cardClone = modeloCard.cloneNode(true); // Clona o modelo de card
		cardClone.classList.remove('d-none'); // Remove a classe que oculta o modelo

		// Configura a imagem do card
		const img = cardClone.querySelector('.fotoCapa');
		if (typeof(item.imagem_base64) !== "undefined") {
			img.src = item.imagem_base64; // Usa a imagem do backend, se disponível
		} else {
			img.src = "img\\img-padrao.png"; // Usa uma imagem padrão se não houver imagem disponível
		}

		// Configura o título e ID do card
		const titulo = cardClone.querySelector('.titulo');
		titulo.id = item.id;
		titulo.textContent = item.titulo;

		// Configura o ano de lançamento
		const anoLancamento = cardClone.querySelector('.anoLancamento');
		anoLancamento.textContent = item.anoLancamento;

		// Exibe os gêneros como "badges" (se houver gêneros)
		if (item.generosLista !== null) {
			const arrayGeneros = item.generosLista.split(", ");
			const generosDiv = cardClone.querySelector('.generos');
			generosDiv.innerHTML = '';

			arrayGeneros.forEach(genero => {
				const badge = document.createElement('span');
				badge.classList.add('badge', 'text-bg-primary', 'me-1'); // Estilos de badge
				badge.textContent = genero;
				generosDiv.appendChild(badge);
			});
		}

		// Configura o sistema de avaliação (pontuação) com estrelas
		const ratingDiv = cardClone.querySelector('.rating-listados');
		for (let i = 5; i >= 1; i--) {
			const input = ratingDiv.querySelector(`#star${i}`);
			input.name = 'rating_' + item.id;
			input.checked = (i === item.pontuacao); // Marca a estrela correspondente à pontuação
		}

		// Adiciona o card clonado ao contêiner
		container.appendChild(cardClone);
	});

	// Adiciona evento de clique para cada título (redireciona para a página de edição)
	const items = document.querySelectorAll('.titulo');
	items.forEach(item => {
		item.addEventListener('click', () => {
			const itemId = item.getAttribute('id');
			window.location.href = `atualizar.phtml?id=${itemId}`; // Redireciona para a página de atualização
		});
	});
}

// Função para renderizar os links de paginação
function renderPaginationLinks(pagina, totalPaginas) {
	// Desativa o botão "Anterior" se estiver na primeira página
	document.getElementById('prevButton').classList.toggle('disabled', pagina === 1);
	// Desativa o botão "Próximo" se estiver na última página
	document.getElementById('nextButton').classList.toggle('disabled', pagina === totalPaginas);

	const pagination = document.querySelector('.pagination');

	// Remove itens de página antiga, mantendo apenas o primeiro e o último botão (anterior/próximo)
	const pageItems = pagination.querySelectorAll('.page-item:not(:first-child):not(:last-child)');
	pageItems.forEach(item => item.remove());

	// Cria os links de paginação dinamicamente
	for (let i = 1; i <= totalPaginas; i++) {
		const li = document.createElement('li');
		li.className = `page-item ${i === pagina ? 'active' : ''}`;

		const link = document.createElement('a');
		link.className = 'page-link';
		link.href = '#';
		link.textContent = i;

		// Evento de clique para cada link de página
		link.addEventListener('click', (e) => {
			e.preventDefault();
			buscaFilmesSeries(i); // Busca os resultados para a página clicada
		});

		li.appendChild(link);
		pagination.insertBefore(li, document.getElementById('proximo'));
	}
}