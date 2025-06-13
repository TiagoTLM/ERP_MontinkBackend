<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Cadastro de Produto</h2>
    <form action="salvar_produto.php" method="POST">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Produto</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="preco" class="form-label">Preço</label>
            <input type="number" step="0.01" name="preco" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Variações</label>
            <div id="variacoes">
                <div class="input-group mb-2">
                    <input type="text" name="variacoes[]" class="form-control" placeholder="Tamanho, Cor, etc.">
                    <input type="number" name="estoques[]" class="form-control" placeholder="Quantidade em estoque">
                </div>
            </div>
            <button type="button" class="btn btn-secondary" onclick="adicionarVariacao()">Adicionar variação</button>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Produto</button>
    </form>
</div>

<script>
    function adicionarVariacao() {
        const div = document.createElement('div');
        div.classList.add('input-group', 'mb-2');
        div.innerHTML = `
            <input type="text" name="variacoes[]" class="form-control" placeholder="Tamanho, Cor, etc.">
            <input type="number" name="estoques[]" class="form-control" placeholder="Quantidade em estoque">
        `;
        document.getElementById('variacoes').appendChild(div);
    }
</script>
</body>
</html>

