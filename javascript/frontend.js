window.onload = function() {
	// Adiciona um evento de clique ao botão com o ID 'add'
	document.getElementById('add').onclick = function() {
		// Redireciona o usuário para a página 'adicionar.phtml'
		window.location.href = "adicionar.phtml";
	};

	// Adiciona um evento de clique ao botão com o ID 'list'
	document.getElementById('list').onclick = function() {
		// Redireciona o usuário para a página 'listar.phtml'
		window.location.href = "listar.phtml";
	};
};

// Função que atualiza a aparência dos rótulos (labels) associados aos botões de opção (radio buttons)
function updateLabel(radio) {
	// Seleciona todos os elementos 'label' dentro do grupo de botões
	const labels = document.querySelectorAll('.btn-group label');

	// Remove classes personalizadas de todos os labels para resetar seu estado visual
	labels.forEach(label => {
		label.classList.remove('btn-checked');
		label.classList.add('btn-outline-primary');
	});

	// Seleciona o label associado ao botão de opção (radio button) que foi selecionado
	const selectedLabels = document.querySelector(`label[for="${radio.id}"]`);

	// Atualiza a aparência do label selecionado para indicar que está marcado
	selectedLabels.classList.remove('btn-outline-primary');
	selectedLabels.classList.add('btn-checked');
}

// Função que atualiza a aparência dos rótulos (labels) associados aos checkboxes
function updateLabelCheckbox(checkbox) {
	// Seleciona o label associado ao checkbox que foi clicado
	const label = document.querySelector(`label[for="${checkbox.id}"]`);

	// Verifica se o checkbox está marcado
	if (checkbox.checked) {
		label.classList.add('btn-checked');
		label.classList.remove('btn-outline-primary');
	} else {
		label.classList.add('btn-outline-primary');
		label.classList.remove('btn-checked');
	}
}
