
<?php
// Iniciar a sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conectar ao banco de dados
$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];

    $stmt = $con->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    if ($stmt === false) {
        die('Erro na preparação da declaração SQL: ' . htmlspecialchars($con->error));
    }

    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo "Notificações marcadas como lidas.";
    } else {
        echo "Erro ao marcar notificações como lidas: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
}

mysqli_close($con);
?>