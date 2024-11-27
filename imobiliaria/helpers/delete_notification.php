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
    $notificationId = $_POST['notification_id'];

    $stmt = $con->prepare("DELETE FROM notifications WHERE notification_id = ?");
    if ($stmt === false) {
        die('Erro na preparação da declaração SQL: ' . htmlspecialchars($con->error));
    }

    $stmt->bind_param("i", $notificationId);

    if ($stmt->execute()) {
        echo "Notificação excluída com sucesso.";
    } else {
        echo "Erro ao excluir notificação: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
}

mysqli_close($con);
?>