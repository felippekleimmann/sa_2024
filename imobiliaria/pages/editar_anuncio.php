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

// Obter o ID do anúncio
$announcement_id = isset($_GET['announcement_id']) ? intval($_GET['announcement_id']) : 0;

// Buscar dados do anúncio
$stmt = $con->prepare("SELECT * FROM announcement WHERE announcement_id = ?");
$stmt->bind_param("i", $announcement_id);
$stmt->execute();
$announcement = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $stmt = $con->prepare("UPDATE announcement SET title = ?, description = ?, price = ? WHERE announcement_id = ?");
    $stmt->bind_param("ssdi", $title, $description, $price, $announcement_id);
    $stmt->execute();
    header("Location: ?page=admin_anuncios&corretor_id=" . $announcement['user_id']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Anúncio</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <style>
        .container {
            width: 80%;
            margin: 0 auto;
            padding-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group textarea {
            height: 100px;
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
            border-color: #d39e00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Anúncio</h2>
        <form method="post">
            <div class="form-group">
                <label for="title">Título</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($announcement['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Preço</label>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($announcement['price']); ?>" step="0.01" required>
            </div>
            <button type="submit" name="update" class="btn-yellow">Atualizar</button>
        </form>
    </div>
</body>
</html>