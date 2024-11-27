<?php
	// Conectar ao banco de dados
	$con = mysqli_connect("localhost", "root", "", "corretora");

	if (!$con) {
		die("Conexão falhou: " . mysqli_connect_error());
	}

	// Inicializar variáveis de filtro
	$category = isset($_POST['category']) ? $_POST['category'] : '';
	$min_price = isset($_POST['min_price']) ? $_POST['min_price'] : '';
	$max_price = isset($_POST['max_price']) ? $_POST['max_price'] : '';
	$info_rooms = isset($_POST['info_rooms']) ? $_POST['info_rooms'] : '';
	$info_parking_space = isset($_POST['info_parking_space']) ? $_POST['info_parking_space'] : '';

	// Número de anúncios por página
	$limit = 6;

	// Determinar a página atual
	$page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
	$page = max($page, 1); // Garantir que a página seja pelo menos 1

	// Calcular o offset
	$offset = ($page - 1) * $limit;

	// Construir a consulta SQL com filtros
	$sql = "SELECT a.announcement_id, a.title, a.description, a.price, b.address, b.info_rooms, b.info_area_total, b.info_parking_space, b.bairro
			FROM announcement a
			JOIN build b ON a.build_id = b.build_id
			WHERE 1=1";

	if ($category !== '') {
		$sql .= " AND b.build_type = '" . mysqli_real_escape_string($con, $category) . "'";
	}

	if ($min_price !== '') {
		$sql .= " AND a.price >= " . mysqli_real_escape_string($con, $min_price);
	}

	if ($max_price !== '') {
		$sql .= " AND a.price <= " . mysqli_real_escape_string($con, $max_price);
	}

	if ($info_rooms !== '') {
		$sql .= " AND b.info_rooms = " . mysqli_real_escape_string($con, $info_rooms);
	}

	if ($info_parking_space !== '') {
		$sql .= " AND b.info_parking_space = " . mysqli_real_escape_string($con, $info_parking_space);
	}

	// Executar a consulta SQL para obter o número total de resultados (sem limites de paginação)
	$total_result = $con->query($sql);
	if ($total_result === false) {
		die("Erro na execução da consulta SQL: " . htmlspecialchars($con->error));
	}
	$total_results = mysqli_num_rows($total_result);

	// Calcular o número total de páginas
	$total_pages = ceil($total_results / $limit);

	// Adicionar LIMIT e OFFSET à consulta original para paginação
	$sql .= " LIMIT $limit OFFSET $offset";
	$result = $con->query($sql);

	if ($result === false) {
		die("Erro na execução da consulta SQL: " . htmlspecialchars($con->error));
	}
	?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Buscar Imóvel</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .form-container {
            background: rgba(0, 0, 0, 0.2); /* Fundo semitransparente para contraste */
            padding: 20px;
            border-radius: 10px;
            margin-top: 50px;
            margin-bottom: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-container .form-control {
            background: rgba(255, 255, 255, 0.9); /* Campos com fundo semitransparente */
            border: none;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-container .form-control::placeholder {
            color: #6c757d;
            font-style: italic;
        }

        .form-container .btn-1 {
            background-color: #ffcc00;
            border: none;
            color: #1a1818;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 8px;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .form-container .btn-1:hover {
            background-color: #e6b800;
        }

        .form-container label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            color: #fff;
        }

        .form-container .input-group-text {
            background-color: #ffcc00;
            border: none;
            border-radius: 8px 0 0 8px;
            color: #1a1818;
        }

        .form-container .input-group {
            margin-bottom: 20px;
        }

        .form-container .input-group .form-control {
            border-radius: 0 8px 8px 0;
        }

		.input-group-prepend {
			height: 38px;
		}

		.background-blur {
            background-image: url('assets/images/topo-fale-conosco.webp');
            filter: blur(8px);
            background-size: cover;
            background-position: center;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .form-container {
            position: relative;
            z-index: 1;
        }

		::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

		.footer {
    background-color: #ffc107;
    color: #212529;
    padding: 30px 0;
    position: relative;
    bottom: 0;
    width: 100%;
}

.footer .container {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.footer .footer-content {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.footer .footer-section {
    flex: 1;
    padding: 10px 20px;
    min-width: 250px;
}

.footer .logo-text {
    font-size: 24px;
    font-weight: bold;
}

.footer .contact span, .footer .socials a {
    display: block;
    margin: 5px 0;
}

.footer .socials a {
    color: #212529;
    margin-right: 15px;
}

.footer .socials a:hover {
    color: #fff;
}

.footer .links ul {
    list-style: none;
    padding: 0;
}

.footer .links ul a {
    text-decoration: none;
    color: #212529;
}

.footer .links ul a:hover {
    color: #fff;
}

.footer .contact-form .contact-input {
    background-color: #fff;
    border: none;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
    width: calc(100% - 20px);
}

.footer .btn-warning {
    background-color: #212529;
    border: none;
    color: #ffc107;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
}

.footer .btn-warning:hover {
    background-color: #1a1a1a;
}

.footer-bottom {
    text-align: center;
    padding: 20px;
    background-color: #212529;
    color: #ffc107;
    font-size: 14px;
}
    </style>
</head>
<body>
<div style="width: 100%; position: relative;">
<div class="background-blur"></div>
	<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <form action="" method="POST" class="form">
                        <div class="form-row">
                            <div class="col-md-6">
                                <label for="searchForm-category" class="label-control">Tipo do Imóvel</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    </div>
                                    <select class="form-control custom-select" id="searchForm-category" name="category">
                                        <option value="">Selecione o tipo</option>
                                        <option value="Apartamento">Apartamento</option>
                                        <option value="Casa">Casa/Sobrado</option>
                                        <option value="Chácara/Sítio">Chácara/Sítio</option>
                                        <option value="Comercial">Comercial</option>
                                        <option value="Terreno">Terreno</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="searchForm-location" class="label-control">Localização</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="searchForm-location" name="location" placeholder="Cidade ou Bairro">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <label for="searchForm-min-price" class="label-control">Preço Mínimo (R$)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="searchForm-min-price" name="min_price" placeholder="Mínimo">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="searchForm-max-price" class="label-control">Preço Máximo (R$)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="searchForm-max-price" name="max_price" placeholder="Máximo">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <label for="searchForm-rooms" class="label-control">Número de Quartos</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-bed"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="searchForm-rooms" name="info_rooms" placeholder="Número de Quartos">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="searchForm-parking" class="label-control">Vagas de Garagem</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-car"></i></span>
                                    </div>
                                    <input type="number" class="form-control" id="searchForm-parking" name="info_parking_space" placeholder="Vagas de Garagem">
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-1 btn-lg">Buscar meu Imóvel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	</div>

    <div class="properties-master-container">
		<div style="margin-top: 20px;">
		<h3>Imóveis para comprar em Joinville<br><span><?php echo $total_results; ?> Resultados</span></h3>
		</div>

        <div class="properties-container">
			<?php

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $announcement_id = $row['announcement_id'];

                    // Buscar fotos do anúncio
                    $photoResult = $con->query("SELECT photo FROM announcement_photos WHERE announcement_id = $announcement_id");
                    $photos = [];
                    while ($photoRow = $photoResult->fetch_assoc()) {
                        $photos[] = $photoRow['photo'];
                    }

                    $photo_base64 = !empty($photos) && strlen($photos[0]) > 100 ? htmlspecialchars(trim($photos[0])) : 'assets/images/image-placeholder.png';
					$urlstart = !empty($photos) && strlen($photos[0]) > 100 ? 'data:image/jpeg;base64,' : '';

                    echo '<a href="?page=imovel&announcement_id=' . $announcement_id . '" class="propertie">';
                    echo '<div class="image" style="background-image: url(\'' . $urlstart .  $photo_base64 . '\');">';
                    echo '</div>';

                    echo '<div class="infos-container">';
                    echo '<div class="type">' . htmlspecialchars($row['title']) . '</div>';
                    echo '<div class="bairro">' . htmlspecialchars($row['bairro']) . ' - Joinville</div>';
                    echo '<div class="price">R$ ' . number_format($row['price'], 2, ',', '.') . '</div>';
                    echo '<div class="icons-info">';
                    echo '<ul>';
                    echo '<li><i class="fa-solid fa-bed"></i> ' . htmlspecialchars($row['info_rooms']) . ' quartos</li>';
                    echo '<li><i class="fa-regular fa-square"></i> ' . htmlspecialchars($row['info_area_total']) . 'm²</li>';
                    echo '<li><i class="fa-solid fa-car"></i> ' . htmlspecialchars($row['info_parking_space']) . ' vaga</li>';
                    echo '</ul>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                }
            } else {
                echo "<p>Nenhum imóvel encontrado com os filtros aplicados.</p>";
            }

            $con->close();
            ?>
        </div>

		<?php if ($total_pages > 1): ?>
		<div class="pagination" style="margin-top: 20px; gap: 10px">
            <?php if ($page > 1): ?>
                <a href="?page=comprar&page_num=<?php echo $page - 1; ?>" class="btn btn-1 btn-lg" ">Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=comprar&page_num=<?php echo $i; ?>" class="btn <?php echo ($i == $page) ? 'btn-2 btn-lg' : 'btn-1 btn-lg'; ?>" style="height: 60px; width: 60px;"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=comprar&page_num=<?php echo $page + 1; ?>" class="btn btn-1 btn-lg">Próxima</a>
            <?php endif; ?>
        </div>
		<?php endif; ?>

    </div>
	<footer class="footer" style="margin-top: 30px">
    <div class="container">
        <div class="footer-content">

        </div>
    </div>
    <div class="footer-bottom">
        &copy; nuhaus.com | Designed by Correa
    </div>
</footer>
</body>
</html>