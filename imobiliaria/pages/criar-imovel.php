<?php
require __DIR__ . '/../helpers/corretorCRUD.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id']) || ($_SESSION['tipo'] != 1 && $_SESSION['tipo'] != 2)) {
    header("Location: login.php");
    exit;
}

$message = "";

// Processar criação de imóvel
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $address = $_POST['address'];
        $city_id = $_POST['city_id'];
        $state_id = $_POST['state_id'];
        $info_area_total = $_POST['info_area_total'];
        $info_parking_space = $_POST['info_parking_space'];
        $info_rooms = $_POST['info_rooms'];
        $bairro = $_POST['bairro'];
        $condominium_price = $_POST['condominium_price'];
        $build_type = $_POST['build_type'];
        $iptu_price = $_POST['iptu_price'];

        $stmt = $con->prepare("INSERT INTO build (address, city_id, state_id, info_area_total, info_parking_space, info_rooms, bairro, condominium_price, iptu_price, build_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $message = "Erro na preparação da declaração SQL: " . htmlspecialchars($con->error);
        } else {
            $stmt->bind_param("siiddiisds", $address, $city_id, $state_id, $info_area_total, $info_parking_space, $info_rooms, $bairro, $condominium_price, $iptu_price, $build_type);

            if ($stmt->execute()) {
                $message = "Imóvel criado com sucesso!";
            } else {
                $message = "Erro ao criar imóvel: " . htmlspecialchars($stmt->error);
            }

            $stmt->close();
        }
    }

    // Processar remoção de imóvel
    if (isset($_POST['delete'])) {
		$build_id = $_POST['build_id'];

		// Primeiro deletar as fotos dos anúncios que referenciam este imóvel
		$stmt = $con->prepare("DELETE FROM announcement_photos WHERE announcement_id IN (SELECT announcement_id FROM announcement WHERE build_id = ?)");
		if ($stmt === false) {
			$message = "Erro na preparação da declaração SQL para deletar fotos dos anúncios: " . htmlspecialchars($con->error);
		} else {
			$stmt->bind_param("i", $build_id);
			if ($stmt->execute()) {
				// Agora deletar os anúncios que referenciam este imóvel
				$stmt = $con->prepare("DELETE FROM announcement WHERE build_id = ?");
				if ($stmt === false) {
					$message = "Erro na preparação da declaração SQL para deletar anúncios: " . htmlspecialchars($con->error);
				} else {
					$stmt->bind_param("i", $build_id);
					if ($stmt->execute()) {
						// Agora deletar o imóvel
						$stmt = $con->prepare("DELETE FROM build WHERE build_id = ?");
						if ($stmt === false) {
							$message = "Erro na preparação da declaração SQL para deletar imóvel: " . htmlspecialchars($con->error);
						} else {
							$stmt->bind_param("i", $build_id);
							if ($stmt->execute()) {
								$message = "Imóvel removido com sucesso!";
							} else {
								$message = "Erro ao remover imóvel: " . htmlspecialchars($stmt->error);
							}
						}
					} else {
						$message = "Erro ao remover anúncios: " . htmlspecialchars($stmt->error);
					}
				}
			} else {
				$message = "Erro ao remover fotos dos anúncios: " . htmlspecialchars($stmt->error);
			}
		}
		$stmt->close();
	}

    // Processar atualização de imóvel
    if (isset($_POST['update'])) {
        $build_id = $_POST['build_id'];
        $address = $_POST['address'];
        $city_id = $_POST['city_id'];
        $state_id = $_POST['state_id'];
        $info_area_total = $_POST['info_area_total'];
        $info_parking_space = $_POST['info_parking_space'];
        $info_rooms = $_POST['info_rooms'];
        $bairro = $_POST['bairro'];
        $condominium_price = $_POST['condominium_price'];
        $build_type = $_POST['build_type'];
        $iptu_price = $_POST['iptu_price'];

        $stmt = $con->prepare("UPDATE build SET address = ?, city_id = ?, state_id = ?, info_area_total = ?, info_parking_space = ?, info_rooms = ?, bairro = ?, condominium_price = ?, build_type = ?, iptu_price = ? WHERE build_id = ?");
        if ($stmt === false) {
            $message = "Erro na preparação da declaração SQL: " . htmlspecialchars($con->error);
        } else {
            $stmt->bind_param("siiddiisdsi", $address, $city_id, $state_id, $info_area_total, $info_parking_space, $info_rooms, $bairro, $condominium_price, $iptu_price, $build_type, $build_id);

            if ($stmt->execute()) {
                $message = "Imóvel atualizado com sucesso!";
            } else {
                $message = "Erro ao atualizar imóvel: " . htmlspecialchars($stmt->error);
            }

            $stmt->close();
        }
    }
}

