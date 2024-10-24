<?php
require __DIR__ . '/../helpers/corretorCRUD.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $announcement_id = $_POST['announcement_id'];
        deleteAnnouncement($con, $announcement_id);
    } elseif (isset($_POST['update'])) {
        $announcement_id = $_POST['announcement_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $build_id = $_POST['build_id'];

        // Processar upload de fotos e codificar em base64
        $photos = [];
        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
                $fileData = file_get_contents($tmpName);
                $base64Data = base64_encode($fileData);
                $photos[] = $base64Data;
            }
        }

        // Convertendo o array de fotos para string
        $photosString = implode(',', $photos);

        // Fotos removidas
        $removedPhotos = isset($_POST['removed_photos']) ? explode(',', $_POST['removed_photos']) : [];

        updateAnnouncement($con, $announcement_id, $title, $description, $price, $build_id, $photosString, $removedPhotos);
    }
}

// Buscar os anúncios do corretor logado
$user_id = $_SESSION['user_id'];
$announcements = getAnnouncements($con, $user_id);
$announcementsListUpdate = getAnnouncements($con, $user_id);

// Buscar fotos de todos os anúncios do corretor logado
$allPhotos = [];
$photoResult = $con->query("SELECT announcement_id, photo FROM announcement_photos WHERE announcement_id IN (SELECT announcement_id FROM announcement WHERE user_id = $user_id)");
while ($photoRow = $photoResult->fetch_assoc()) {
    $allPhotos[$photoRow['announcement_id']][] = $photoRow['photo'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Anúncios</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
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
            border-color: #d39e00;
        }
        .hidden {
            display: none;
        }
        .image-preview {
            position: relative;
            display: inline-block;
            margin: 5px;
        }
        .image-preview img {
            width: 100px;
            height: 100px;
        }
        .image-preview .delete-btn {
            position: absolute;
            top: 0;
            right: 0;
            background: red;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- <?php include '../includes/header.php'; ?> -->
    <div class="content">
        <div class="container">
            <h2>Meus Anúncios</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Endereço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $announcements->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['price']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="announcement_id" value="<?php echo $row['announcement_id']; ?>">
                                    <button type="submit" name="delete" class="btn-yellow">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Editar Anúncio</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="announcement_selector">Selecionar Anúncio</label>
                    <select id="announcement_selector" class="form-control">
                        <option value="">Selecione um anúncio</option>
                        <?php while ($row = $announcementsListUpdate->fetch_assoc()): ?>
                            <option value="<?php echo $row['announcement_id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-description="<?php echo htmlspecialchars($row['description']); ?>" data-price="<?php echo $row['price']; ?>" data-build_id="<?php echo $row['build_id']; ?>"><?php echo htmlspecialchars($row['title']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div id="editForm" class="hidden">
                    <input type="hidden" name="announcement_id" id="announcement_id">
                    <input type="hidden" name="removed_photos" id="removed_photos">
                    <div class="form-group">
                        <label for="title">Título</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea name="description" id="description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Preço</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="build_id">Imóvel</label>
                        <select name="build_id" id="build_id" class="form-control" required>
                            <?php $builds = getBuilds($con); while ($build = $builds->fetch_assoc()): ?>
                                <option value="<?php echo $build['build_id']; ?>"><?php echo htmlspecialchars($build['address']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="existing_photos">Fotos Existentes</label>
                        <div id="existing_photos"></div>
                        <?php foreach ($allPhotos as $announcement_id => $photos): ?>
                            <div id="photos_<?php echo $announcement_id; ?>" class="hidden">
                                <?php foreach ($photos as $index => $photo): ?>
                                    <div class="image-preview" data-index="<?php echo $index; ?>">
                                        <img src="data:image/jpeg;base64,<?php echo $photo; ?>">
                                        <button type="button" class="delete-btn" onclick="removeExistingPhoto(<?php echo $announcement_id; ?>, <?php echo $index; ?>)">X</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-group">
                        <label for="photos">Fotos</label>
                        <input type="file" name="photos[]" id="photos" class="form-control" accept="image/*" multiple>
                        <div id="new_photos_preview"></div>
                    </div>
                    <button type="submit" name="update" class="btn-yellow">Atualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const announcementSelector = document.getElementById('announcement_selector');
            const editForm = document.getElementById('editForm');
            const titleInput = document.getElementById('title');
            const descriptionTextarea = document.getElementById('description');
            const priceInput = document.getElementById('price');
            const buildIdSelect = document.getElementById('build_id');
            const announcementIdInput = document.getElementById('announcement_id');
			const removedPhotosInput = document.getElementById('removed_photos');
            const photosInput = document.getElementById('photos');
            const newPhotosPreview = document.getElementById('new_photos_preview');
            let removedPhotos = [];

            announcementSelector.addEventListener('change', function () {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    titleInput.value = selectedOption.getAttribute('data-title');
                    descriptionTextarea.value = selectedOption.getAttribute('data-description');
                    priceInput.value = selectedOption.getAttribute('data-price');
                    buildIdSelect.value = selectedOption.getAttribute('data-build_id');
                    announcementIdInput.value = this.value;
                    editForm.classList.remove('hidden');

                    // Mostrar fotos correspondentes ao anúncio selecionado
                    document.querySelectorAll('[id^="photos_"]').forEach(div => div.classList.add('hidden'));
                    document.getElementById('photos_' + this.value).classList.remove('hidden');
                } else {
                    editForm.classList.add('hidden');
                    document.querySelectorAll('[id^="photos_"]').forEach(div => div.classList.add('hidden'));
                }
            });

            photosInput.addEventListener('change', function () {
                newPhotosPreview.innerHTML = '';
                Array.from(this.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'image-preview';
                        imgDiv.innerHTML = `
                            <img src="${e.target.result}" />
                            <button type="button" class="delete-btn" onclick="removeNewPhoto(${index})">X</button>
                        `;
                        newPhotosPreview.appendChild(imgDiv);
                    };
                    reader.readAsDataURL(file);
                });
            });

            window.removeExistingPhoto = function(announcementId, index) {
                const photoDiv = document.querySelector(`#photos_${announcementId} .image-preview[data-index="${index}"]`);
                if (photoDiv) {
                    photoDiv.remove();
                    removedPhotos.push(index);
                    removedPhotosInput.value = removedPhotos.join(',');
                }
            };

            window.removeNewPhoto = function(index) {
                const photoDiv = document.querySelector(`#new_photos_preview .image-preview:nth-child(${index + 1})`);
                if (photoDiv) {
                    photoDiv.remove();
                    // Remove the file from the input
                    const dt = new DataTransfer();
                    Array.from(photosInput.files).forEach((file, i) => {
                        if (i !== index) {
                            dt.items.add(file);
                        }
                    });
                    photosInput.files = dt.files;
                }
            };
        });
    </script>
</body>
</html>