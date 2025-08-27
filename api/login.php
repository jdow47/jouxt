<?php
// Configurar headers para evitar problemas de CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Tratar requisições OPTIONS (preflight) - apenas se REQUEST_METHOD estiver definido
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Determinar o caminho correto para os arquivos
$script_dir = dirname(__FILE__);
$db_file = $script_dir . '/controles/db.php';
$login_file = $script_dir . '/controles/login.php';

// Verificar se os arquivos existem
if (!file_exists($db_file)) {
    http_response_code(500);
    echo json_encode([
        'title' => 'Erro de configuração do servidor.',
        'icon' => 'error'
    ]);
    exit();
}

if (!file_exists($login_file)) {
    http_response_code(500);
    echo json_encode([
        'title' => 'Erro de configuração do servidor.',
        'icon' => 'error'
    ]);
    exit();
}

require_once($db_file);
require_once($login_file);

try {
    if ($conexao = conectar_bd()) {
        if (isset($_GET['login'])) {
            if (empty($_GET['username'])) {
                $resposta = [
                    'title' => 'É necessário preencher o campo usuário.',
                    'icon' => 'error'
                ];
                echo json_encode($resposta);
            } elseif (empty($_GET['password'])) {
                $resposta = [
                    'title' => 'É necessário preencher o campo senha.',
                    'icon' => 'error'
                ];
                echo json_encode($resposta);
            } else {
                $info = login($_GET['username'], $_GET['password'], $conexao);
                echo json_encode($info);
            }
        } else {
            $resposta = [
                'title' => 'Parâmetro de login não fornecido.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
        }
    } else {
        $resposta = [
            'title' => 'Não foi possível conectar ao banco de dados.',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
    }
} catch (Exception $e) {
    error_log("Erro no login.php: " . $e->getMessage());
    http_response_code(500);
    $resposta = [
        'title' => 'Erro interno do servidor.',
        'icon' => 'error'
    ];
    echo json_encode($resposta);
}

$conexao = null;
?>

