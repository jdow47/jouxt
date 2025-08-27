<?php
/**
 * Arquivo de teste para verificar migração cPanel
 * Acesse: seudominio.com/teste_conexao.php
 */

// Incluir configuração
if (file_exists('config.php')) {
    require_once('config.php');
} else {
    // Configuração manual se config.php não existir
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'xtream');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DEBUG_MODE', true);
}

header('Content-Type: application/json; charset=utf-8');

$resultado = [
    'status' => 'testando',
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => []
];

// Teste 1: Verificar versão do PHP
$resultado['tests']['php_version'] = [
    'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'OK' : 'ERRO',
    'version' => PHP_VERSION,
    'required' => '7.4.0+'
];

// Teste 2: Verificar extensões necessárias
$extensoes_necessarias = ['pdo', 'pdo_mysql', 'mbstring', 'json'];
$extensoes_status = [];
foreach ($extensoes_necessarias as $ext) {
    $extensoes_status[$ext] = extension_loaded($ext) ? 'OK' : 'ERRO';
}
$resultado['tests']['extensions'] = $extensoes_status;

// Teste 3: Verificar conectividade com banco
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $conexao = new PDO($dsn, DB_USER, DB_PASS);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Teste de query simples
    $stmt = $conexao->query("SELECT COUNT(*) as total FROM categoria");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $resultado['tests']['database'] = [
        'status' => 'OK',
        'connection' => 'Conectado com sucesso',
        'categorias_count' => $total
    ];
} catch (Exception $e) {
    $resultado['tests']['database'] = [
        'status' => 'ERRO',
        'error' => $e->getMessage(),
        'config' => [
            'host' => DB_HOST,
            'database' => DB_NAME,
            'user' => DB_USER
        ]
    ];
}

// Teste 4: Verificar arquivos importantes
$arquivos_importantes = [
    'config.php' => 'Configuração centralizada',
    'api/controles/db.php' => 'Conexão com banco',
    'api/controles/checkLogout.php' => 'Controle de sessão',
    '.htaccess' => 'Configurações do servidor'
];

$arquivos_status = [];
foreach ($arquivos_importantes as $arquivo => $descricao) {
    $arquivos_status[$arquivo] = [
        'status' => file_exists($arquivo) ? 'OK' : 'ERRO',
        'description' => $descricao,
        'size' => file_exists($arquivo) ? filesize($arquivo) : 0
    ];
}
$resultado['tests']['files'] = $arquivos_status;

// Teste 5: Verificar permissões
$pastas_importantes = ['api', 'js', 'css', 'img'];
$permissoes_status = [];
foreach ($pastas_importantes as $pasta) {
    if (is_dir($pasta)) {
        $permissoes_status[$pasta] = [
            'status' => 'OK',
            'permissions' => substr(sprintf('%o', fileperms($pasta)), -4)
        ];
    } else {
        $permissoes_status[$pasta] = [
            'status' => 'ERRO',
            'error' => 'Pasta não encontrada'
        ];
    }
}
$resultado['tests']['permissions'] = $permissoes_status;

// Teste 6: Verificar configurações do servidor
$resultado['tests']['server'] = [
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    'http_host' => $_SERVER['HTTP_HOST'] ?? 'N/A',
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
    'php_sapi' => php_sapi_name()
];

// Determinar status geral
$erros = 0;
$sucessos = 0;

foreach ($resultado['tests'] as $teste) {
    if (is_array($teste)) {
        foreach ($teste as $item) {
            if (is_array($item) && isset($item['status'])) {
                if ($item['status'] === 'OK') {
                    $sucessos++;
                } elseif ($item['status'] === 'ERRO') {
                    $erros++;
                }
            }
        }
    }
}

$resultado['status'] = $erros === 0 ? 'SUCESSO' : 'ERRO';
$resultado['summary'] = [
    'total_tests' => $sucessos + $erros,
    'success' => $sucessos,
    'errors' => $erros
];

// Adicionar recomendações se houver erros
if ($erros > 0) {
    $resultado['recommendations'] = [
        'Verificar configurações do banco de dados',
        'Confirmar se todas as extensões PHP estão habilitadas',
        'Verificar permissões de arquivos e pastas',
        'Consultar logs de erro do cPanel'
    ];
}

echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?> 