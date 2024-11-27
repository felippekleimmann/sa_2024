<?php
require __DIR__ . '/../helpers/corretorCRUD.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$con = mysqli_connect("localhost", "root", "", "corretora");

ob_start();

// Verificar se o corretor está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ?page=inicial");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtenha os dados do formulário
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $build_id = $_POST['build_id'];
    $isHighlighted = isset($_POST['isHighlighted']) ? 1 : 0;  // Capture o valor do checkbox

    // Prepare a declaração SQL
    $stmt = $con->prepare("INSERT INTO announcement (title, description, price, build_id, user_id, isHighlighted) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die('Erro na preparação da declaração SQL: ' . htmlspecialchars($con->error));
    }

    // Vincule os parâmetros à declaração SQL
    $stmt->bind_param("ssdiii", $title, $description, $price, $build_id, $_SESSION['user_id'], $isHighlighted);

    // Execute a declaração SQL
    if ($stmt->execute()) {
        $announcement_id = $stmt->insert_id;
        $message = "Anúncio criado com sucesso!";

        // Processar upload de fotos e codificar em base64
        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
                $fileData = file_get_contents($tmpName);
                $base64Data = base64_encode($fileData);

                $photoStmt = $con->prepare("INSERT INTO announcement_photos (announcement_id, photo) VALUES (?, ?)");
                if ($photoStmt === false) {
                    die('Erro na preparação da declaração SQL para fotos: ' . htmlspecialchars($con->error));
                }
                $photoStmt->bind_param("is", $announcement_id, $base64Data);
                $photoStmt->execute();
                $photoStmt->close();
            }
        }
    } else {
        $message = "Erro ao criar anúncio: " . htmlspecialchars($stmt->error);
    }

    // Feche a declaração SQL
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Anúncio</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Criar Anúncio</h2>
        <?php if (isset($message)): ?>
            <div class="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
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
                <label for="photos">Fotos</label>
                <input type="file" name="photos[]" id="photos" class="form-control" multiple>
            </div>
			<div class="form-group">
				<input type="checkbox" name="isHighlighted" id="isHighlighted">
				<label for="isHighlighted">Destacar Anúncio</label>
			</div>
            <button type="submit" class="btn-yellow">Criar Anúncio</button>
        </form>
    </div>
</body>
</html>