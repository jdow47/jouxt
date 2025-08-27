<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once('./controles/db.php');
require_once('./controles/checkLogout.php');

checkLogoutapi();

$method = $_SERVER['REQUEST_METHOD'];
$con = conectar_bd();
if (!$con) {
    echo json_encode(['icon' => 'error', 'msg' => 'Falha de conexão']);
    exit;
}

$admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 0;
if ($admin_id <= 0) {
    echo json_encode(['icon' => 'error', 'msg' => 'Sessão inválida']);
    exit;
}

function validarTipo($tipo) {
    return in_array($tipo, ['live','movie','series'], true);
}

try {
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $stmt = $GLOBALS['con']->prepare('SELECT id, nome, type, is_adult, bg FROM categoria WHERE id = :id AND admin_id = :admin_id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':admin_id', $GLOBALS['admin_id'], PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['icon' => $row ? 'success' : 'error', 'data' => $row]);
            exit;
        }
        // listar todas por tipo
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;
        if ($tipo && !validarTipo($tipo)) {
            echo json_encode(['icon' => 'error', 'msg' => 'Tipo inválido']);
            exit;
        }
        $sql = 'SELECT id, nome, type, is_adult, bg, ordem FROM categoria WHERE admin_id = :admin_id';
        if ($tipo) $sql .= ' AND type = :type';
        $sql .= ' ORDER BY ordem ASC, id ASC';
        $stmt = $GLOBALS['con']->prepare($sql);
        $stmt->bindValue(':admin_id', $GLOBALS['admin_id'], PDO::PARAM_INT);
        if ($tipo) $stmt->bindValue(':type', $tipo, PDO::PARAM_STR);
        $stmt->execute();
        echo json_encode(['icon' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    $payload = $_POST;
    if (empty($payload)) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (is_array($json)) $payload = $json;
    }

    if ($method === 'POST') {
        $nome = isset($payload['nome']) ? trim($payload['nome']) : '';
        $tipo = isset($payload['tipo']) ? $payload['tipo'] : '';
        $is_adult = isset($payload['is_adult']) ? (int)$payload['is_adult'] : 0;
        $bg = isset($payload['bg']) ? trim($payload['bg']) : null;
        if (strlen($nome) < 3) { echo json_encode(['icon'=>'error','msg'=>'Nome mínimo 3 caracteres']); exit; }
        if (!validarTipo($tipo)) { echo json_encode(['icon'=>'error','msg'=>'Tipo inválido']); exit; }
        // duplicidade por tipo
        $chk = $con->prepare('SELECT COUNT(*) c FROM categoria WHERE admin_id = :admin_id AND type = :type AND LOWER(nome) = LOWER(:nome)');
        $chk->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $chk->bindValue(':type', $tipo, PDO::PARAM_STR);
        $chk->bindValue(':nome', $nome, PDO::PARAM_STR);
        $chk->execute();
        if ((int)$chk->fetchColumn() > 0) { echo json_encode(['icon'=>'error','msg'=>'Já existe categoria com este nome neste tipo']); exit; }
        // próxima ordem
        $next = $con->prepare('SELECT COALESCE(MAX(ordem),0)+1 FROM categoria WHERE admin_id = :admin_id AND type = :type');
        $next->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $next->bindValue(':type', $tipo, PDO::PARAM_STR);
        $next->execute();
        $ordem = (int)$next->fetchColumn();
        $ins = $con->prepare('INSERT INTO categoria (nome, type, is_adult, bg, admin_id, ordem) VALUES (:nome, :type, :is_adult, :bg, :admin_id, :ordem)');
        $ins->bindValue(':nome', $nome);
        $ins->bindValue(':type', $tipo);
        $ins->bindValue(':is_adult', $is_adult, PDO::PARAM_INT);
        $ins->bindValue(':bg', $bg);
        $ins->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $ins->bindValue(':ordem', $ordem, PDO::PARAM_INT);
        $ins->execute();
        echo json_encode(['icon'=>'success','msg'=>'Categoria criada']);
        exit;
    }

    if ($method === 'PUT' || ($method === 'PATCH')) {
        $id = isset($payload['id']) ? (int)$payload['id'] : 0;
        $nome = isset($payload['nome']) ? trim($payload['nome']) : '';
        $tipo = isset($payload['tipo']) ? $payload['tipo'] : '';
        $is_adult = isset($payload['is_adult']) ? (int)$payload['is_adult'] : 0;
        $bg = isset($payload['bg']) ? trim($payload['bg']) : null;
        if ($id <= 0) { echo json_encode(['icon'=>'error','msg'=>'ID inválido']); exit; }
        if (strlen($nome) < 3) { echo json_encode(['icon'=>'error','msg'=>'Nome mínimo 3 caracteres']); exit; }
        if (!validarTipo($tipo)) { echo json_encode(['icon'=>'error','msg'=>'Tipo inválido']); exit; }
        $chk = $con->prepare('SELECT COUNT(*) c FROM categoria WHERE admin_id = :admin_id AND type = :type AND LOWER(nome) = LOWER(:nome) AND id <> :id');
        $chk->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $chk->bindValue(':type', $tipo, PDO::PARAM_STR);
        $chk->bindValue(':nome', $nome, PDO::PARAM_STR);
        $chk->bindValue(':id', $id, PDO::PARAM_INT);
        $chk->execute();
        if ((int)$chk->fetchColumn() > 0) { echo json_encode(['icon'=>'error','msg'=>'Já existe categoria com este nome neste tipo']); exit; }
        $upd = $con->prepare('UPDATE categoria SET nome = :nome, type = :type, is_adult = :is_adult, bg = :bg WHERE id = :id AND admin_id = :admin_id');
        $upd->bindValue(':nome', $nome);
        $upd->bindValue(':type', $tipo);
        $upd->bindValue(':is_adult', $is_adult, PDO::PARAM_INT);
        $upd->bindValue(':bg', $bg);
        $upd->bindValue(':id', $id, PDO::PARAM_INT);
        $upd->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $upd->execute();
        echo json_encode(['icon'=>'success','msg'=>'Categoria atualizada']);
        exit;
    }

    if ($method === 'DELETE') {
        parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
        $id = isset($qs['id']) ? (int)$qs['id'] : 0;
        if ($id <= 0) { echo json_encode(['icon'=>'error','msg'=>'ID inválido']); exit; }
        $del = $con->prepare('DELETE FROM categoria WHERE id = :id AND admin_id = :admin_id');
        $del->bindValue(':id', $id, PDO::PARAM_INT);
        $del->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
        $del->execute();
        echo json_encode(['icon'=>'success','msg'=>'Categoria removida']);
        exit;
    }

    echo json_encode(['icon'=>'error','msg'=>'Método não suportado']);
} catch (Exception $e) {
    echo json_encode(['icon'=>'error','msg'=>'Erro no processamento']);
} 