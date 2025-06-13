<?php
session_start();

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

function calcular_subtotal() {
    $subtotal = 0;
    if (!isset($_SESSION['carrinho'])) return $subtotal;
    foreach ($_SESSION['carrinho'] as $item) {
        $subtotal += $item['quantidade'] * $item['preco'];
    }
    return $subtotal;
}

function calcular_frete($subtotal) {
    if ($subtotal > 200) return 0;
    if ($subtotal >= 52 && $subtotal <= 166.59) return 15;
    return 20;
}

function limpar_carrinho() {
    unset($_SESSION['carrinho']);
}

function obter_carrinho() {
    return $_SESSION['carrinho'] ?? [];
}

