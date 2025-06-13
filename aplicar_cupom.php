<?php
// aplicar_cupom.php
session_start();

$mysqli = new mysqli('localhost', 'root', '', 'erp_db');

if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

$codigo = trim($_POST['codigo'] ?? '');

if ($codigo === '') {
    $_SESSION['cupom_erro'] = 'Código do cupom não pode estar vazio.';
    header('Location: view_carrinho.php');
    exit;
}

// Busca o cupom
$stmt = $mysqli->prepare("SELECT * FROM cupons WHERE codigo = ? AND validade >= CURDATE()");
$stmt->bind_param('s', $codigo);
$stmt->execute();
$result = $stmt->get_result();
$cupom = $result->fetch_assoc();
$stmt->close();

if (!$cupom) {
    $_SESSION['cupom_erro'] = 'Cupom inválido ou expirado.';
    header('Location: view_carrinho.php');
    exit;
}

// Calcula o subtotal do carrinho
require_once 'carrinho.php';
$subtotal = calcular_subtotal();

// Verifica o valor mínimo
if ($subtotal < $cupom['valor_minimo']) {
    $_SESSION['cupom_erro'] = 'Valor mínimo para este cupom: R$ ' . number_format($cupom['valor_minimo'], 2, ',', '.');
    header('Location: view_carrinho.php');
    exit;
}

// Tudo certo: salva o cupom na sessão
$_SESSION['cupom_aplicado'] = [
    'codigo' => $cupom['codigo'],
    'desconto' => $cupom['desconto']
];

header('Location: view_carrinho.php');
exit;

