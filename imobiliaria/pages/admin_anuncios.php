<?php
require __DIR__ . '/../helpers/corretorCRUD.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] != 1) {
    header("Location: login.php");
    exit;
}

$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Obter o ID do corretor
$corretor_id = isset($_GET['corretor_id']) ? intval($_GET['corretor_id']) : 0;

// Buscar os anúncios do corretor
$announcements = $con->query("SELECT * FROM announcement WHERE user_id = $corretor_id");

// Processar remoção de anúncio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $announcement_id = intval($_POST['announcement_id']);
    $stmt = $con->prepare("DELETE FROM announcement WHERE announcement_id = ?");
    $stmt->bind_param("i", $announcement_id);
    $stmt->execute();
    header("Location: admin_anuncios.php?corretor_id=$corretor_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Anúncios</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <style>
        .container {
            width: 80%;
            margin: 0 auto;
            padding-top: 20px;
        }
        .announcement-card {
            display: flex;
            align-items: center;
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .announcement-card img {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            margin-right: 10px;
        }
        .announcement-card .info {
            flex-grow: 1;
        }
        .announcement-card .actions {
            display: flex;
            gap: 10px;
        }
        .announcement-card .actions button {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .announcement-card .actions button:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Anúncios do Corretor</h2>
        <?php while ($announcement = $announcements->fetch_assoc()): ?>
            <?php
            $announcement_id = $announcement['announcement_id'];

            // Buscar fotos do anúncio
            $photoResult = $con->query("SELECT photo FROM announcement_photos WHERE announcement_id = $announcement_id");
            $photos = [];
            while ($photoRow = $photoResult->fetch_assoc()) {
                $photos[] = $photoRow['photo'];
            }

            $photo_base64 = !empty($photos) ? trim($photos[0]) : ''; // Pegar a primeira foto
            ?>
            <div class="announcement-card">
                <div class="image" style="background-image: url('data:image/jpeg;base64,<?php echo htmlspecialchars($photo_base64); ?>'); width: 100px; height: 100px; background-size: cover; border-radius: 8px; margin-right: 10px;"></div>
                <div class="info">
                    <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                    <p><?php echo htmlspecialchars($announcement['description']); ?></p>
                    <p>Preço: R$ <?php echo number_format($announcement['price'], 2, ',', '.'); ?></p>
                </div>
                <div class="actions">
                    <form method="post">
                        <input type="hidden" name="announcement_id" value="<?php echo $announcement['announcement_id']; ?>">
                        <button type="submit" name="delete">Excluir</button>
                    </form>
                    <a style="border-radius: 4px; padding: 5px 10px; background: #007bff; color: #fff; text-decoration: none;" href="?page=editar_anuncio&announcement_id=<?php echo $announcement['announcement_id']; ?>">Editar</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>