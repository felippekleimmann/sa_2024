<?php
$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['form_id']) && $_POST['form_id'] === 'visitorRequestForm' && !isUserLoggedIn()) {
		$visitorName = $_POST['visitor_name'];
		$visitorEmail = $_POST['email'];
		$visitorMessage = $_POST['buscando'];

		$stmt = $con->prepare("INSERT INTO visitor_requests (name, email, message) VALUES (?, ?, ?)");
		if ($stmt === false) {
			die('Erro na preparação da declaração SQL: ' . htmlspecialchars($con->error));
		}

		$stmt->bind_param("sss", $visitorName, $visitorEmail, $visitorMessage);

		if ($stmt->execute()) {
			// Criar notificação para todos os corretores
			createNotifications($visitorName, $visitorEmail, $visitorMessage);
			$message = "Solicitação enviada com sucesso!";
		} else {
			$message = "Erro ao enviar solicitação: " . htmlspecialchars($stmt->error);
		}

		$stmt->close();
	} else {
		// Inicializar variáveis de filtro
		$category = $_POST['category'] ?? '';
		$location = $_POST['location'] ?? '';
		$code = $_POST['code'] ?? '';
		$name = $_POST['name'] ?? '';

		// Construir a consulta SQL com base nos filtros
		$query = "SELECT a.announcement_id
				FROM announcement a
				JOIN build b ON a.build_id = b.build_id
				LEFT JOIN announcement_photos ap ON a.announcement_id = ap.announcement_id
				WHERE 1=1"; // Adiciona uma condição sempre verdadeira para facilitar a concatenação das outras condições

		if (!empty($category)) {
			$query .= " AND b.build_type = '$category'";
		}

		if (!empty($location)) {
			$query .= " AND (b.address LIKE '%$location%' OR b.neighborhood LIKE '%$location%')";
		}

		if (!empty($code)) {
			$query .= " AND a.announcement_id = '$code'";
		}

		if (!empty($name)) {
			$query .= " AND a.title LIKE '%$name%'";
		}

		var_dump($query);
		$result = $con->query($query);
		$announcements = [];

		if ($result === false) {
			die("Erro na execução da consulta SQL: " . htmlspecialchars($con->error));
		}

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$announcements[] = $row;
			}
			// Redirecionar para a página do primeiro anúncio encontrado
			$firstAnnouncementId = $announcements[0]['announcement_id'];
			header("Location: ?page=imovel&announcement_id=$firstAnnouncementId");
			exit();
		} else {
			$message_error = "Não foi possível encontrar nenhum anúncio.";
		}
	}
}

// Função para criar notificações para todos os corretores
function createNotifications($name, $email, $message) {
    global $con;
    $stmt = $con->prepare("SELECT user_id FROM user WHERE user_type_id = 2");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $userId = $row['user_id'];
        $notificationMessage = "Nova solicitação de $name ($email): $message";

        $notificationStmt = $con->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notificationStmt->bind_param("is", $userId, $notificationMessage);
        $notificationStmt->execute();
        $notificationStmt->close();
    }

    $stmt->close();
}

$query = "SELECT a.announcement_id, a.title, a.description, a.price, b.address, b.info_rooms, b.info_area_total, b.info_parking_space, b.neighborhood, ap.photo
          FROM announcement a
          JOIN build b ON a.build_id = b.build_id
          LEFT JOIN announcement_photos ap ON a.announcement_id = ap.announcement_id  WHERE a.isHighlighted = TRUE;";

$result = $con->query($query);
$highlightedAnnouncements = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $highlightedAnnouncements[] = $row;
    }
} else {
    $message_error_to_highlightAnnouncements = "Não foi possível encontrar nenhum anúncio.";
}

?>

<div class="jss11">
	<div class="banner-section" style="background-image: url('assets/images/anage-inicial.webp');">
        <div class="container">
            <div class="block-centered">
                <h1 class="title title-1"><span>Nuhaus Imóveis:</span> a sua imobiliária em Joinville</h1>
                <div class="block-search">
                    <div class="blur"></div>
                    <div class="filters-section position-relative">
                        <div class="block-filters position-static">