// Buscar todos os imóveis
$builds = getFullBuilds($con);

// Funções para buscar cidades e estados
function getCities($con) {
    $sql = "SELECT city_id, name FROM city";
    $result = $con->query($sql);
    return $result;
}

function getStates($con) {
    $sql = "SELECT state_id, name FROM state";
    $result = $con->query($sql);
    return $result;
}

$cities = getCities($con);
$states = getStates($con);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Imóveis</title>
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
            border-color: #e0a800;
        }
    </style>
</head>
<body>
    <!-- <?php include '../includes/header.php'; ?> -->
    <div class="content">
        <div class="container">
            <h2>Gerenciamento de Imóveis</h2>

            <?php if ($message): ?>
                <div class="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <h3>Criar Imóvel</h3>
                    <form method="post">
                        <div class="form-group">
                            <label for="address">Endereço</label>
                            <input type="text" name="address" id="address" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="city_id">Cidade</label>
                            <select name="city_id" id="city_id" class="form-control" required>
                                <?php while ($city = $cities->fetch_assoc()): ?>
                                    <option value="<?php echo $city['city_id']; ?>"><?php echo htmlspecialchars($city['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="state_id">Estado</label>
                            <select name="state_id" id="state_id" class="form-control" required>
                                <?php while ($state = $states->fetch_assoc()): ?>
                                    <option value="<?php echo $state['state_id']; ?>"><?php echo htmlspecialchars($state['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="info_area_total">Área Total</label>
                            <input type="number" name="info_area_total" id="info_area_total" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="info_parking_space">Vagas de Estacionamento</label>
                            <input type="number" name="info_parking_space" id="info_parking_space" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="info_rooms">Quartos</label>
                            <input type="number" name="info_rooms" id="info_rooms" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="bairro">Bairro</label>
                            <input type="text" name="bairro" id="bairro" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="condominium_price">Preço do Condomínio</label>
                            <input type="number" name="condominium_price" id="condominium_price" class="form-control" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="build_type">Tipo do imóvel</label>
                            <select name="build_type" id="build_type" class="form-control" required>
								<option value="">Selecione o tipo</option>
								<option value="Apartamento">Apartamento</option>
								<option value="Casa">Casa/Sobrado</option>
								<option value="Chácara/Sítio">Chácara/Sítio</option>
								<option value="Comercial">Comercial</option>
								<option value="Terreno">Terreno</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="iptu_price">Preço do IPTU</label>
                            <input type="number" name="iptu_price" id="iptu_price" class="form-control" step="0.01" required>
                        </div>
						<button type="submit" name="create" class="btn-yellow">Criar Imóvel</button>
                    </form>
                </div>

                <div class="col-md-6">
                    <h3>Atualizar/Remover Imóvel</h3>
                    <form method="post">
                        <div class="form-group">
                            <label for="build_selector">Selecionar Imóvel</label>
                            <select id="build_selector" class="form-control">
                                <option value="">Selecione um imóvel</option>
                                <?php while ($row = $builds->fetch_assoc()): ?>
                                    <option value="<?php echo $row['build_id']; ?>"
									data-address="<?php echo htmlspecialchars($row['address']); ?>"
									data-city-id="<?php echo $row['city_id']; ?>"
									data-state-id="<?php echo $row['state_id']; ?>"
									data-info-area-total="<?php echo $row['info_area_total']; ?>"
									data-info-parking-space="<?php echo $row['info_parking_space']; ?>"
									data-info-rooms="<?php echo $row['info_rooms']; ?>"
									data-bairro="<?php echo htmlspecialchars($row['bairro']); ?>"
									data-condominium-price="<?php echo $row['condominium_price']; ?>"
									data-iptu-price="<?php echo $row['iptu_price']; ?>"
									data-build-type="<?php echo $row['build_type']; ?>">
                                        <?php echo htmlspecialchars($row['address']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div id="updateForm" style="display:none;">
                            <input type="hidden" name="build_id" id="build_id">
                            <div class="form-group">
                                <label for="address">Endereço</label>
                                <input type="text" name="address" id="update_address" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="city_id">Cidade</label>
                                <select name="city_id" id="update_city_id" class="form-control" required>
                                    <?php
                                    // Reset the result set pointer and fetch rows again
                                    $cities->data_seek(0);
                                    while ($city = $cities->fetch_assoc()): ?>
                                        <option value="<?php echo $city['city_id']; ?>"><?php echo htmlspecialchars($city['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="state_id">Estado</label>
                                <select name="state_id" id="update_state_id" class="form-control" required>
                                    <?php
                                    $states->data_seek(0);
                                    while ($state = $states->fetch_assoc()): ?>
                                        <option value="<?php echo $state['state_id']; ?>"><?php echo htmlspecialchars($state['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="info_area_total">Área Total</label>
                                <input type="number" name="info_area_total" id="update_info_area_total" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="info_parking_space">Vagas de Estacionamento</label>
                                <input type="number" name="info_parking_space" id="update_info_parking_space" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="info_rooms">Quartos</label>
                                <input type="number" name="info_rooms" id="update_info_rooms" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="bairro">Bairro</label>
                                <input type="text" name="bairro" id="update_bairro" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="condominium_price">Preço do Condomínio</label>
                                <input type="number" name="condominium_price" id="update_condominium_price" class="form-control" step="0.01">
                            </div>
                            <div class="form-group">
                            <label for="build_type">Tipo do imóvel</label>
                            <select name="build_type" id="update_build_type" class="form-control" required>
								<option value="">Selecione o tipo</option>
								<option value="Apartamento">Apartamento</option>
								<option value="Casa">Casa/Sobrado</option>
								<option value="Chácara/Sítio">Chácara/Sítio</option>
								<option value="Comercial">Comercial</option>
								<option value="Terreno">Terreno</option>
                            </select>
                        </div>
                            <div class="form-group">
                                <label for="iptu_price">Preço do IPTU</label>
                                <input type="number" name="iptu_price" id="update_iptu_price" class="form-control" step="0.01" required>
                            </div>
                            <button type="submit" name="update" class="btn-yellow">Atualizar</button>
                            <button type="submit" name="delete" class="btn-yellow">Remover</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const buildSelector = document.getElementById('build_selector');
    const updateForm = document.getElementById('updateForm');
    const addressInput = document.getElementById('update_address');
    const cityInput = document.getElementById('update_city_id');
    const stateInput = document.getElementById('update_state_id');
    const areaTotalInput = document.getElementById('update_info_area_total');
    const parkingSpaceInput = document.getElementById('update_info_parking_space');
    const roomsInput = document.getElementById('update_info_rooms');
    const bairroInput = document.getElementById('update_bairro');
    const condominiumPriceInput = document.getElementById('update_condominium_price');
    const iptuPriceInput = document.getElementById('update_iptu_price');
    const buildTypeInput = document.getElementById('update_build_type');
    const buildIdInput = document.getElementById('build_id');

    buildSelector.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            addressInput.value = selectedOption.getAttribute('data-address') || '';
            cityInput.value = selectedOption.getAttribute('data-city-id') || '';
            stateInput.value = selectedOption.getAttribute('data-state-id') || '';
            areaTotalInput.value = selectedOption.getAttribute('data-info-area-total') || '';
            parkingSpaceInput.value = selectedOption.getAttribute('data-info-parking-space') || '';
            roomsInput.value = selectedOption.getAttribute('data-info-rooms') || '';
            bairroInput.value = selectedOption.getAttribute('data-bairro') || '';
            condominiumPriceInput.value = selectedOption.getAttribute('data-condominium-price') || '';
            iptuPriceInput.value = selectedOption.getAttribute('data-iptu-price') || '';
            buildTypeInput.value = selectedOption.getAttribute('data-build-type') || '';
            buildIdInput.value = this.value;
            updateForm.style.display = 'block';
        } else {
            updateForm.style.display = 'none';
        }
    });
});
    </script>
</body>
</html>