<?php
// Conexão com o banco de dados
$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$message_error = '';
$message_success = '';

// Verificar se há dados de POST
if (!empty($_POST)) {
    $cpf = $_POST['cpf'];
    $password = $_POST['password'];

    // Consulta SQL para verificar o usuário
    $sql = "SELECT * FROM user WHERE cpf = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userInfos = $result->fetch_object();

        // Verificar se a senha fornecida coincide com a senha criptografada
        if ($password === $userInfos->password) {
            // Iniciar sessão e armazenar informações do usuário
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['session_id'] = session_id();
            $_SESSION['user_id'] = $userInfos->user_id;
            $_SESSION['name'] = $userInfos->username;
            $_SESSION['tipo'] = $userInfos->user_type_id;
            $_SESSION['photo'] = $userInfos->photo;

            // Redirecionar com base no tipo de usuário
            $message_success = 'Login realizado com sucesso!';

			if ($_SESSION['tipo'] == 1) {
				header("Refresh: 2; url=?page=admin_corretores");
			} else {
				header("Refresh: 2; url=?page=corretor");
			}
        } else {
            $message_error = 'Usuário ou senha incorretos.';
        }
    } else {
        $message_error = 'Usuário ou senha incorretos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Área do Corretor</title>
    <style>
        .mainContainer {
			margin-top: 300px;
            height: 100%;
            /* margin: 0; */
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            /* background-color: #f9f9f9; */
        }

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('assets/images/topo-fale-conosco.webp');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            filter: blur(8px);
            z-index: -1;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .login-title {
            font-size: 24px;
            color: #333333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666666;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #dddddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background-color: #ffc107;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #e0a800;
        }

        .message-error, .message-success {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }

        .message-error {
            background-color: #ffdddd;
            color: #d8000c;
            border: 1px solid #d8000c;
        }

        .message-success {
            background-color: #ddffdd;
            color: #008000;
            border: 1px solid #008000;
        }
    </style>
</head>
<body>
	<div class="mainContainer">
		<div class="background"></div>
			<div class="login-container">
				<h2 class="login-title">Login</h2>
				<form action="" method="post">
					<div class="form-group">
						<label for="cpf">CPF</label>
						<input name="cpf" type="text" class="form-control" id="cpf" placeholder="Digite seu CPF" value="">
					</div>
					<div class="form-group">
						<label for="password">Senha</label>
						<input name="password" type="password" class="form-control" id="password" placeholder="Digite sua senha" value="">
					</div>
					<button type="submit" class="btn">Entrar</button>
				</form>

				<?php if ($message_error != ''): ?>
					<div class="message-error">
						<?php echo $message_error; ?>
					</div>
				<?php endif; ?>

				<?php if ($message_success != ''): ?>
					<div class="message-success">
						<?php echo $message_success; ?>
					</div>
				<?php endif; ?>
			</div>
	</div>
</body>
</html>