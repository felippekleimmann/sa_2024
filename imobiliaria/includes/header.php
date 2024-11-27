<?php
session_start();
ob_start();

// Conectar ao banco de dados
$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

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
            <img src="assets/images/nuhaus.png" style="width: 150px; height: 140px; margin-top: 10px">
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
						<li><a href="?page=criar-imovel">Imóveis</a></li>
                        <li><a href="?page=editar-usuario">Perfil</a></li>
						<?php if ($_SESSION['tipo'] == 1) { ?>
							<li><a href="?page=corretores-adm">Criar Usuários</a></li>
							<li><a href="?page=admin_corretores">Corretores</a></li>
						<?php } else { ?>
							<li><a href="?page=criar-anuncio">Criar Anúncio</a></li>
                        	<li><a href="?page=corretor">Meus Anúncios</a></li>
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
				<?php if (!empty($_SESSION['photo']) && strlen($_SESSION['photo']) > 100): ?>
					<img src="data:image/jpeg;base64,<?php echo htmlspecialchars($_SESSION['photo']); ?>" alt="Foto do usuário" class="user-photo">
				<?php else: ?>
					<img src="assets/images/default-avatar.png" alt="Foto do usuário" class="user-photo">
				<?php endif; ?>
                    <div class="user-name">
                        Olá, <?php echo htmlspecialchars($_SESSION['name']); ?>
                        <div class="notification-icon" onclick="toggleNotificationDropdown()">
                            <i class="fa-solid fa-bell"></i>
                            <?php
                            // Conte o número de notificações não lidas
                            $stmt = $con->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();
                            $stmt->bind_result($unreadNotifications);
                            $stmt->fetch();
                            $stmt->close();
                            if ($unreadNotifications > 0) {
                                echo "<span class='notification-count'>$unreadNotifications</span>";
                            }
                            ?>
                            <div class="notification-dropdown" id="notificationDropdown">
                                <ul>
                                    <?php
                                    // Buscar notificações do usuário
									$stmt = $con->prepare("SELECT notification_id, message, detailed_message FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
									$stmt->bind_param("i", $_SESSION['user_id']);
									$stmt->execute();
									$result = $stmt->get_result();
									while ($row = $result->fetch_assoc()) {
										$notificationId = $row['notification_id'];
										$messageFromNotification = htmlspecialchars($row['message']);
										$detailedMessage = htmlspecialchars($row['detailed_message']);
										echo "<li style='display: flex; justify-content: space-between; align-items: center; gap: 10px'>
												<span onclick=\"showNotificationDetails('$messageFromNotification', '$detailedMessage')\">$messageFromNotification</span>
												<button class='delete-btn' onclick=\"showConfirmDeleteModal($notificationId)\">
													<i class='fa fa-trash'></i>
												</button>
											  </li>";
									}
									$stmt->close();
                                    ?>
                                </ul>
                            </div>
                        </div>
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

<div id="notificationModal" class="modal">
    <div class="modal-content">
	<span class="close">&times;</span>
        <div class="modal-header">
            <h2 id="modalMessage"></h2>
        </div>
        <div class="modal-body">
            <p id="modalDetailedMessage"></p>
        </div>
    </div>
</div>

<div id="confirmDeleteModal" class="modal">
    <div class="modal-content">
		<span class="close">&times;</span>
        <div class="modal-header">
            <h2>Confirmar Exclusão</h2>
        </div>
        <div class="modal-body">
            <p>Tem certeza que deseja excluir esta notificação?</p>
        </div>
        <div class="modal-footer">
			<div>
				<button class="btn-cancel-modal">Cancelar</button>
				<button class="btn-confirm-modal">Excluir</button>
			</div>
        </div>
    </div>
</div>

<script>
	var notificationIdToDelete;
// Função para alternar a exibição do dropdown de notificações
function toggleNotificationDropdown() {
    var dropdown = document.getElementById('notificationDropdown');
    dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';

    if (dropdown.style.display === 'block') {
        // Marcar notificações como lidas
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "pages/mark_notifications_read.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("user_id=<?php echo $_SESSION['user_id']; ?>");

        // Remover o número de notificações não lidas
        var notificationCount = document.querySelector('.notification-count');
        if (notificationCount) {
            notificationCount.remove();
        }
    }
}

// Função para fechar o dropdown se o usuário clicar fora dele
window.onclick = function(event) {
    if (!event.target.matches('.notification-icon') && !event.target.matches('.notification-icon *')) {
        var dropdown = document.getElementById('notificationDropdown');
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        }
    }
}

// Função para exibir detalhes da notificação
function showNotificationDetails(message, detailedMessage) {
    var modal = document.getElementById("notificationModal");
    var modalMessage = document.getElementById("modalMessage");
    var modalDetailedMessage = document.getElementById("modalDetailedMessage");
    var span = document.getElementsByClassName("close")[0];

    modalMessage.textContent = message;
	if (detailedMessage) {
		modalDetailedMessage.textContent = detailedMessage;
	} else {
		modalDetailedMessage.textContent = "Não há detalhes adicionais.";
	}

    modal.style.display = "block";

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

function showConfirmDeleteModal(notificationId) {
    notificationIdToDelete = notificationId;
    var modal = document.getElementById("confirmDeleteModal");
    var span = document.getElementsByClassName("close")[1];
    var cancelBtn = document.querySelector(".btn-cancel-modal");
    var confirmBtn = document.querySelector(".btn-confirm-modal");

    modal.style.display = "block";

    span.onclick = function() {
        modal.style.display = "none";
    }

    cancelBtn.onclick = function() {
        modal.style.display = "none";
    }

    confirmBtn.onclick = function() {
        deleteNotification(notificationIdToDelete);
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

function deleteNotification(notificationId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "helpers/delete_notification.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert("Notificação excluída com sucesso!");
            location.reload(); // Recarregar a página para atualizar a lista de notificações
        }
    };
    xhr.send("notification_id=" + notificationId);
}
</script>

<style>
	/* Estilos para o botão de exclusão */
	.delete-btn {
    background-color: transparent;
    color: #ff4d4d;
    border: none;
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 5px;
    transition: color 0.3s ease;
	}

	.delete-btn:hover {
		color: #cc0000;
	}

	.notification-dropdown ul li {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 10px;
		border-bottom: 1px solid #ddd;
	}

	.notification-dropdown ul li:hover {
		background-color: #f1f1f1;
	}
/* Estilos para a modal de notificações */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.6);
    font-family: 'Arial', sans-serif;
}

