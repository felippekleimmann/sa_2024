<?php
require __DIR__ . '/../helpers/corretorCRUD.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ?page=inicial");
    exit;
}

// Conectar ao banco de dados
$con = mysqli_connect("localhost", "root", "", "corretora");
if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

$message = "";

// Obter informações do usuário
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $phone = $_POST['phone'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $sql = "UPDATE user SET username = ?, email = ?, cpf = ?, phone = ?, password = ? WHERE user_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssssi", $username, $email, $cpf, $phone, $password, $user_id);
    } else {
        $sql = "UPDATE user SET username = ?, email = ?, cpf = ?, phone = ? WHERE user_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssi", $username, $email, $cpf, $phone, $user_id);
    }

    if ($stmt->execute()) {
        $message = "Informações atualizadas com sucesso!";
    } else {
        $message = "Erro ao atualizar informações: " . htmlspecialchars($stmt->error);
    }

    // Processar upload da foto
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['photo']['tmp_name']);
        $photo = base64_encode($imageData);
        $sql = "UPDATE user SET photo = ? WHERE user_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $photo, $user_id);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
	<style>
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
	</style>
</head>
<body>
    <div class="container" style="margin-top: 20px;">
        <h2>Editar Informações do Usuário</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Nome</label>
                <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="cpf">CPF</label>
                <input type="text" name="cpf" id="cpf" class="form-control" value="<?php echo htmlspecialchars($user['cpf']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Telefone</label>
                <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Nova Senha (deixe em branco para não alterar)</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="photo">Foto</label>
                <input type="file" name="photo" id="photo" class="form-control">
                <?php if (!empty($user['photo'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo $user['photo']; ?>" alt="Foto do usuário" class="user-photo">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn-yellow">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>