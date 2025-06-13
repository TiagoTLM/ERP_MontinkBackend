<?php
session_start();

// Funções do carrinho 
function adicionar_ao_carrinho($produto_id, $variacao_id, $quantidade, $preco) {
    $key = $produto_id . '_' . ($variacao_id ?? '0');

    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    if (isset($_SESSION['carrinho'][$key])) {
        $_SESSION['carrinho'][$key]['quantidade'] += $quantidade;
    } else {
        $_SESSION['carrinho'][$key] = [
            'produto_id' => $produto_id,
            'variacao_id' => $variacao_id,
            'quantidade' => $quantidade,
            'preco' => $preco
        ];
    }
}

// Validar dados do POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
    $variacao_id = filter_input(INPUT_POST, 'variacao_id', FILTER_VALIDATE_INT);
    $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
    $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);

    // Variacao pode ser null
    if ($produto_id === false || $quantidade === false || $quantidade < 1 || $preco === false || $preco < 0) {
        die('Dados inválidos.');
    }

    adicionar_ao_carrinho($produto_id, $variacao_id, $quantidade, $preco);

    // Redirecionar para carrinho
    header('Location: carrinho.php');
    exit;
} else {
    die('Método inválido.');
}