.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    width: 80%;
    max-width: 600px;
    position: relative;
    animation: slide-down 0.3s ease-out;
}

@keyframes slide-down {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    margin-top: -10px;
}

.close:hover,
.close:focus {
    color: #333;
    text-decoration: none;
    cursor: pointer;
}

.modal-header {
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.modal-header h2 {
    margin: 0;
    font-size: 24px;
}

.modal-body p {
    font-size: 20px; /* Aumentado de 16px para 18px */
    line-height: 2; /* Melhorar a legibilidade */
    color: #555;
}

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-start; /* Alinha os botões à esquerda */
    padding: 1rem;
    border-top: 1px solid #e9ecef;
}

.btn-close-modal, .btn-cancel-modal, .btn-confirm-modal {
    background-color: #ff4d4d;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    margin-left: 10px;
}

.btn-close-modal:hover, .btn-cancel-modal:hover, .btn-confirm-modal:hover {
    background-color: #cc0000;
}

#visitorRequestForm {
    margin-top: 20px;
}

#visitorRequestForm .form-group {
    margin-bottom: 15px;
}

#visitorRequestForm .btn-yellow {
    background-color: #ffc107;
    color: #212529;
    border: 1px solid #ffc107;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
}

#visitorRequestForm .btn-yellow:hover {
    background-color: #0251a6;
    border-color: #0251a6;
}

/* Estilos para o ícone de notificação */
.notification-icon {
    position: relative;
    display: inline-block;
    margin-left: 10px;
    cursor: pointer;
}

.notification-count {
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    position: absolute;
    top: -10px;
    right: -10px;
    font-size: 12px;
}

/* Estilos para o dropdown de notificações */
.notification-dropdown {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    color: black;
    min-width: 450px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.notification-dropdown ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.notification-dropdown ul li {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.notification-dropdown ul li:hover {
    background-color: #f1f1f1;
}
</style>