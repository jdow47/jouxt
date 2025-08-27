<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once('./controles/db.php');
require_once('./controles/checkLogout.php');

checkLogoutapi();

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (!is_array($data)) {
        echo json_encode(['icon' => 'error', 'msg' => 'JSON inválido']);
        exit;
    }

    $validTypes = ['live','movie','series'];
    $admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 0;
    if ($admin_id <= 0) {
        echo json_encode(['icon' => 'error', 'msg' => 'Sessão inválida']);
        exit;
    }

    $con = conectar_bd();
    if (!$con) {
        echo json_encode(['icon' => 'error', 'msg' => 'Falha de conexão']);
        exit;
    }

    $con->beginTransaction();

    $stmt = $con->prepare('UPDATE categoria SET ordem = :ordem WHERE id = :id AND admin_id = :admin_id');

    foreach ($data as $type => $rows) {
        if (!in_array($type, $validTypes, true)) {
            continue;
        }
        if (!is_array($rows)) {
            continue;
        }
        foreach ($rows as $row) {
            $id = isset($row['id']) ? (int)$row['id'] : 0;
            $ordem = isset($row['ordem']) ? (int)$row['ordem'] : 0;
            if ($id <= 0 || $ordem < 0) continue;
            $stmt->bindValue(':ordem', $ordem, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    $con->commit();
    echo json_encode(['icon' => 'success', 'msg' => 'Ordem salva com sucesso']);
} catch (Exception $e) {
    if (isset($con) && $con->inTransaction()) {
        $con->rollBack();
    }
    echo json_encode(['icon' => 'error', 'msg' => 'Erro ao salvar ordem']);
} 