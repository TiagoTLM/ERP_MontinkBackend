<?php
$mysqli = new mysqli('localhost', 'root', '', 'mini_erp');
if ($mysqli->connect_errno) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID inválido.");
}

// Iniciar transação
$mysqli->begin_transaction();

try {
    // Verificar se o produto existe
    $stmt = $mysqli->prepare("SELECT nome FROM produtos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        throw new Exception("Produto não encontrado.");
    }
    $produto = $res->fetch_assoc();
    $stmt->close();

    // Deletar estoque das variações do produto
    $stmt = $mysqli->prepare("DELETE e FROM estoque e JOIN variacoes v ON e.variacao_id = v.id WHERE v.produto_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Deletar variações do produto
    $stmt = $mysqli->prepare("DELETE FROM variacoes WHERE produto_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Deletar o produto
    $stmt = $mysqli->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    $mysqli->commit();

    // Redirecionar pra lista com sucesso
    header("Location: lista_produtos.php?msg=Produto+\"".urlencode($produto['nome'])."\"+excluído+com+sucesso");
    exit;

} catch (Exception $e) {
    $mysqli->rollback();
    die("Erro ao excluir produto: " . $e->getMessage());
}

