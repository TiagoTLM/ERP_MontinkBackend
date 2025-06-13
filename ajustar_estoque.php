<?php
$mysqli = new mysqli('localhost', 'root', '', 'mini_erp');
if ($mysqli->connect_errno) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Processa o POST do ajuste
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estoque_id = filter_input(INPUT_POST, 'estoque_id', FILTER_VALIDATE_INT);
    $tipo_ajuste = $_POST['tipo_ajuste'] ?? '';
    $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);

    if (!$estoque_id || !in_array($tipo_ajuste, ['entrada', 'saida', 'correcao']) || $quantidade === false || $quantidade < 0) {
        $error = "Dados inválidos para o ajuste.";
    } else {
        // Buscar estoque atual
        $stmt = $mysqli->prepare("SELECT quantidade FROM estoque WHERE id = ?");
        $stmt->bind_param('i', $estoque_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $quant_atual = $row['quantidade'];

            if ($tipo_ajuste === 'entrada') {
                $novo_qtd = $quant_atual + $quantidade;
            } elseif ($tipo_ajuste === 'saida') {
                $novo_qtd = $quant_atual - $quantidade;
                if ($novo_qtd < 0) {
                    $error = "Saída maior que estoque disponível!";
                }
            } else { // correcao
                $novo_qtd = $quantidade;
            }

            if (!isset($error)) {
                $update = $mysqli->prepare("UPDATE estoque SET quantidade = ? WHERE id = ?");
                $update->bind_param('ii', $novo_qtd, $estoque_id);
                $update->execute();
                $update->close();
                $success = "Estoque atualizado com sucesso!";
            }
        } else {
            $error = "Estoque não encontrado.";
        }
        $stmt->close();
    }
}

// Buscar lista completa de estoque com produtos e variações
$query = "
SELECT 
  e.id AS estoque_id, 
  p.nome AS produto_nome, 
  v.descricao AS variacao_desc, 
  e.quantidade
FROM estoque e
LEFT JOIN produtos p ON e.produto_id = p.id
LEFT JOIN variacoes v ON e.variacao_id = v.id
ORDER BY p.nome, v.descricao";

$result_estoque = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Ajuste Manual de Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h2>Ajuste Manual de Estoque</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="estoque_id" class="form-label">Produto / Variação</label>
            <select id="estoque_id" name="estoque_id" class="form-select" required>
                <option value="">Selecione o estoque</option>
                <?php while ($row = $result_estoque->fetch_assoc()): ?>
                    <option value="<?= $row['estoque_id'] ?>">
                        <?= htmlspecialchars($row['produto_nome']) ?>
                        <?= $row['variacao_desc'] ? " - " . htmlspecialchars($row['variacao_desc']) : '' ?>
                        (Qtd atual: <?= $row['quantidade'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de ajuste</label>
            <select name="tipo_ajuste" class="form-select" required>
                <option value="entrada">Entrada (+)</option>
                <option value="saida">Saída (-)</option>
                <option value="correcao">Correção (quantidade final)</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" id="quantidade" name="quantidade" min="0" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar Estoque</button>
    </form>

    <hr />

    <h4>Estoque Atual</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Variação</th>
                <th>Quantidade</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Resetar ponteiro para reusar resultados da query
            $result_estoque->data_seek(0);
            while ($row = $result_estoque->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['produto_nome']) ?></td>
                    <td><?= htmlspecialchars($row['variacao_desc'] ?? '-') ?></td>
                    <td><?= $row['quantidade'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

