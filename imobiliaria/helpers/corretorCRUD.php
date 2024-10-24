<?php
function updateAnnouncement($con, $announcement_id, $title, $description, $price, $build_id, $photosString, $removedPhotos) {
    // Atualizar o anúncio
    $stmt = $con->prepare("UPDATE announcement SET title = ?, description = ?, price = ?, build_id = ? WHERE announcement_id = ?");
    if ($stmt === false) {
        die('Erro na preparação da declaração SQL: ' . htmlspecialchars($con->error));
    }
    $stmt->bind_param("ssdii", $title, $description, $price, $build_id, $announcement_id);

    if ($stmt->execute() === false) {
        die('Erro na execução da declaração SQL: ' . htmlspecialchars($stmt->error));
    }
    $stmt->close();

    // Remover fotos existentes com base nos índices fornecidos
    if (!empty($removedPhotos)) {
        foreach ($removedPhotos as $index) {
            $photoStmt = $con->prepare("DELETE FROM announcement_photos WHERE announcement_id = ? AND photo = (SELECT photo FROM announcement_photos WHERE announcement_id = ? LIMIT 1 OFFSET ?)");
            if ($photoStmt === false) {
                die('Erro na preparação da declaração SQL para remoção de fotos: ' . htmlspecialchars($con->error));
            }
            $photoStmt->bind_param("iii", $announcement_id, $announcement_id, $index);
            if ($photoStmt->execute() === false) {
                die('Erro na execução da declaração SQL para remoção de fotos: ' . htmlspecialchars($photoStmt->error));
            }
            $photoStmt->close();
        }
    }

    // Inserir as novas fotos
    if (!empty($photosString)) {
        $photosArray = explode(',', $photosString);
        $photoStmt = $con->prepare("INSERT INTO announcement_photos (announcement_id, photo) VALUES (?, ?)");
        if ($photoStmt === false) {
            die('Erro na preparação da declaração SQL para fotos: ' . htmlspecialchars($con->error));
        }
        foreach ($photosArray as $photo) {
            $photoStmt->bind_param("is", $announcement_id, $photo);
            if ($photoStmt->execute() === false) {
                die('Erro na execução da declaração SQL para fotos: ' . htmlspecialchars($photoStmt->error));
            }
        }
        $photoStmt->close();
    }
}

// Função para excluir um anúncio
function deleteAnnouncement($con, $announcement_id) {
    // Remover fotos associadas ao anúncio
    $photoStmt = $con->prepare("DELETE FROM announcement_photos WHERE announcement_id = ?");
    if ($photoStmt === false) {
        die('Erro na preparação da declaração SQL para remoção de fotos: ' . htmlspecialchars($con->error));
    }
    $photoStmt->bind_param("i", $announcement_id);
    if ($photoStmt->execute() === false) {
        die('Erro na execução da declaração SQL para remoção de fotos: ' . htmlspecialchars($photoStmt->error));
    }
    $photoStmt->close();

    // Remover o anúncio
    $stmt = $con->prepare("DELETE FROM announcement WHERE announcement_id = ?");
    if ($stmt === false) {
        die('Erro na preparação da declaração SQL: ' . htmlspecialchars($con->error));
    }
    $stmt->bind_param("i", $announcement_id);
    if ($stmt->execute() === false) {
        die('Erro na execução da declaração SQL: ' . htmlspecialchars($stmt->error));
    }
    $stmt->close();
}

// Função para buscar todos os anúncios do corretor logado
function getAnnouncements($con, $user_id) {
    $sql = "SELECT a.announcement_id, a.title, a.description, a.price, b.address, a.build_id
            FROM announcement a
            JOIN build b ON a.build_id = b.build_id
            WHERE a.user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Também busca mas só 2 colunas
function getBuilds($con) {
    $sql = "SELECT build_id, address FROM build";
    $result = $con->query($sql);
    return $result;
}

// Função para buscar todos os imóveis disponíveis para seleção
function getFullBuilds($con) {
    $sql = "SELECT * FROM build";
    $result = $con->query($sql);
    return $result;
}
?>