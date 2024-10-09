<?php
$isAdmin = $_SESSION['tipo'] == 1;
?>

<!-- <?php if ($isAdmin): ?>
    é um admin
<?php endif; ?>

<?php if (!$isAdmin): ?>
    é um corretor
<?php endif; ?> -->

<div class="page-admin-style">

    <?php if ($isAdmin): ?>
        <table>
            <thead>
                <th>Titulo 1</th>
                <th>Titulo 2</th>
                <th>Titulo 3</th>
            </thead>
            <tbody>
                <td>Conteudo Admin 1</td>
                <td>Conteudo Admin 2</td>
                <td>Conteudo Admin 3</td>
            </tbody>
        </table>

        <br><br><br>
    <?php endif; ?>

    <table>
        <thead>
            <th>Titulo 1</th>
            <th>Titulo 2</th>
            <th>Titulo 3</th>
        </thead>
        <tbody>
            <td>Conteudo Corretor 1</td>
            <td>Conteudo Corretor 2</td>
            <td>Conteudo Corretor 3</td>
        </tbody>
    </table>

</div>