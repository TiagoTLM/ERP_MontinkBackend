<?php
$mysqli = new mysqli('localhost', 'root', '', 'erp_db');

if ($mysqli->connect_errno) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

$cupons = $mysqli->query("SELECT * FROM cupons ORDER BY validade DESC");

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Cupons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Cupons</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Código</th>
                <th>Validade</th>
                <th>Valor Mínimo (R$)</th>
                <th>Desconto (R$)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($cupom = $cupons->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($cupom['codigo']) ?></td>
                    <td><?= htmlspecialchars($cupom['validade']) ?></td>
                    <td><?= number_format($cupom['valor_minimo'], 2, ',', '.') ?></td>
                    <td><?= number_format($cupom['desconto'], 2, ',', '.') ?></td>
                    <td>
                        <a href="editar_cupom.php?id=<?= $cupom['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="cupons.php?delete=<?= $cupom['id'] ?>" onclick="return confirm('Confirma exclusão?')" class="btn btn-sm btn-danger">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="editar_cupom.php" class="btn btn-success">Novo Cupom</a>
</div>
</body>
</html>

