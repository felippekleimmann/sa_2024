<?php
session_start();
ob_start();

function isUserLoggedIn(): bool
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
                if (isUserLoggedIn()) { ?>
                    <?php if ($_SESSION['tipo'] == 1) { ?>
                        <li><a href="?page=cadastros">Cadastros</a></li>
                        <li><a href="?page=anuncios-corretor">Anuncios</a></li>
                        <li><a href="?page=corretores-adm">Corretores</a></li>
                    <?php } else { ?>
                        <li><a href="?page=corretor">Meus Anúncios</a></li>
                        <li><a href="?page=criar-anuncio">Criar Anúncio</a></li>
                    <?php } ?>
                <?php } else { ?>
                    <li><a href="?page=sobre">Sobre nós</a></li>
                    <li><a href="?page=contato">Contato</a></li>
                    <li><a href="?page=anuncie">Anuncie</a></li>
                    <li><a href="?page=comprar">Comprar </a></li>
                <?php
                }
                ?>
            </ul>
        </nav>
        <?php
        if (!isUserLoggedIn()):
        ?>
            <a href="?page=area-do-corretor" class="client-area-container">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                Área Restrita
            </a>
        <?php else: ?>
            <div class="logged-in-header-container">
                <div class="user-info">
                    <?php if (!empty($_SESSION['photo'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($_SESSION['photo']); ?>" alt="Foto do usuário" class="user-photo">
                    <?php endif; ?>
                    <div class="user-name">
                        Olá, <?php echo htmlspecialchars($_SESSION['name']); ?>
                    </div>
                </div>
                <a href="?page=logout" class="client-area-container">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Sair
                </a>
            </div>
        <?php endif; ?>
    </div>
</header>