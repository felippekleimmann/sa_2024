<?php
$con = mysqli_connect("localhost", "root", "", "nuhaus");

if (!empty($_GET)) {
    if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
        logoutUser();
    }
}

$message_error = '';

if (!empty($_POST)) {
    $sql = "SELECT * FROM user 
    WHERE cpf = '" . $_POST['cpf'] . "' 
    AND password = '" . $_POST['password']  . "';";

    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        $userInfos = $result->fetch_object();

        $_SESSION['session_id'] = session_id();
        $_SESSION['name'] = $userInfos->name;
        $_SESSION['tipo'] = $userInfos->user_type_id;

        header("Location: ?page=admin-page");

        // if ($userInfos->user_type_id == 1) {

        // }

        // if ($userInfos->user_type_id == 2) {
        //     header("Location: ?page=corretor");
        // }
    } else {
        $message_error = 'Usuário ou senha incorretos.';
    }
}


?>

</html>
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
                            <div class="form-group"><label for="contactName" class="label-control">CPF</label><input name="cpf" type="text" class="form-control" id="contactName" placeholder="Digite seu nome" value=""></div>
                            <div class="form-group"><label for="contactSubject" class="label-control">Senha</label><input name="password" type="password" class="form-control" id="contactSubject" placeholder="Informe o assunto" value=""></div>
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
</div>