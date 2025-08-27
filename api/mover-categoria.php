<?php
session_start();
header('Content-Type: application/json');
require_once('./controles/db.php');

$id = (int)$_POST['id'];
$direcao = $_POST['direcao'];
$admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 0;

if ($admin_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Sessão inválida']);
    exit;
}

$con = conectar_bd();
if (!$con) {
    echo json_encode(['success' => false, 'message' => 'Falha de conexão']);
    exit;
}

try {
    $con->beginTransaction();

    // Buscar categoria atual
    $stmt = $con->prepare('SELECT id, nome, type, COALESCE(ordem, 0) as ordem FROM categoria WHERE id = ? AND admin_id = ?');
    $stmt->execute([$id, $admin_id]);
    $categoria_atual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$categoria_atual) {
        $con->rollBack();
        echo json_encode(['success' => false, 'message' => 'Categoria não encontrada']);
        exit;
    }

    $ordem_atual = (int)$categoria_atual['ordem'];
    $tipo = $categoria_atual['type'];

    // Buscar categoria adjacente
    if ($direcao == 'up') {
        $stmt = $con->prepare('SELECT id, COALESCE(ordem, 0) as ordem FROM categoria WHERE admin_id = ? AND type = ? AND COALESCE(ordem, 0) < ? ORDER BY COALESCE(ordem, 0) DESC LIMIT 1');
        $stmt->execute([$admin_id, $tipo, $ordem_atual]);
    } else {
        $stmt = $con->prepare('SELECT id, COALESCE(ordem, 0) as ordem FROM categoria WHERE admin_id = ? AND type = ? AND COALESCE(ordem, 0) > ? ORDER BY COALESCE(ordem, 0) ASC LIMIT 1');
        $stmt->execute([$admin_id, $tipo, $ordem_atual]);
    }
    
    $categoria_adjacente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$categoria_adjacente) {
        $con->rollBack();
        echo json_encode(['success' => false, 'message' => 'Não é possível mover mais nesta direção']);
        exit;
    }

    $ordem_adjacente = (int)$categoria_adjacente['ordem'];
    $id_adjacente = (int)$categoria_adjacente['id'];

    // Trocar posições
    $stmt1 = $con->prepare('UPDATE categoria SET ordem = ? WHERE id = ? AND admin_id = ?');
    $stmt2 = $con->prepare('UPDATE categoria SET ordem = ? WHERE id = ? AND admin_id = ?');

    // Atualizar categoria atual
    $stmt1->execute([$ordem_adjacente, $id, $admin_id]);

    // Atualizar categoria adjacente
    $stmt2->execute([$ordem_atual, $id_adjacente, $admin_id]);

    $con->commit();
    
    echo json_encode(['success' => true, 'message' => 'Ordem atualizada com sucesso']);

} catch (Exception $e) {
    if (isset($con) && $con->inTransaction()) {
        $con->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Erro ao mover categoria: ' . $e->getMessage()]);
} 