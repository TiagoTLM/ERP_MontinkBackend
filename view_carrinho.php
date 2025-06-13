<?php
require_once 'carrinho.php';
$itens = obter_carrinho();
$subtotal = calcular_subtotal();
$frete = calcular_frete($subtotal);
$total = $subtotal + $frete;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Carrinho de Compras</h2>
    <?php if (count($itens) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Variação</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['produto_id']) ?></td>
                        <td><?= htmlspecialchars($item['variacao_id'] ?? '-') ?></td>
                        <td><?= $item['quantidade'] ?></td>
                        <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-4">
            <p><strong>Subtotal:</strong> R$ <?= number_format($subtotal, 2, ',', '.') ?></p>
            <p><strong>Frete:</strong> R$ <?= number_format($frete, 2, ',', '.') ?></p>
            <p><strong>Total:</strong> R$ <?= number_format($total, 2, ',', '.') ?></p>
        </div>
        <form action="finalizar_pedido.php" method="POST">
            <div class="mb-3">
                <label for="cep" class="form-label">CEP</label>
                <input type="text" name="cep" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Finalizar Pedido</button>
        </form>
    <?php else: ?>
        <p>Seu carrinho está vazio.</p>
    <?php endif; ?>
</div>
</body>
</html>

