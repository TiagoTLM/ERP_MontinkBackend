<?php
// cupons.php
$mysqli = new mysqli('localhost', 'root', '', 'erp_db');

if ($mysqli->connect_errno) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

$id = $_POST['id'] ?? null;
$codigo = $_POST['codigo'] ?? null;
$validade = $_POST['validade'] ?? null;
$minimo = $_POST['minimo'] ?? null;
$desconto = $_POST['desconto'] ?? null;

// Inserir ou atualizar cupom
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $codigo && $validade && $minimo !== null && $desconto !== null) {
    if ($id) {
        $stmt = $mysqli->prepare("UPDATE cupons SET codigo=?, validade=?, valor_minimo=?, desconto=? WHERE id=?");
        $stmt->bind_param('ssddi', $codigo, $validade, $minimo, $desconto, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $mysqli->prepare("INSERT INTO cupons (codigo, validade, valor_minimo, desconto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssdd', $codigo, $validade, $minimo, $desconto);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: view_cupons.php');
    exit;
}

// Excluir cupom
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM cupons WHERE id = $del_id");
    header('Location: view_cupons.php');
    exit;
}

