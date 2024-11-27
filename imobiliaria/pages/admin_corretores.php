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

// Buscar todos os corretores
$corretores = $con->query("SELECT user_id, username, photo FROM user WHERE user_type_id = 2");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Corretores</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <style>
        .container {
            width: 80%;
            margin: 0 auto;
            padding-top: 20px;
        }
        .corretor-card {
            display: flex;
            align-items: center;
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .corretor-card img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .corretor-card a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .corretor-card a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gerenciamento de Corretores</h2>
        <?php while ($corretor = $corretores->fetch_assoc()): ?>
            <div class="corretor-card">
				<?php if(!empty($corretor['photo']) && strlen($corretor['photo']) > 100): ?>
                <img src="data:image/jpeg;base64,<?php echo $corretor['photo']; ?>" alt="Foto do Corretor">
				<?php else: ?>
					<img src="assets/images/default-avatar.png" alt="Foto do usuário" class="user-photo">
				<?php endif; ?>
                <a href="?page=admin_anuncios&corretor_id=<?php echo $corretor['user_id']; ?>"><?php echo htmlspecialchars($corretor['username']); ?></a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>