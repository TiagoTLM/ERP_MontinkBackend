<?php
$mysqli = new mysqli('localhost', 'root', '', 'mini_erp');
if ($mysqli->connect_errno) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Atualizar status, se vier POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedido_id = filter_input(INPUT_POST, 'pedido_id', FILTER_VALIDATE_INT);
    $novo_status = $_POST['status'] ?? '';

    $status_validos = ['novo', 'processando', 'enviado', 'entregue', 'cancelado'];

    if ($pedido_id && in_array($novo_status, $status_validos)) {
        $stmt = $mysqli->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $novo_status, $pedido_id);
        $stmt->execute();
        $stmt->close();
        $msg = "Status do pedido #$pedido_id atualizado para '$novo_status'.";
    } else {
        $msgErro = "Dados inválidos para atualização.";
    }
}

// Buscar pedidos
$result = $mysqli->query("SELECT * FROM pedidos ORDER BY criado_em DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Lista de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h2>Pedidos</h2>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php elseif (!empty($msgErro)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($msgErro) ?></div>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Total (R$)</th>
                    <th>Frete (R$)</th>
                    <th>CEP</th>
                    <th>Status</th>
                    <th>Atualizar Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($pedido = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $pedido['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></td>
                        <td><?= number_format($pedido['total'], 2, ',', '.') ?></td>
                        <td><?= number_format($pedido['frete'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($pedido['cep']) ?></td>
                        <td>
                            <?php
                            $statusClass = [
                                'novo' => 'badge bg-primary',
                                'processando' => 'badge bg-info text-dark',
                                'enviado' => 'badge bg-warning text-dark',
                                'entregue' => 'badge bg-success',
                                'cancelado' => 'badge bg-danger'
                            ];
                            $classe = $statusClass[$pedido['status']] ?? 'badge bg-secondary';
                            ?>
                            <span class="<?= $classe ?>"><?= ucfirst($pedido['status']) ?></span>
                        </td>
                        <td>
                            <form method="POST" class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                <select name="status" class="form-select form-select-sm" required>
                                    <?php foreach ($statusClass as $status => $class): ?>
                                        <option value="<?= $status ?>" <?= $pedido['status'] === $status ? 'selected' : '' ?>>
                                            <?= ucfirst($status) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Atualizar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum pedido encontrado.</p>
    <?php endif; ?>
</div>
</body>
</html>

