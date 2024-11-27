<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>StarWatch</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link href="css\style.css" rel="stylesheet">
		<script src="javascript\frontend.js"></script>
	</head>
	<body>
		<nav class="navbar navbar-expand-lg bg-navbar">
			<div class="container-fluid">
				<a class="navbar-brand" href="index.php"><img src="img\claquete.png" width="40" height="40"></a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
					<div class="navbar-nav">
						<a class="nav-link active" href="index.php">Home</a>
						<a class="nav-link" href="adicionar.phtml">Adicionar Filmes e Séries</a>
						<a class="nav-link" href="listar.phtml">Meus Filmes e Séries</a>
					</div>
				</div>
			</div>
		</nav>

		<div class="container col-xxl-8 px-4 py-5">
			<div class="row flex-lg-row-reverse align-items-center g-5 py-5">
				<div class="col-10 col-sm-8 col-lg-6">
					<img src="img\img-tela-inicial.jpg" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" width="700" height="500" loading="lazy">
				</div>
				<div class="col-lg-6">
					<p class="display-5 fw-bold text-body-emphasis lh-1 mb-3">StarWatch</p>
					<p class="lead starwatch">Aqui na StarWatch você pode documentar todas as produções que já assistiu, organizar seus favoritos e dar notas para cada título. Com a StarWatch, acompanhar a sua trajetória cinematográfica nunca foi tão simples! Explore o mundo do cinema e das séries do seu jeito, basta clicar em Adicionar para iniciar sua jornada!</p>
					<div class="d-grid gap-2 d-md-flex justify-content-md-start">
					<button type="button" id="add" class="btn btn-primary btn-lg px-4 me-md-2">Adicionar</button>
					<button type="button" id="list" class="btn btn-primary btn-lg px-4 me-md-2">Listar</button>
					</div>
				</div>
			</div>
		</div>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	</body>
</html>