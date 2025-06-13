<?php
// webhook.php
header('Content-Type: application/json');
$mysqli = new mysqli('localhost', 'root', '', 'erp_db');

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com o banco']);
    exit;
}

// Recebe dados via GET ou POST
$id = $_REQUEST['id'] ?? null;
$status = $_REQUEST['status'] ?? null;

if (!$id || !$status) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros id e status são obrigatórios']);
    exit;
}

// Verifica se pedido existe
$stmt = $mysqli->prepare("SELECT id FROM pedidos WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Pedido não encontrado']);
    exit;
}

$stmt->close();

if (strtolower($status) === 'cancelado') {
    // Apaga pedido
    $stmt = $mysqli->prepare("DELETE FROM pedidos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['message' => "Pedido $id cancelado e removido"]);
} else {
    // Atualiza status
    $stmt = $mysqli->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['message' => "Pedido $id atualizado para status '$status'"]);
}

