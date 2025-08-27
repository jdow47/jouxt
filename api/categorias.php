<?php
// Configurar headers para compatibilidade com cPanel
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Tratar requisições OPTIONS (preflight)
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Debug mode para cPanel - remover após corrigir
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/debug_categorias.log');

// Log de debug
file_put_contents('debug_categorias.log', date('Y-m-d H:i:s') . " - API categorias chamada\n", FILE_APPEND);

session_start();

// Determinar caminhos corretos para cPanel
$script_dir = dirname(__FILE__);
$db_file = $script_dir . '/controles/db.php';
$categorias_file = $script_dir . '/controles/categorias.php';
$checkLogout_file = $script_dir . '/controles/checkLogout.php';

// Verificar se os arquivos existem
if (!file_exists($db_file)) {
    file_put_contents('debug_categorias.log', date('Y-m-d H:i:s') . " - ERRO: db.php não encontrado\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Database configuration not found']);
    exit();
}

if (!file_exists($categorias_file)) {
    file_put_contents('debug_categorias.log', date('Y-m-d H:i:s') . " - ERRO: categorias.php não encontrado\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Categories functions not found']);
    exit();
}

if (!file_exists($checkLogout_file)) {
    file_put_contents('debug_categorias.log', date('Y-m-d H:i:s') . " - ERRO: checkLogout.php não encontrado\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Logout check not found']);
    exit();
}

require_once($db_file);
require_once($categorias_file);
require_once($checkLogout_file);

// Testar conexão com banco
$conexao = conectar_bd();
if (!$conexao) {
    file_put_contents('debug_categorias.log', date('Y-m-d H:i:s') . " - ERRO: Conexão com banco falhou\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

file_put_contents('debug_categorias.log', date('Y-m-d H:i:s') . " - Conexão OK\n", FILE_APPEND);

checkLogoutapi();

// Endpoint específico para listar categorias (compatibilidade com cPanel)
if (isset($_GET['listar_categorias']) || isset($_POST['listar_categorias'])) {
    try {
        $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
        
        if (!$admin_id) {
            echo json_encode([
                'error' => 'Admin não autenticado',
                'data' => []
            ]);
            exit();
        }

        // Parâmetros do DataTables
        $dados_requisicao = $_POST ?: $_GET;
        
        $colunas = [
            0 => 'id',
            1 => 'nome',
            2 => 'type',
            3 => 'is_adult',
            5 => 'bg',
            6 => 'ordem',
        ];

        // Query para contar total
        $query = "SELECT COUNT(id) AS qnt_categorias FROM categoria WHERE admin_id = :admin_id";
        
        if (!empty($dados_requisicao['search']['value'])) {
            $query .= " AND (id LIKE :id OR nome LIKE :nome OR type LIKE :type)";
        }

        $stmt = $conexao->prepare($query);
        $stmt->bindValue(':admin_id', $admin_id);
        
        if (!empty($dados_requisicao['search']['value'])) {
            $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
            $stmt->bindValue(':id', $valor_pesq);
            $stmt->bindValue(':nome', $valor_pesq);
            $stmt->bindValue(':type', $valor_pesq);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Query para listar dados
        $inicio = isset($dados_requisicao['start']) ? (int)$dados_requisicao['start'] : 0;
        $quantidade = isset($dados_requisicao['length']) ? (int)$dados_requisicao['length'] : 10;

        $list_query = "SELECT id, nome, type, is_adult, bg, COALESCE(ordem, 0) as ordem FROM categoria WHERE admin_id = :admin_id";

        if (!empty($dados_requisicao['search']['value'])) {
            $list_query .= " AND (id LIKE :id OR nome LIKE :nome OR type LIKE :type)";
        }

        // Ordenação
        if (isset($dados_requisicao['order'][0]['column']) && isset($colunas[$dados_requisicao['order'][0]['column']])) {
            $list_query .= " ORDER BY " . $colunas[$dados_requisicao['order'][0]['column']] . " " . $dados_requisicao['order'][0]['dir'] . " LIMIT :quantidade OFFSET :inicio";
        } else {
            $list_query .= " ORDER BY COALESCE(ordem, 0) ASC, id ASC LIMIT :quantidade OFFSET :inicio";
        }

        $list_stmt = $conexao->prepare($list_query);
        $list_stmt->bindValue(':admin_id', $admin_id);
        $list_stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $list_stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);

        if (!empty($dados_requisicao['search']['value'])) {
            $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
            $list_stmt->bindValue(':id', $valor_pesq);
            $list_stmt->bindValue(':type', $valor_pesq);
            $list_stmt->bindValue(':nome', $valor_pesq);
        }

        $list_stmt->execute();

        $dados = [];
        while ($row = $list_stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $adulto = ($is_adult == 1) ? 'Sim' : 'nao';

            $acoes = '';
            $acoes .= '<button class="btn btn-sm btn-outline-primary mr-1" onclick="moverCategoria(' . $id . ', \'up\')" title="Mover para cima"><i class="fas fa-arrow-up"></i></button>';
            $acoes .= '<button class="btn btn-sm btn-outline-primary mr-1" onclick="moverCategoria(' . $id . ', \'down\')" title="Mover para baixo"><i class="fas fa-arrow-down"></i></button>';
            $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/categorias.php", "edite_categorias", "' . $id . '")\'><i class="fa fa-edit"></i></a>';
            $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" data-placement="top" title="Apagar" onclick=\'modal_master("api/categorias.php", "delete_categorias", "' . $id . '", "name", "'.$nome.'")\'> <i class="far fa-trash-alt text-danger"></i></a>';

            $dados[] = [
                "category_id" => $id,
                "category_name" => $nome,
                "type" => $type,
                "is_adult" => $adulto,
                "bg" => $bg,
                "ordem" => isset($ordem) ? (int)$ordem : 0,
                "acao" => $acoes
            ];
        }

        $resultado = [
            "draw" => intval($dados_requisicao['draw'] ?? 1),
            "recordsTotal" => intval($result['qnt_categorias']),
            "recordsFiltered" => intval($result['qnt_categorias']),
            "data" => $dados
        ];

        file_put_contents('debug_categorias.log', date('Y-m-d H:i:s') . " - Categorias listadas: " . count($dados) . "\n", FILE_APPEND);
        echo json_encode($resultado);
        exit();

    } catch (Exception $e) {
        file_put_contents('debug_categorias.log', date('Y-m-d H:i:s') . " - ERRO: " . $e->getMessage() . "\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao listar categorias: ' . $e->getMessage()]);
        exit();
    }
}

        $stmt = $conexao->prepare($query);
        $stmt->bindValue(':admin_id', $admin_id);
        
        if (!empty($dados_requisicao['search']['value'])) {
            $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
            $stmt->bindValue(':id', $valor_pesq);
            $stmt->bindValue(':nome', $valor_pesq);
            $stmt->bindValue(':type', $valor_pesq);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Query para listar dados
        $inicio = isset($dados_requisicao['start']) ? (int)$dados_requisicao['start'] : 0;
        $quantidade = isset($dados_requisicao['length']) ? (int)$dados_requisicao['length'] : 10;

        $list_query = "SELECT id, nome, type, is_adult, bg, COALESCE(ordem, 0) as ordem FROM categoria WHERE admin_id = :admin_id";

        if (!empty($dados_requisicao['search']['value'])) {
            $list_query .= " AND (id LIKE :id OR nome LIKE :nome OR type LIKE :type)";
        }

        // Ordenação
        if (isset($dados_requisicao['order'][0]['column']) && isset($colunas[$dados_requisicao['order'][0]['column']])) {
            $list_query .= " ORDER BY " . $colunas[$dados_requisicao['order'][0]['column']] . " " . $dados_requisicao['order'][0]['dir'] . " LIMIT :quantidade OFFSET :inicio";
        } else {
            $list_query .= " ORDER BY COALESCE(ordem, 0) ASC, id ASC LIMIT :quantidade OFFSET :inicio";
        }

        $list_stmt = $conexao->prepare($list_query);
        $list_stmt->bindValue(':admin_id', $admin_id);
        $list_stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
        $list_stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);

        if (!empty($dados_requisicao['search']['value'])) {
            $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
            $list_stmt->bindValue(':id', $valor_pesq);
            $list_stmt->bindValue(':nome', $valor_pesq);
            $list_stmt->bindValue(':type', $valor_pesq);
        }

        $list_stmt->execute();

        $dados = [];
        while ($row = $list_stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $adulto = ($is_adult == 1) ? 'Sim' : 'nao';

            $acoes = '';
            $acoes .= '<button class="btn btn-sm btn-outline-primary mr-1" onclick="moverCategoria(' . $id . ', \'up\')" title="Mover para cima"><i class="fas fa-arrow-up"></i></button>';
            $acoes .= '<button class="btn btn-sm btn-outline-primary mr-1" onclick="moverCategoria(' . $id . ', \'down\')" title="Mover para baixo"><i class="fas fa-arrow-down"></i></button>';
            $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/categorias.php", "edite_categorias", "' . $id . '")\'><i class="fa fa-edit"></i></a>';
            $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" data-placement="top" title="Apagar" onclick=\'modal_master("api/categorias.php", "delete_categorias", "' . $id . '", "name", "'.$nome.'")\'> <i class="far fa-trash-alt text-danger"></i></a>';

            $dados[] = [
                "category_id" => $id,
                "category_name" => $nome,
                "type" => $type,
                "is_adult" => $adulto,
                "bg" => $bg,
                "ordem" => isset($ordem) ? (int)$ordem : 0,
                "acao" => $acoes
            ];
        }

        $resultado = [
            "draw" => intval($dados_requisicao['draw'] ?? 1),
            "recordsTotal" => intval($result['qnt_categorias']),
            "recordsFiltered" => intval($result['qnt_categorias']),
            "data" => $dados
        ];

        file_put_contents('debug_categorias.log', date('Y-m-d H:i:s') . " - Categorias listadas: " . count($dados) . "\n", FILE_APPEND);
        echo json_encode($resultado);
        exit();

    } catch (Exception $e) {
        file_put_contents('debug_categorias.log', date('Y-m-d H:i:s') . " - ERRO: " . $e->getMessage() . "\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao listar categorias: ' . $e->getMessage()]);
        exit();
    }
}

// Resto das funções existentes...
if (isset($_POST['delete_tudo'])) {
    $tabelas          = isset($_POST["delete_tudo"]) ? htmlspecialchars($_POST["delete_tudo"]) : "tudo";
    if (function_exists('delete_tudo')) {
        $delete_tudo = delete_tudo($tabelas);
        if (!$delete_tudo){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao Funçao deletar tudo.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_tudo);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['add_categoria'])) {

    if (function_exists('add_categoria')) {
        $add_categoria = add_categoria();
        if (!$add_categoria){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao Funçao add categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($add_categoria);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['confirme_add_categoria'])) {
    if (empty($_POST['nome'])) {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'É necessário preencher o campo nome.',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }

    $category_name          = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : null;
    $type                   = isset($_POST["tipo"]) ? htmlspecialchars($_POST["tipo"]) : null;
    $is_adult               = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : 0;
    $bg                     = isset($_POST["gb_ssiptv"]) ? htmlspecialchars($_POST["gb_ssiptv"]) : null;

    if (function_exists('confirme_add_categoria')) {
        $confirme_add_categoria = confirme_add_categoria($category_name, $type, $is_adult, $bg);
        if (!$confirme_add_categoria){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adicionar categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_add_categoria);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['delete_categorias'])) {
    $id = $_POST['delete_categorias'];
    $name = isset($_POST['name']) ? $_POST['name'] : null;

    if (function_exists('delete_categorias')) {
        $delete_categorias = delete_categorias($id, $name);
        if (!$delete_categorias){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_categorias);
            exit();
        }
    }else {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['confirme_delete_categorias'])) {
    $id = isset($_POST['confirme_delete_categorias']) ? (int) $_POST['confirme_delete_categorias'] : null;
    $name = isset($_POST['name']) ? (int) $_POST['name'] : null;

    if (function_exists('confirme_delete_categorias')) {
        $delete_categorias = confirme_delete_categorias($id, $name);
        if (!$delete_categorias){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_categorias);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['edite_categorias'])) {
    $id = $_POST['edite_categorias'];

    if (function_exists('edite_categorias')) {

        $edite_categorias = edite_categorias($id);
        if (!$edite_categorias){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($edite_categorias);
            exit();
        }
    }else {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['confirme_editar_categoria'])) {
    if (empty($_POST['nome'])) {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'É necessário preencher o campo nome.',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }

    $id            = isset($_POST["confirme_editar_categoria"]) ? (int) $_POST["confirme_editar_categoria"] : 0;
    $category_name = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : null;
    $type          = isset($_POST["tipo"]) ? htmlspecialchars($_POST["tipo"]) : null;
    $is_adult      = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : 0;
    $bg            = isset($_POST["gb_ssiptv"]) ? htmlspecialchars($_POST["gb_ssiptv"]) : null;

    if (function_exists('confirme_editar_categoria')) {
        $confirme_editar_categoria = confirme_editar_categoria($id, $category_name, $type, $is_adult, $bg);
        if (!$confirme_editar_categoria){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_editar_categoria);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

// Se nenhuma ação foi especificada, retornar erro
http_response_code(400);
echo json_encode(['error' => 'Ação não especificada']);
?>