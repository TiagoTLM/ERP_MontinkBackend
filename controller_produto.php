<?php
require_once 'db.php';

function salvar_produto($nome, $preco, $variacoes = [], $estoques = []) {
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");
    $stmt->execute([$nome, $preco]);
    $produto_id = $pdo->lastInsertId();

    foreach ($variacoes as $i => $descricao) {
        $stmt = $pdo->prepare("INSERT INTO variacoes (produto_id, descricao) VALUES (?, ?)");
        $stmt->execute([$produto_id, $descricao]);
        $variacao_id = $pdo->lastInsertId();

        $quantidade = $estoques[$i] ?? 0;
        $stmt = $pdo->prepare("INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES (?, ?, ?)");
        $stmt->execute([$produto_id, $variacao_id, $quantidade]);
    }

    if (empty($variacoes)) {
        $quantidade = $estoques[0] ?? 0;
        $stmt = $pdo->prepare("INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES (?, NULL, ?)");
        $stmt->execute([$produto_id, $quantidade]);
    }

    return $produto_id;
}

function atualizar_produto($id, $nome, $preco) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ? WHERE id = ?");
    $stmt->execute([$nome, $preco, $id]);
}

function atualizar_estoque($produto_id, $estoques) {
    global $pdo;
    foreach ($estoques as $variacao_id => $quantidade) {
        $stmt = $pdo->prepare("UPDATE estoque SET quantidade = ? WHERE produto_id = ? AND variacao_id = ?");
        $stmt->execute([$quantidade, $produto_id, $variacao_id]);
    }
}

function listar_produtos() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM produtos");
    return $stmt->fetchAll();
}

function buscar_variacoes($produto_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM variacoes WHERE produto_id = ?");
    $stmt->execute([$produto_id]);
    return $stmt->fetchAll();
}

function buscar_estoque($produto_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM estoque WHERE produto_id = ?");
    $stmt->execute([$produto_id]);
    return $stmt->fetchAll();
}