<form method="post">
    <div class="form-row">
        <div class="col-md-4">
            <div class="form-group"><label for="code" class="label-control">Código do imóvel</label><input type="text" class="form-control" name="code"></div>
        </div>
        <div class="col-md-4">
            <div class="form-group"><label for="name" class="label-control">Nome do empreendimento</label><input type="text" class="form-control" name="name"></div>
        </div>
        <div class="col-md-4">
            <div class="form-group"><label for="searchForm-category" class="label-control">Tipo do Imóvel</label><select class="form-control custom-select" id="searchForm-category" name="category">
                <option value="">Selecione o tipo</option>
                <option value="22">Condomínio</option>
                <option value="3">Casa Comercial</option>
                <option value="61495c47e41c1635">Casa Residencial</option>
                <option value="1">Cobertura</option>
                <option value="23">Comercial</option>
                <option value="66759947f38d44232">Conjunto Comercial</option>
                <option value="14">Galpão</option>
                <option value="19">Garagem</option>
                <option value="17">Loja</option>
                <option value="9">Sala</option>
                <option value="8">Terreno</option>
            </select></div>
        </div>
        <div class="col-md-4">
            <div class="form-group"><label for="location" class="label-control">Localização</label><input type="text" class="form-control" name="location"></div>
        </div>
        <div class="col-md-4">
            <div class="block-submit"><button type="submit" class="btn btn-1">Buscar meu Imóvel</button></div>
        </div>
    </div>
	<?php if (!empty($message_error)): ?>
        <div class="alert alert-danger" style="margin-top: 10px;">
			<?php echo $message_error; ?>
        </div>
		<?php endif; ?>
	</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="featured-properties">
        <div class="container">
            <div class="block">
                <div class="MuiBox-root jss13">
                    <h2 class="title title-2">Imóveis em destaque</h2>
                </div>

                <div class="MuiBox-root jss18" axis="x" index="0">
                    <div role="tabpanel" id="full-width-tabpanel-0" aria-labelledby="full-width-tab-0" dir="ltr">
                        <div class="MuiBox-root jss19">
                            <p class="MuiTypography-root MuiTypography-body1">
                            <div class="swiper-container swiper-container-initialized swiper-container-horizontal swiper-container-pointer-events">
                                <div class="swiper-pagination swiper-pagination-bullets"><span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet"></span></div>
                                <div class="swiper-wrapper" id="swiper-wrapper-38e4f83ac606b393" aria-live="polite" style="transform: translate3d(0px, 0px, 0px);">
                                    <?php foreach ($highlightedAnnouncements as $announcement): ?>
                                        <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 9" style="width: 360px; margin-right: 30px;">
                                            <div class="card card-property">
                                                <a target="_blank" href="/sa_2024/imobiliaria/index.php?page=imovel&announcement_id=<?php echo $announcement['announcement_id']; ?>">
                                                    <div class="property-images">
                                                        <div class="swiper-container swiper-container-virtual swiper-container-initialized swiper-container-horizontal swiper-container-pointer-events">
                                                            <div class="swiper-wrapper" id="swiper-wrapper-1d68cf6563545868" aria-live="polite" style="transform: translate3d(0px, 0px, 0px);">
                                                                <div class="swiper-slide swiper-slide-active" data-swiper-slide-index="0" style="left: 0px; width: 358px;">
																<div class="image" style="background-image: url('data:image/jpeg;base64,<?php echo $announcement['photo']; ?>');"></div><span class="count">1/30</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                                                        <p class="card-text"><?php echo htmlspecialchars($announcement['description']); ?></p>
                                                        <p class="card-text">Preço: R$ <?php echo number_format($announcement['price'], 2, ',', '.'); ?></p>
                                                        <p class="card-text">Endereço: <?php echo htmlspecialchars($announcement['address']); ?></p>
                                                        <p class="card-text">Quartos: <?php echo htmlspecialchars($announcement['info_rooms']); ?></p>
                                                        <p class="card-text">Área: <?php echo htmlspecialchars($announcement['info_area_total']); ?> m²</p>
                                                        <p class="card-text">Vagas de garagem: <?php echo htmlspecialchars($announcement['info_parking_space']); ?></p>
                                                        <p class="card-text">Bairro: <?php echo htmlspecialchars($announcement['neighborhood']); ?></p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="info-ad">
        <div class="container">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="title title-2">Anuncie seu Imóvel em Joinville com a Nuhaus</h2>
                            <h3 class="title title-3">Aluguel e Venda</h3>
                            <div class="block">
                                <div class="item"><img alt="" src="/static/media/fast-forward.94d1fa14.svg" class="icon"><span class="item-info"><strong>Agilidade: </strong>negociação e assinatura do contrato online.</span></div>
                                <div class="item"><img alt="" src="/static/media/handshake.f27c5be9.svg" class="icon"><span class="item-info"><strong>Assertividade: </strong>imobiliária que mais vende e aluga imóveis em Joinville e região.</span></div>
                                <div class="item"><img alt="" src="/static/media/laptop.91bbcd74.svg" class="icon"><span class="item-info"><strong>Divulgação: </strong>seu imóvel é anunciado no site da Nuhaus e principais portais do país.</span></div>
                                <div class="item"><img alt="" src="/static/media/medal.9ac812c3.svg" class="icon"><span class="item-info"><strong>Segurança: </strong>processos certificados e mais 30 anos fazendo negócios imobiliários em Joinville com ética, transparência e credibilidade.</span></div>
                                <div class="item"><img alt="" src="/static/media/users.53638531.svg" class="icon"><span class="item-info"><strong>Atendimento completo: </strong>estrutura para dar todo o suporte do início ao fim da transação imobiliária.</span></div>
                            </div><a class="btn btn-1" href="?page=anuncie">Anunciar Imóvel</a>
                        </div>
                        <div class="col-md-6">
                            <div class="block-images"><img alt="" src="/static/media/anuncie-seu-imovel-anage.6d77619f.webp" class="img-fluid img-ad">
                                <div class="stamp"><img alt="" src="/static/media/anage-desde-1866.da79e667.webp" class="img-fluid img-stamp"><span class="info"><strong>Confiança: </strong>mais de 30 anos de experiência e inovações, somos a principal autoridade no mercado imobiliário de Joinville e região.</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="newsletter-section">
        <div class="container">
            <form method="post" class="form" id="visitorRequestForm">
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="info">
                            <p class="text text-1"><span class="title title-4">Pensando em vender ou comprar um imóvel? </span>Deixe seu e-mail e receba nossas dicas, novidades e promoções!</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-row">
								<input type="hidden" name="form_id" value="visitorRequestForm">
								<div class="col-12 col-md-3">
									<div class="form-group">
										<label for="visitor_name" class="label-control">Nome</label>
										<input name="visitor_name" type="text" class="form-control" id="visitor_name" value="" required>
									</div>
								</div>
								<div class="col-12 col-md-4 col-lg-3">
									<div class="form-group">
										<label for="newsletter-email" class="label-control">E-mail</label>
										<input name="email" type="email" class="form-control" id="newsletter-email" value="" required>
									</div>
								</div>
								<div class="col-12 col-md-2 col-lg-3">
									<div class="form-group">
										<label for="newsletter-buscando" class="label-control">O que você está buscando?</label>
										<select name="buscando" class="form-control custom-select" id="newsletter-buscando" required>
											<option value="">Selecione</option>
											<option value="Anunciar imóvel">Anunciar imóvel</option>
											<option value="Comprar imóvel">Comprar imóvel</option>
											<option value="Vender imóvel">Vender imóvel</option>
										</select>
									</div>
								</div>
								<div class="col-12 col-md-3">
									<button type="submit" name="button" class="btn btn-2 btn-lg">Enviar</button>
								</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-12">
                        <div class="custom-control custom-checkbox small-checkbox"><input type="checkbox" class="custom-control-input" name="acceptPolicy"><label class="custom-control-label">Ao preencher este formulário concordo com a coleta e tratamento dos meus dados, conforme <a target="_blank" href="/PolticadePrivacidadeAnage.pdf">Política de Privacidade</a>, nos termos da Lei 13.709/2018, permitindo desde já eventual armazenamento destes dados e o contato comercial da Nuhaus Imóveis</label></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>