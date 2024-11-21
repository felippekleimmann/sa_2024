<?php
// Conexão com o banco de dados
$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Verificar se há logout
if (!empty($_GET)) {
    if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
        logoutUser();
    }
}

$message_error = '';

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
		// var_dump($userInfos->password);
        // if (password_verify($password, $userInfos->password)) {
        if ($password ===  $userInfos->password) {
            // Iniciar sessão e armazenar informações do usuário
            session_start();
            $_SESSION['session_id'] = session_id();
            $_SESSION['user_id'] = $userInfos->user_id;
            $_SESSION['name'] = $userInfos->username;
            $_SESSION['tipo'] = $userInfos->user_type_id;
            $_SESSION['photo'] = $userInfos->photo;

            // Redirecionar com base no tipo de usuário
            if ($userInfos->user_type_id == 1) {
                header("Location: ?page=admin-page");
            } else if ($userInfos->user_type_id == 2) {
                header("Location: ?page=corretor");
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
        body, html {
            height: 100%;
            margin: 0;
			overflow: hidden;
        }

        .background {
			background-image: url('assets/images/topo-fale-conosco.webp');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
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
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .message-error {
            margin-top: 15px;
            padding: 10px;
            background-color: #ffdddd;
            color: #d8000c;
            border: 1px solid #d8000c;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="contact-section">
    <div class="page-header large" style="background-image: url('assets/images/topo-fale-conosco.webp');">
        <div class="description">
            <div class="container">
                <div class="row">
                    <div class="col-md-5 offset-md-1">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="form-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-3">
                        <h2 class="title title-2">Login</h2>
                        <form action="#" class="form" id="form-anage-contato" method="post">
                            <div class="form-group">
                                <label for="contactName" class="label-control">CPF</label>
                                <input name="cpf" type="text" class="form-control" id="contactName" placeholder="Digite seu CPF" value="">
                            </div>
                            <div class="form-group">
                                <label for="contactSubject" class="label-control">Senha</label>
                                <input name="password" type="password" class="form-control" id="contactSubject" placeholder="Digite sua senha" value="">
                            </div>
                            <button type="submit" name="button" class="btn btn-1">Entrar</button>
                        </form>

                        <?php if ($message_error != ''): ?>
                            <div class="message-error">
                                <?php echo $message_error; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>