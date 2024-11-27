<?php
$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    $errorMessage = "Conexão falhou: " . mysqli_connect_error();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    // Inserir dados na tabela visitor_requests
    $stmt = $con->prepare("INSERT INTO visitor_requests (name, email, phone, detailed_message) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        $errorMessage = "Erro na preparação da declaração SQL: " . htmlspecialchars($con->error);
    } else {
        $stmt->bind_param("ssss", $name, $email, $phone, $message);
        if ($stmt->execute()) {
            $request_id = $stmt->insert_id;
        } else {
            $errorMessage = "Erro ao inserir solicitação de visitante: " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    }

    // Enviar notificação para o corretor selecionado
    if (!isset($errorMessage)) {
		$stmt = $con->prepare("SELECT user_id FROM user WHERE user_type_id = 2");
		$stmt->execute();
		$result = $stmt->get_result();

		while ($row = $result->fetch_assoc()) {
			$userId = $row['user_id'];
			$notificationMessage = "Nova solicitação pelo formulário de ANÚNCIO: $name - ($email)";
			$notificationStmt = $con->prepare("INSERT INTO notifications (user_id, message, detailed_message) VALUES (?, ?, ?)");
			if ($notificationStmt === false) {
            	$errorMessage = "Erro na preparação da declaração SQL para notificação: " . htmlspecialchars($con->error);
			} else {
				$notificationStmt->bind_param("iss", $userId, $notificationMessage, $message);
				if ($notificationStmt->execute()) {
					$successMessage = "Notificação enviada para o corretor.";
				} else {
					$errorMessage = "Erro ao enviar notificação: " . htmlspecialchars($notificationStmt->error);
				}
				$notificationStmt->close();
			}
		}

		$stmt->close();
	}

    if (!isset($errorMessage)) {
        $successMessage = "Solicitação enviada com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anuncie seu imóvel</title>
    <style>
        .toaster {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
        }

        .toaster.success {
            background-color: #4CAF50;
        }

        .toaster.error {
            background-color: #f44336;
        }

        .toaster.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @-webkit-keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }

        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }

        @-webkit-keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }

        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }
    </style>
</head>
<body>
<div class="advertise-property" id="quero-anunciar">
    <div class="page-header" style="background-image: url('assets/images/anuncie-topo.webp');"></div>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="description">
                                <h1 class="title title-1">Anunciar imóvel em Joinville</h1>
                                <div class="info">
                                    <p class="text text-1">Proprietário, anuncie seu imóvel - para vender - com a Nuhaus. Aqui, nós te ajudamos a fechar o melhor negócio! Preencha o formulário ao lado com seus dados pessoais e os dados do seu imóvel. Entraremos em contato com você!</p>
                                </div>
                                <h2 class="title title-2">Como funciona</h2>
                                <div class="steps">
                                    <div class="item">
                                        <div class="step">1</div>
                                        <p class="text text-1">Preencha o formulário com seus dados e os dados do imóvel.</p>
                                    </div>
                                    <div class="item">
                                        <div class="step">2</div>
                                        <p class="text text-1">Nossos especialistas entrarão em contato em breve para mais informações e detalhes do imóvel.</p>
                                    </div>
                                    <div class="item">
                                        <div class="step">3</div>
                                        <p class="text text-1">Seu imóvel é incluído nos nossos sistemas e publicado no site da Nuhaus e nos principais portais imobiliários do país.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="block-form">
                                <form method="POST" class="form">
                                    <h3 class="title title-3">Faça o pré-cadastro do seu imóvel</h3>
                                    <h4 class="title title-4">Dados Pessoais</h4>
                                    <div class="form-group">
										<label for="contactName" class="label-control">Nome</label>
										<input name="name" type="text" class="form-control" id="contactName" placeholder="Digite seu nome" value="" required>
									</div>
									<div class="form-row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="contactEmail" class="label-control">E-mail</label>
												<input name="email" type="email" class="form-control" id="contactEmail" placeholder="exemplo@email.com.br" value="" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="contactPhone" class="label-control">DDD + Telefone/WhatsApp</label>
												<input name="phone" type="text" class="form-control" id="contactPhone" placeholder="(00) 00000-00000" value="" required>
											</div>
										</div>
									</div>
                                    <h4 class="title title-4">Conte-nos mais sobre o seu imóvel</h4>
									<div class="form-group">
                                    <label for="contactMessage" class="label-control">Especificações</label>
										<textarea name="message" class="form-control" id="contactMessage" placeholder="Especifíque seu imóvel aqui" required></textarea>
									</div>
									<div class="custom-control custom-checkbox small-checkbox">
										<input type="checkbox" class="custom-control-input" id="acceptPolicy" name="acceptPolicy" required>
										<label class="custom-control-label" for="acceptPolicy">
											Ao preencher este formulário concordo com a coleta e tratamento dos meus dados, conforme <a target="_blank" href="/PolticadePrivacidadeAnage.pdf">Política de Privacidade</a>, nos termos da Lei 13.709/2018, permitindo desde já eventual armazenamento destes dados e o contato comercial da Nuhaus Imóveis
										</label>
									</div>
									<button type="submit" name="button" class="btn btn-1">Enviar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="reasons">
        <div class="container">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block-title">
                                <h3 class="title title-2">Razões para escolher a <b>Nuhaus Imóveis</b></h3>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="item"><img alt="" src="/static/media/icon-velocidade.12226335.svg" class="icon">
                                <p class="text text-1"><span>Velocidade: </span>a imobiliária que mais aluga e vende em Joinville</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="item"><img alt="" src="/static/media/icon-divulgacao.34c189c9.svg" class="icon">
                                <p class="text text-1"><span>Divulgação: </span>no site da Nuhaus e nos principais portais do país com fotos profissionais</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="item"><img alt="" src="/static/media/icon-agilidade.d6d66961.svg" class="icon">
                                <p class="text text-1"><span>Agilidade: </span>negociação e assinatura do contrato online</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="item"><img alt="" src="/static/media/icon-seguranca.cbf6b1ad.svg" class="icon">
                                <p class="text text-1"><span>Segurança: </span>processos certificados e mais de 30 anos fazendo negócios imobiliários em Joinville</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="item"><img alt="" src="/static/media/icon-atendimento-2.21de3c8e.svg" class="icon">
                                <p class="text text-1"><span>Atendimento personalizado: </span>pessoalmente nas nossas lojas ou de forma digital</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="item"><img alt="" src="/static/media/icon-certificacao.97227d83.svg" class="icon">
                                <p class="text text-1"><span>Certificação ISO 9001: </span>o selo comprova o compromisso e a eficácia da Política de Qualidade Nuhaus Imóveis</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="action"><a href="#quero-anunciar" class="btn btn-1">Quero Anunciar</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toaster" class="toaster"></div>

	<script>
		function showToaster(message, type) {
			var toaster = document.getElementById("toaster");
			toaster.className = "toaster " + type;
			toaster.innerText = message;
			toaster.classList.add("show");
			setTimeout(function(){ toaster.classList.remove("show"); }, 3000);
		}

		<?php if (isset($successMessage)): ?>
			showToaster("<?php echo $successMessage; ?>", "success");
		<?php endif; ?>

		<?php if (isset($errorMessage)): ?>
			showToaster("<?php echo $errorMessage; ?>", "error");
		<?php endif; ?>
	</script>
</body>
</html>