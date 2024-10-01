<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/vendors/fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/reset.css">
    <link rel="stylesheet" type="text/css" href="assets/css/anage-css.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css">

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/_qs.js"></script>
</head>

<body>

    <header class="main-header">
        <div class="logo">
            <a href="?page=inicial">
                <img src="assets/images/logo.webp">
            </a>
        </div>
        <div class="menu-itens-container">
            <div class="close-menu">
                <i class="fa-solid fa-x"></i>
                Fechar
            </div>
            <nav>
                <ul>
                    <li><a href="?page=sobre">Sobre nós</a></li>
                    <li><a href="?page=contato">Contato</a></li>
                    <li><a href="?page=anuncie">Anuncie</a></li>
                    <li><a href="?page=comprar">Comprar</a></li>
                    <li><a href="?page=alugar">Alugar</a></li>
                </ul>
            </nav>
            <a href="?page=areacliente" class="client-area-container">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                Área do Cliente
            </a>
        </div>
        <div class="menu-mobile-icon">
            <i class="fa-solid fa-bars"></i>
        </div>
    </header>
    <section class="main-container">
        <?php
        $page_default = 'inicial';
        $page = $page_default;
        if (!empty($_GET['page'])) {
            $page = $_GET['page'];
        }
        $page_url = 'pages/' . $page . '.php';
        if (file_exists($page_url)) {
            include_once($page_url);
        } else {
            include_once('pages/' . $page_default . '.php');
        }
        ?>
    </section>
    <footer class="main-footer">

    </footer>


    <script src="assets/js/script.js"></script>
    <script src="assets/vendors/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"></script>
</body>

</html>