<?php
// lista_produtos.php
$mysqli = new mysqli('localhost', 'root', '', 'mini_erp');
if ($mysqli->connect_errno) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Buscar produtos com quantidade total no estoque (soma das variações)
$sql = "
    SELECT p.id, p.nome, p.preco, 
           COALESCE(SUM(e.quantidade), 0) AS estoque_total
    FROM produtos p
    LEFT JOIN estoque e ON e.produto_id = p.id
    GROUP BY p.id, p.nome, p.preco
    ORDER BY p.nome
";

$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Lista de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Produtos</h2>
    <a href="editar_produto.php" class="btn btn-success mb-3">Novo Produto</a>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Preço (R$)</th>
                <th>Estoque Total</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($produto = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($produto['nome']) ?></td>
                        <td><?= number_format($produto['preco'], 2, ',', '.') ?></td>
                        <td><?= $produto['estoque_total'] ?></td>
                        <td>
                            <a href="editar_produto.php?id=<?= $produto['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="excluir_produto.php?id=<?= $produto['id'] ?>" 
                               onclick="return confirm('Confirma exclusão do produto <?= htmlspecialchars(addslashes($produto['nome'])) ?>?')" 
                               class="btn btn-danger btn-sm">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Nenhum produto cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

