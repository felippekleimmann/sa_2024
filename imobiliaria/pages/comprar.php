<div class="result-content">
    <div class="filters-section position-relative">
        <div class="block-filters position-static">
            <form action="#" class="form">
			<div class="form-row">
                    <div class="col-md-3">
                        <div class="form-group normal-group black-group"><select class="form-control custom-select" id="searchForm-modality" name="statusLabel">
                                <option value="comprar">Comprar</option>
                                <option value="lancamentos">Lançamentos</option>
                            </select></div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group"><label for="searchForm-category" class="label-control">Tipo do Imóvel</label><select class="form-control custom-select" id="searchForm-category" name="category">
                                <option value="">Selecione o tipo</option>
                                <option value="22">Apartamento</option>
                                <option value="7">Casa/Sobrado</option>
                                <option value="11">Chácara/Sítio</option>
                                <option value="23">Comercial</option>
                                <option value="8">Terreno</option>
                            </select></div>
                    </div>
                    <div class="col-md-4">
                        <div class="block-control active">
                            <div class="block-toggle"><span class="text text-1">Selecione a cidade ou bairro</span>
                                <div><span class="text text-2">Joinville</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="block-control">
                            <div class="block-toggle"><span class="text text-1">Faixa de Preço (R$)</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="block-control">
                                    <div class="block-toggle"><span class="text text-1">Quartos</span></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="block-items">
                                    <p class="text text-1">Vagas Garagem</p>
                                    <div class="actions"><button type="button" class="btn btn-small active">1+</button><button type="button" class="btn btn-small">2+</button><button type="button" class="btn btn-small">3+</button><button type="button" class="btn btn-small">4+</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6"><button type="button" class="btn toggleSearch">Filtros Avançados</button></div>
                    <div class="col-md-6">
                        <div class="block-submit"><button type="submit" class="btn btn-1">Buscar meu Imóvel</button></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="properties-master-container">
    <h3>
        Imóveis para comprar em Joinville<br>
        <span>1072 Resultados</span>
    </h3>

    <div class="properties-container">
		<?php
		// Conectar ao banco de dados
		$con = mysqli_connect("localhost", "root", "", "corretora");

		if (!$con) {
			die("Conexão falhou: " . mysqli_connect_error());
		}

		// Consulta SQL para buscar os anúncios e os dados dos imóveis
		$sql = "SELECT a.announcement_id, a.title, a.description, a.price, b.address, b.info_rooms, b.info_area_total, b.info_parking_space, b.neighborhood
				FROM announcement a
				JOIN build b ON a.build_id = b.build_id";
		$result = $con->query($sql);

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$announcement_id = $row['announcement_id'];

				// Buscar fotos do anúncio
				$photoResult = $con->query("SELECT photo FROM announcement_photos WHERE announcement_id = $announcement_id");
				$photos = [];
				while ($photoRow = $photoResult->fetch_assoc()) {
					$photos[] = $photoRow['photo'];
				}

				$photo_base64 = !empty($photos) ? trim($photos[0]) : ''; // Pegar a primeira foto

				echo '<a href="?page=imovel&announcement_id=' . $announcement_id . '" class="propertie">';
				echo '<div class="image" style="background-image: url(\'data:image/jpeg;base64,' . htmlspecialchars($photo_base64) . '\');">';
				echo '</div>';

				echo '<div class="infos-container">';
				echo '<div class="type">' . htmlspecialchars($row['title']) . '</div>';
				echo '<div class="neighborhood">' . htmlspecialchars($row['neighborhood']) . ' - Joinville</div>';
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
			echo '<p>Nenhum anúncio encontrado.</p>';
		}

		$con->close();
		?>
    </div>
</div>