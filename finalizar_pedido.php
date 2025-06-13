<?php
// finalizar_pedido.php
session_start();
require_once 'carrinho.php';

$mysqli = new mysqli('localhost', 'root', '', 'erp_db');

if ($mysqli->connect_errno) {
    die("Erro na conexão com o banco: " . $mysqli->connect_error);
}

$itens = obter_carrinho();
$subtotal = calcular_subtotal();
$frete = calcular_frete($subtotal);
$total = $subtotal + $frete;

// Aplica cupom se existir
$desconto = 0;
if (isset($_SESSION['cupom_aplicado'])) {
    $desconto = $_SESSION['cupom_aplicado']['desconto'];
    $total -= $desconto;
}

$cep = trim($_POST['cep'] ?? '');
$email = trim($_POST['email'] ?? '');

// Validações simples
if (empty($cep) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('CEP ou e-mail inválido.');
}

// Insere pedido
$stmt = $mysqli->prepare("INSERT INTO pedidos (total, frete, cep, email) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ddss', $total, $frete, $cep, $email);
$stmt->execute();
$pedido_id = $stmt->insert_id;
$stmt->close();

// Envio de e-mail
$mensagem = "Obrigado pela compra!\nPedido nº: $pedido_id\nTotal: R$ " . number_format($total, 2, ',', '.');
mail($email, "Confirmação de Pedido #$pedido_id", $mensagem);

// Webhook
$webhook_url = "https://dominioficticiotlm.com/webhook.php"; 
$status = 'confirmado';
@file_get_contents($webhook_url . "?id=$pedido_id&status=$status");

// Limpa carrinho e cupom
limpar_carrinho();
unset($_SESSION['cupom_aplicado']);

echo "<h2>Pedido finalizado com sucesso!</h2>";
echo "<p>Enviamos os detalhes para o e-mail informado.</p>";
echo "<a href='view_produto.php'>Voltar para Produtos</a>";

