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

$message = "";

// Processar criação de corretor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        // Obtenha os dados do formulário
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $phone = $_POST['phone'];

        // Processar a imagem para base64
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
            $imageData = file_get_contents($_FILES['photo']['tmp_name']);
            $photo = base64_encode($imageData);
        } else {
            $photo = '';
        }

        // Verificar se o CPF já existe no banco de dados
        $sqlCheck = "SELECT * FROM user WHERE cpf = ?";
        $stmtCheck = $con->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $cpf);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $message = "Erro: CPF já cadastrado!";
        } else {
            // Insira os dados no banco de dados
            $stmt = $con->prepare("INSERT INTO user (username, password, email, cpf, phone, photo, user_type_id) VALUES (?, ?, ?, ?, ?, ?, 2)");
            if ($stmt === false) {
                die('Erro na preparação da declaração SQL: ' . htmlspecialchars($con->error));
            }

            $stmt->bind_param("ssssss", $username, $password, $email, $cpf, $phone, $photo);

            if ($stmt->execute()) {
                $message = "Corretor criado com sucesso!";
            } else {
                $message = "Erro ao criar corretor: " . htmlspecialchars($stmt->error);
            }

            $stmt->close();
        }

        $stmtCheck->close();
    }

    // Processar remoção de corretor
    if (isset($_POST['delete'])) {
        $corretor_id = $_POST['corretor_id'];
        $stmt = $con->prepare("DELETE FROM usuario WHERE user_id = ? AND user_type_id = 2");
        $stmt->bind_param("i", $corretor_id);

        if ($stmt->execute()) {
            $message = "Corretor removido com sucesso!";
        } else {
            $message = "Erro ao remover corretor: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    }

    if (isset($_POST['update'])) {
        $corretor_id = $_POST['corretor_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $phone = $_POST['phone'];

        $photo = '';
        if (isset($_FILES['photo'])) {
            $imageData = file_get_contents($_FILES['photo']['tmp_name']);
            $photo = base64_encode($imageData);
        }

        $stmt = $con->prepare("UPDATE user SET username = ?, email = ?, cpf = ?, phone = ?, photo = ? WHERE user_id = ? AND user_type_id = 2");

        if ($stmt === false) {
            die('Erro na preparação da declaração SQL: ' . htmlspecialchars($con->error));
        }

        $stmt->bind_param("ssssss", $username, $email, $cpf, $phone, $photo, $corretor_id);

        if ($stmt->execute()) {
            $message = "Corretor atualizado com sucesso!";
        } else {
            $message = "Erro ao atualizar corretor: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    }
}

// Buscar todos os corretores
$corretores = $con->query("SELECT * FROM user WHERE user_type_id = 2");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar corretores</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
            border-color: #d39e00;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <h3>Criar Corretor</h3>
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="username">Nome</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="cpf">CPF</label>
                            <input type="text" name="cpf" id="cpf" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefone</label>
                            <input type="text" name="phone" id="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
							<label for="photo">Foto</label>
							<input type="file" name="photo" id="photo" class="form-control" accept="image/*">
						</div>
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" name="create" class="btn-yellow">Criar Corretor</button>
                    </form>
                </div>

                <div class="col-md-6">
                    <h3>Atualizar/Remover Corretor</h3>
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="corretor_selector">Selecionar Corretor</label>
                            <select id="corretor_selector" class="form-control">
                                <option value="">Selecione um corretor</option>
                                <?php while ($corretor = $corretores->fetch_assoc()): ?>
                                    <option value="<?php echo $corretor['user_id']; ?>" data-username="<?php echo htmlspecialchars($corretor['username']); ?>" data-email="<?php echo htmlspecialchars($corretor['email']); ?>" data-cpf="<?php echo htmlspecialchars($corretor['cpf']); ?>" data-phone="<?php echo htmlspecialchars($corretor['phone']); ?>" data-photo="<?php echo htmlspecialchars($corretor['photo']); ?>"><?php echo htmlspecialchars($corretor['username']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div id="updateForm" style="display:none;">
                            <input type="hidden" name="corretor_id" id="corretor_id">
                            <div class="form-group">
                                <label for="update_username">Nome</label>
                                <input type="text" name="username" id="update_username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="update_email">Email</label>
                                <input type="email" name="email" id="update_email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="update_cpf">CPF</label>
                                <input type="text" name="cpf" id="update_cpf" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="update_phone">Telefone</label>
                                <input type="text" name="phone" id="update_phone" class="form-control" required>
                            </div>
                            <div class="form-group">
								<label for="update_photo">Foto</label>
								<input type="file" name="photo" id="update_photo" class="form-control" accept="image/*">
							</div>
                            <button type="submit" name="update" class="btn-yellow">Atualizar Corretor</button>
                            <button type="submit" name="delete" class="btn-yellow">Remover Corretor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const corretorSelector = document.getElementById('corretor_selector');

            corretorSelector.addEventListener('change', function() {
                const selectedOption = corretorSelector.options[corretorSelector.selectedIndex];
                if (selectedOption.value) {
                    document.getElementById('updateForm').style.display = 'block';
                    document.getElementById('corretor_id').value = selectedOption.value;
                    document.getElementById('update_username').value = selectedOption.getAttribute('data-username');
					document.getElementById('update_email').value = selectedOption.getAttribute('data-email');
                    document.getElementById('update_cpf').value = selectedOption.getAttribute('data-cpf');
                    document.getElementById('update_phone').value = selectedOption.getAttribute('data-phone');
                    document.getElementById('update_photo').value = selectedOption.getAttribute('data-photo');
                } else {
                    document.getElementById('updateForm').style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>