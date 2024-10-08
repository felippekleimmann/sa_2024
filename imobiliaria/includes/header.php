<?php
session_start();
ob_start();

function isUserLoggedIn()
{
    // Verifica se o usuário está logado e se o session_id() é válido
    if (isset($_SESSION['session_id']) && $_SESSION['session_id'] === session_id()) {
        return true; // Usuário está logado
    } else {
        return false; // Usuário não está logado
    }
}

function logoutUser()
{
    // Limpa todas as variáveis de sessão
    $_SESSION = array();

    // Destrói a sessão
    session_destroy();

    // Redireciona para a página de login (ou qualquer outra página)
    header("Location: login.php");
    exit;
}
?>

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
                <?php
                if (isset($_SESSION['session_id']) && $_SESSION['session_id'] === session_id() && $_SESSION['tipo'] == 1) { ?>
                    <li><a href="?page=corretores">Corretores</a></li>
                    <li><a href="?page=anuncios">Anuncios</a></li>
                <?php
                } elseif (isset($_SESSION['session_id']) && $_SESSION['session_id'] === session_id() && $_SESSION['tipo'] == 2) { ?>
                    <li><a href="?page=anuncios">Anuncios</a></li>
                <?php
                } else {
                ?>
                    <li><a href="?page=sobre">Sobre nós</a></li>
                    <li><a href="?page=contato">Contato</a></li>
                    <li><a href="?page=anuncie">Anuncie</a></li>
                    <li><a href="?page=comprar">Comprar</a></li>
                <?php
                }

                ?>

            </ul>

        </nav>
        <a href="?page=area-do-corretor" class="client-area-container">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
            Área do Corretor
        </a>
    </div>
    <div class="menu-mobile-icon">
        <i class="fa-solid fa-bars"></i>
    </div>
</header>