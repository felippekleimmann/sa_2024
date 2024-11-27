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
    $sql = "SELECT a.title, a.description, a.price, b.address, b.info_rooms, b.build_type, b.condominium_price, b.iptu_price, b.info_area_total, b.info_parking_space, b.bairro, a.user_id
            FROM announcement a
            JOIN build b ON a.build_id = b.build_id
            WHERE a.announcement_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $announcement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $announcement = $result->fetch_assoc();

    // Buscar fotos do anúncio
    $photoResult = $con->query("SELECT photo FROM announcement_photos WHERE announcement_id = $announcement_id");
    $photos = [];
    while ($photoRow = $photoResult->fetch_assoc()) {
        $photos[] = $photoRow['photo'];
    }

    // Buscar informações do corretor
    $user_id = $announcement['user_id'];
    $corretorResult = $con->query("SELECT phone FROM user WHERE user_id = $user_id");
    $corretor = $corretorResult->fetch_assoc();
    $corretor_phone = $corretor['phone'];
} else {
    die("ID do anúncio inválido.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Imóvel</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-4otLYLO4J5jZn1PksHJ5a2dc8U9DPg0VJzQt5MZs8Y2irhf7lD0+8D0+RZWynVzQf0CR9U2H5zD0m1tIoX9ZbA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-utbXc5a0tU9WJ+6A4w1ZgExZ8uL9VtR2V11V5o5U3IzB9pVVYF4o7yL3pY9Z2uJw3t5i8iEMfb8yJX4z5T9Xog==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
            body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f6f9;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 1200px;
        margin: 40px auto;
        background: #fff;
        padding: 20px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    .property-carousel .item {
        height: 500px;
        background-size: cover;
        background-position: center;
        border-radius: 10px;
    }
    .property-details {
        margin-top: 20px;
    }
    .property-details .title {
        font-size: 2.5em;
        font-weight: bold;
        margin-bottom: 15px;
        color: #333;
    }
    .property-details .location {
        font-size: 1.5em;
        margin-bottom: 10px;
        color: #666;
    }
    .property-details .price {
        font-size: 2em;
        font-weight: bold;
        margin-bottom: 20px;
        color: #27ae60;
    }
    .property-details .description {
        font-size: 1.2em;
        margin-bottom: 20px;
        line-height: 1.6;
        color: #555;
    }
    .property-highlight {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .property-highlight div {
        text-align: center;
        font-size: 1.2em;
        color: #333;
    }
    .property-highlight i {
        margin-right: 8px;
    }
    .btn-primary {
        background-color: #ffc107;
        color: #fff;
        border: none;
        padding: 15px 30px;
        font-size: 1.2em;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #e0a800;
    }

		.btn-whatsapp {
        background-color: #25d366; /* Cor do WhatsApp */
        color: #fff;
        border: none;
        padding: 15px 30px;
        font-size: 1.2em;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-whatsapp i {
        margin-right: 10px;
    }

    .btn-whatsapp:hover {
        background-color: #1da851;
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="property-carousel owl-carousel">
			<?php if(!empty($photos) && strlen($photos[0]) > 100): ?>
            <?php foreach ($photos as $photo): ?>
                <div class="item" style="background-image: url('data:image/jpeg;base64,<?php echo $photo; ?>');"></div>
            <?php endforeach; ?>
			<?php else: ?>
				<div class="item" style="background-image: url('assets/images/image-placeholder.png');"></div>
			<?php endif; ?>
        </div>
        <div class="property-details">
            <div class="title"><?php echo htmlspecialchars($announcement['title']); ?></div>
            <div class="location"><?php echo htmlspecialchars($announcement['bairro']); ?> - Joinville</div>
            <div class="price">R$ <?php echo number_format($announcement['price'], 2, ',', '.'); ?></div>
            <div class="description"><?php echo nl2br(htmlspecialchars($announcement['description'])); ?></div>
            <div class="property-highlight">
				<div><i class="fa-solid fa-bed"></i> <?php echo htmlspecialchars($announcement['info_rooms']); ?> quartos</div>
				<div><i class="fa-regular fa-square"></i> <?php echo htmlspecialchars($announcement['info_area_total']); ?>m²</div>
				<div><i class="fa-solid fa-car"></i> <?php echo htmlspecialchars($announcement['info_parking_space']); ?> vaga</div>
				<?php if (!empty($announcement['condominium_price'])): ?>
					<div><i class="fa-solid fa-building"></i> R$ <?php echo number_format($announcement['condominium_price'], 2, ',', '.'); ?> condomínio</div>
				<?php endif; ?>
				<?php if (!empty($announcement['iptu_price'])): ?>
					<div><i class="fa-solid fa-file-invoice-dollar"></i> R$ <?php echo number_format($announcement['iptu_price'], 2, ',', '.'); ?> IPTU</div>
				<?php endif; ?>
				<?php if (!empty($announcement['build_type'])): ?>
					<div><i class="fa-solid fa-home"></i> <?php echo htmlspecialchars($announcement['build_type']); ?></div>
				<?php endif; ?>
			</div>
			<button class="btn-whatsapp" onclick="window.open('https://wa.me/<?php echo $corretor_phone; ?>', '_blank')">
				<i class="fas fa-phone"></i> Contatar Corretor
			</button>
        </div>
    </div>

    <!-- Adicione os scripts do Owl Carousel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-pUgqg4L7P4z6WQ+u5wS5D5KbQayZ1r3v5Kk4A+KK6L5Q5p5z6c5mQ5r4T6n5z4w5Q6J5y6p1k4k6l3o6p5m5eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function(){
            $(".property-carousel").owlCarousel({
                items: 1,
                loop: true,
                nav: true,
                dots: true,
                autoplay: true,
                autoplayTimeout: 5000,
                navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>']
            });
        });
    </script>
</body>
</html>