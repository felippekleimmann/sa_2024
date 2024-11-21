<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Imóvel</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <style>
        .alert {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .btn-yellow {
            background-color: #ffc107;
            color: #212529;
            border: 1px solid #ffc107;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-yellow:hover {
            background-color: #e0a800;
            border-color: #e0a800;
        }
        body {
            overflow: hidden; /* Remove barras de rolagem */
        }
        .content {
            overflow: hidden; /* Remove barras de rolagem da área de conteúdo */
        }
    </style>
</head>
<body>
<?php
require __DIR__ . '/../helpers/corretorCRUD.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conectar ao banco de dados
$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Capturar o ID do anúncio da URL
$announcement_id = isset($_GET['announcement_id']) ? intval($_GET['announcement_id']) : 0;

if ($announcement_id > 0) {
    // Consultar o banco de dados para obter os detalhes do anúncio
    $sql = "SELECT a.title, a.description, a.price, b.address, b.info_rooms, b.info_area_total, b.info_parking_space, b.neighborhood
            FROM announcement a
            JOIN build b ON a.build_id = b.build_id
            WHERE a.announcement_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $announcement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $announcement = $result->fetch_assoc();

    if ($announcement) {
        // Buscar fotos do anúncio
        $photoResult = $con->query("SELECT photo FROM announcement_photos WHERE announcement_id = $announcement_id");
        $photos = [];
        while ($photoRow = $photoResult->fetch_assoc()) {
            $photos[] = $photoRow['photo'];
        }
        $photo_base64 = !empty($photos) ? trim($photos[0]) : ''; // Pegar a primeira foto
    } else {
        die("Anúncio não encontrado.");
    }
} else {
    die("ID do anúncio inválido.");
}
?>

<div class="propertie-item-inside property-content">
    <div class="imovel-carousel owl-carousel">
        <?php foreach ($photos as $photo): ?>
            <div class="item" style="background-image: url('data:image/jpeg;base64,<?php echo htmlspecialchars($photo); ?>');"></div>
        <?php endforeach; ?>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <div class="description">
                    <div class="details">
                        <div class="row">
                            <div class="col-md-8">
                                <h1 class="title title-1 name"><?php echo htmlspecialchars($announcement['title']); ?>
                                    <div class="title title-3 location"><?php echo htmlspecialchars($announcement['neighborhood']); ?> | Joinville - SC</div>
                                </h1>
                            </div>
                            <div class="col-md-4">
                                <p class="text text-1">Código: <?php echo htmlspecialchars($announcement_id); ?></p>
                            </div>
                            <div class="col-md-12">
                                <p class="text text-1"><?php echo htmlspecialchars($announcement['address']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="highlights">
                        <div class="highlight"><img alt="" src="https://www.anageimoveis.com.br/static/media/icon-quartos.3b0f6d9a.svg" class="icon"><span class="text text-1"><?php echo htmlspecialchars($announcement['info_rooms']); ?> quartos</span></div>
                        <div class="highlight"><img alt="" src="https://www.anageimoveis.com.br/static/media/icon-vagas.8bd618da.svg" class="icon"><span class="text text-1"><?php echo htmlspecialchars($announcement['info_parking_space']); ?> vagas de garagem</span></div>
                        <div class="highlight"><img alt="" src="https://www.anageimoveis.com.br/static/media/icon-area-2.ca40a20b.svg" class="icon"><span class="text text-1">Área total: <?php echo htmlspecialchars($announcement['info_area_total']); ?> m²</span></div>
                        <div class="highlight"><img alt="" src="https://www.anageimoveis.com.br/static/media/icon-area-2.ca40a20b.svg" class="icon"><span class="text text-1">Área privativa: <?php echo htmlspecialchars($announcement['info_area_total']); ?> m²</span></div>
                    </div>
                    <div class="show-mobile">
                        <div class="sidebar">
                            <div class="block-info">
                                <p class="text property-type">Imóvel para vender</p>
                                <p class="title title-2"><span class="price">R$&nbsp;<?php echo number_format($announcement['price'], 2, ',', '.'); ?></span></p>
                                <div class="row">
                                    <div class="col-7">
                                        <p class="text text-1">Condomínio:</p>
                                    </div>
                                    <div class="col-5">
                                        <p class="text text-1 text-right">R$&nbsp;0,00</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-7">
                                        <p class="text text-1">IPTU:</p>
                                    </div>
                                    <div class="col-5">
                                        <p class="text text-1 text-right">R$&nbsp;154,85</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-7">
                                        <p class="text text-1"><span>Total:</span></p>
                                    </div>
                                    <div class="col-5">
                                        <p class="text text-1 text-right"><span>R$&nbsp;2.744,85</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="block-actions"><a href="https://web.whatsapp.com/send?phone=+5547996531009" target="_blank" class="btn new-whatsapp"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">
                                        <path d="M436.5 74.4c-47.9-48-111.6-74.4-179.5-74.4-139.8 0-253.6 113.8-253.6 253.7 0 44.7 11.7 88.4 33.9 126.8l-36 131.5 134.5-35.3c37.1 20.2 78.8 30.9 121.2 30.9h0.1c0 0 0 0 0 0 139.8 0 253.7-113.8 253.7-253.7 0-67.8-26.4-131.5-74.3-179.5zM257.1 464.8v0c-37.9 0-75-10.2-107.4-29.4l-7.7-4.6-79.8 20.9 21.3-77.8-5-8c-21.2-33.5-32.3-72.3-32.3-112.2 0-116.3 94.6-210.9 211-210.9 56.3 0 109.3 22 149.1 61.8 39.8 39.9 61.7 92.8 61.7 149.2-0.1 116.4-94.7 211-210.9 211zM372.7 306.8c-6.3-3.2-37.5-18.5-43.3-20.6s-10-3.2-14.3 3.2c-4.2 6.3-16.4 20.6-20.1 24.9-3.7 4.2-7.4 4.8-13.7 1.6s-26.8-9.9-51-31.5c-18.8-16.8-31.6-37.6-35.3-43.9s-0.4-9.8 2.8-12.9c2.9-2.8 6.3-7.4 9.5-11.1s4.2-6.3 6.3-10.6c2.1-4.2 1.1-7.9-0.5-11.1s-14.3-34.4-19.5-47.1c-5.1-12.4-10.4-10.7-14.3-10.9-3.7-0.2-7.9-0.2-12.1-0.2s-11.1 1.6-16.9 7.9c-5.8 6.3-22.2 21.7-22.2 52.9s22.7 61.3 25.9 65.6c3.2 4.2 44.7 68.3 108.3 95.7 15.1 6.5 26.9 10.4 36.1 13.4 15.2 4.8 29 4.1 39.9 2.5 12.2-1.8 37.5-15.3 42.8-30.1s5.3-27.5 3.7-30.1c-1.5-2.8-5.7-4.4-12.1-7.6z"></path></svg>Fale pelo WhatsApp</a>
										<a class="MuiTypography-root MuiLink-root MuiLink-underlineHover btn new-visit MuiTypography-colorPrimary" href="https://anage.bitrix24.site/prevenda/agendar_visita/?CodIm=09124.001" target="_blank"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="539" height="512" viewBox="0 0 539 512">
                                            <path d="M432.716 182.16v270.101h-326.633v-270.101h-37.688v288.942c0 10.41 8.436 18.844 18.844 18.844h364.318c10.415 0 18.844-8.429 18.844-18.844v-288.942h-37.686z"></path>
                                            <path d="M282.002 4.787c-7.141-6.381-17.945-6.381-25.093 0l-256.909 229.269 25.094 28.121 244.358-218.076 244.37 218.077 25.088-28.123-256.908-229.269z"></path>
                                        </svg>Agendar visita</a>
										<div class="block-actions-secundary"><button type="button" class="new-link-phone"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">
											<path d="M352 320c-32 32-32 64-64 64s-64-32-96-64-64-64-64-96 32-32 64-64-64-128-96-128-96 96-96 96c0 64 65.75 193.75 128 256s192 128 256 128c0 0 96-64 96-96s-96-128-128-96z"></path>
										</svg>Telefone das lojas</button><button type="button" class="new-link-more">Quero saber mais</button></div>
									</div>
								</div>
                        </div>
						<div class="shared-block">
                                <div class="shared"><span class="text text-6">Compartilhar Imóvel</span>
                                    <ul class="list">
                                        <li><a href="#"><img alt="" src="https://www.anageimoveis.com.br/static/media/whatsapp-black.c06983c1.svg" class="icon-whatsapp"></a></li>
                                        <li><a href="#"><img alt="" src="https://www.anageimoveis.com.br/static/media/mail.29ceaa6c.svg" class="icon"></a></li>
                                    </ul>
                                </div>
                                <p class="text text-7">* Os valores de condomínio e taxas são aproximados. Além do aluguel, será de responsabilidade do locatário o pagamento do IPTU, condomínio, TLU, seguro contra incêndio, e demais encargos estabelecidos no contrato de locação, conforme artigos 22 e 23 da lei 8.245/91.”</p>
                            </div>
                </div>
                <div class="info">
                    <h4 class="title title-3">Sobre esse imóvel</h4>
                    <div class="block-text">
                        <div><?php echo nl2br(htmlspecialchars($announcement['description'])); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 offset-md-1 hidden-mobile">
                <div class="sidebar">
                    <div class="block-info">
                        <p class="text property-type">Imóvel para vender</p>
                        <p class="title title-2"><span class="price">R$&nbsp;<?php echo number_format($announcement['price'], 2, ',', '.'); ?></span></p>
                        <div class="row">
                            <div class="col-7">
                                <p class="text text-1">Condomínio:</p>
                            </div>
                            <div class="col-5">
                                <p class="text text-1 text-right">R$&nbsp;0,00</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <p class="text text-1">IPTU:</p>
                            </div>
                            <div class="col-5">
                                <p class="text text-1 text-right">R$&nbsp;154,85</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-7">
							<p class="text text-1"><span>Total:</span></p>
                            </div>
                            <div class="col-5">
                                <p class="text text-1 text-right"><span>R$&nbsp;2.744,85</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="block-actions"><a href="https://web.whatsapp.com/send?phone=+5547996531009" target="_blank" class="btn new-whatsapp"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">
                                <path d="M436.5 74.4c-47.9-48-111.6-74.4-179.5-74.4-139.8 0-253.6 113.8-253.6 253.7 0 44.7"></path></svg>Fale pelo WhatsApp</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var owl = $('.propertie-item-inside .imovel-carousel');
        owl.owlCarousel({
            items: 1,
            loop: true,
            margin: 0,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true
        });
    });
</script>
</body>
</html>
<?php
$stmt->close();
$con->close();
?>
