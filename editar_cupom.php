<?php
$mysqli = new mysqli('localhost', 'root', '', 'erp_db');

if ($mysqli->connect_errno) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

$id = $_GET['id'] ?? null;
$cupom = null;

if ($id) {
    $stmt = $mysqli->prepare("SELECT * FROM cupons WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cupom = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title><?= $id ? 'Editar' : 'Novo' ?> Cupom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2><?= $id ? 'Editar' : 'Novo' ?> Cupom</h2>
    <form method="POST" action="cupons.php">
        <?php if ($id): ?>
            <input type="hidden" name="id" value="<?= $id ?>">
        <?php endif; ?>
        <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="text" id="codigo" name="codigo" required class="form-control" value="<?= htmlspecialchars($cupom['codigo'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="validade" class="form-label">Validade</label>
            <input type="date" id="validade" name="validade" required class="form-control" value="<?= htmlspecialchars($cupom['validade'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="minimo" class="form-label">Valor Mínimo</label>
            <input type="number" step="0.01" id="minimo" name="minimo" required class="form-control" value="<?= htmlspecialchars($cupom['valor_minimo'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="desconto" class="form-label">Desconto</label>
            <input type="number" step="0.01" id="desconto" name="desconto" required class="form-control" value="<?= htmlspecialchars($cupom['desconto'] ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary"><?= $id ? 'Atualizar' : 'Cadastrar' ?></button>
        <a href="view_cupons.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>
</body>
</html>

