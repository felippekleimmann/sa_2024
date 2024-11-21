<?php
$con = mysqli_connect("localhost", "root", "", "corretora");

if (!$con) {
    die("Conexão falhou: " . mysqli_connect_error());
}

$announcement_id = $_GET['announcement_id'];
$photoResult = $con->query("SELECT photo FROM announcement_photos WHERE announcement_id = $announcement_id");
$photos = [];
while ($photoRow = $photoResult->fetch_assoc()) {
    $photos[] = $photoRow['photo'];
}

echo json_encode($photos);
?>