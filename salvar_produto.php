<?php
// salvar_produto.php
require_once 'controller_produto.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $preco = $_POST['preco'] ?? 0;
    $variacoes = $_POST['variacoes'] ?? [];
    $estoques = $_POST['estoques'] ?? [];

    if ($nome && $preco > 0) {
        $produto_id = salvar_produto($nome, $preco, $variacoes, $estoques);
        header("Location: view_produto.php?sucesso=1&produto_id=$produto_id");
        exit;
    } else {
        echo "Dados inválidos. Verifique o nome e o preço.";
    }
} else {
    echo "Método inválido.";
}

