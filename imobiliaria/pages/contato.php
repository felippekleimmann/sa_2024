<?php
$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    $errorMessage = "Conexão falhou: " . mysqli_connect_error();
}

// Buscar todos os corretores
$corretores = $con->query("SELECT user_id, username, photo FROM user WHERE user_type_id = 2");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];
    $corretor_id = $_POST['corretor_id'];

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
    if (!isset($errorMessage) && !empty($corretor_id)) {
        $notificationMessage = "Nova solicitação pelo formulário de COMPRA: $name - ($email)";
        $notificationStmt = $con->prepare("INSERT INTO notifications (user_id, message, detailed_message) VALUES (?, ?, ?)");
        if ($notificationStmt === false) {
            $errorMessage = "Erro na preparação da declaração SQL para notificação: " . htmlspecialchars($con->error);
        } else {
            $notificationStmt->bind_param("iss", $corretor_id, $notificationMessage, $message);
            if ($notificationStmt->execute()) {
                $successMessage = "Notificação enviada para o corretor.";
            } else {
                $errorMessage = "Erro ao enviar notificação: " . htmlspecialchars($notificationStmt->error);
            }
            $notificationStmt->close();
        }
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
    <title>Contato</title>
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
    <div class="contact-section">
        <div class="page-header large" style="background-image: url('assets/images/topo-fale-conosco.webp');">
            <div class="description">
                <div class="container">
                    <div class="row">
                        <div class="col-md-5 offset-md-1">
                            <h1 class="title title-1">Entre em contato com a Nuhaus Imóveis, sua imobiliária em Joinville</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="form-content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="title title-2">Quero <b>Comprar</b> com a Nuhaus</h2>
                            <p class="text text-1">Precisa falar com um de nossos corretores com intuito de compra? Preencha o formulário de contato com a Nuhaus com seus dados e envie sua mensagem.</p>
                            <form method="POST" class="form" id="form-anage-contato">
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
                                <div class="form-group">
                                    <label for="corretorSelector" class="label-control">Selecionar Corretor</label>
                                    <select name="corretor_id" class="form-control custom-select" id="corretorSelector" required>
                                        <option value="">Selecione um corretor</option>
                                        <?php while ($corretor = $corretores->fetch_assoc()): ?>
                                            <option value="<?php echo $corretor['user_id']; ?>" data-photo="<?php echo htmlspecialchars($corretor['photo']); ?>">
                                                <?php echo htmlspecialchars($corretor['username']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Corretor Selecionado</label>
                                    <div id="selectedCorretor" style="display: flex; align-items: center;">
                                        <img id="corretorPhoto" src="#" alt="Foto do Corretor" style="width: 50px; height: 50px; border-radius: 50%; display: none; margin-right: 10px;">
                                        <span id="corretorName"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="contactMessage" class="label-control">Mensagem</label>
                                    <textarea name="message" class="form-control" id="contactMessage" placeholder="Mensagem" required></textarea>
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
                        <div class="col-md-5 offset-md-1">
                            <h2 class="title title-2">A Nuhaus Imóveis</h2>
                            <p class="text text-1">Conheça a nossa história, diferenciais, serviços e saiba como nos tornamos a imobiliária referência em Joinville e Região.</p>
							<a class="btn btn-3" href="?page=sobre">Conheça Nossa História</a>
                            <a class="btn btn-3" href="?page=servicos">Nossos Serviços</a>
                            <a class="btn btn-3" href="?page=imoveis">Imóveis à Venda</a>
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

        document.getElementById('corretorSelector').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.textContent === 'Selecione um corretor') {
                document.getElementById('corretorPhoto').style.display = 'none';
                document.getElementById('corretorName').textContent = '';
                return;
            }
            const photo = selectedOption.getAttribute('data-photo');
            const name = selectedOption.textContent;

            const corretorPhoto = document.getElementById('corretorPhoto');
            const corretorName = document.getElementById('corretorName');

            if (photo) {
                corretorPhoto.src = 'data:image/jpeg;base64,' + photo;
                corretorPhoto.style.display = 'block';
            } else {
                corretorPhoto.style.display = 'none';
            }

            corretorName.textContent = name;
        });

        <?php if (isset($successMessage)): ?>
            showToaster("<?php echo $successMessage; ?>", "success");
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            showToaster("<?php echo $errorMessage; ?>", "error");
        <?php endif; ?>
    </script>
</body>
</html>