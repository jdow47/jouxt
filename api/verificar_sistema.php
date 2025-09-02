<?php
// Script de verificação do sistema de categorias
// Execute este arquivo diretamente no navegador para testar

session_start();
header('Content-Type: application/json; charset=utf-8');

// CORREÇÃO: Caminhos corretos usando __DIR__
require_once(__DIR__ . '/controles/db.php');

try {
    echo json_encode([
        'status' => 'iniciando',
        'message' => 'Verificando sistema de categorias...'
    ], JSON_PRETTY_PRINT);
    echo "\n\n";

    // 1. Testar conexão com banco
    $conexao = conectar_bd();
    if (!$conexao) {
        throw new Exception('Falha na conexão com banco de dados');
    }
    
    echo json_encode([
        'teste' => 'conexao_banco',
        'status' => 'sucesso',
        'message' => 'Conexão com banco estabelecida'
    ], JSON_PRETTY_PRINT);
    echo "\n\n";

    // 2. Verificar se tabela categoria existe
    $stmt = $conexao->prepare("SHOW TABLES LIKE 'categoria'");
    $stmt->execute();
    $table_exists = $stmt->fetch();
    
    if (!$table_exists) {
        throw new Exception('Tabela categoria não encontrada');
    }
    
    echo json_encode([
        'teste' => 'tabela_categoria',
        'status' => 'sucesso',
        'message' => 'Tabela categoria encontrada'
    ], JSON_PRETTY_PRINT);
    echo "\n\n";

    // 3. Verificar estrutura da tabela categoria
    $stmt = $conexao->prepare("DESCRIBE categoria");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $required_columns = ['id', 'nome', 'type', 'is_adult', 'ordem'];
    $existing_columns = array_column($columns, 'Field');
    $missing_columns = array_diff($required_columns, $existing_columns);
    
    if (!empty($missing_columns)) {
        echo json_encode([
            'teste' => 'estrutura_tabela',
            'status' => 'aviso',
            'message' => 'Colunas ausentes: ' . implode(', ', $missing_columns),
            'colunas_existentes' => $existing_columns,
            'colunas_necessarias' => $required_columns
        ], JSON_PRETTY_PRINT);
        echo "\n\n";
        
        // Adicionar coluna ordem se não existir
        if (in_array('ordem', $missing_columns)) {
            try {
                $conexao->exec("ALTER TABLE categoria ADD COLUMN ordem INT DEFAULT 0");
                echo json_encode([
                    'teste' => 'adicionar_coluna_ordem',
                    'status' => 'sucesso',
                    'message' => 'Coluna ordem adicionada com sucesso'
                ], JSON_PRETTY_PRINT);
                echo "\n\n";
            } catch (Exception $e) {
                echo json_encode([
                    'teste' => 'adicionar_coluna_ordem',
                    'status' => 'erro',
                    'message' => 'Erro ao adicionar coluna ordem: ' . $e->getMessage()
                ], JSON_PRETTY_PRINT);
                echo "\n\n";
            }
        }
    } else {
        echo json_encode([
            'teste' => 'estrutura_tabela',
            'status' => 'sucesso',
            'message' => 'Todas as colunas necessárias estão presentes',
            'colunas' => $existing_columns
        ], JSON_PRETTY_PRINT);
        echo "\n\n";
    }

    // 4. Testar consulta de listagem de categorias
    $stmt = $conexao->prepare("SELECT id, nome, type, is_adult, COALESCE(ordem, 0) as ordem FROM categoria LIMIT 5");
    $stmt->execute();
    $sample_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'teste' => 'consulta_categorias',
        'status' => 'sucesso',
        'message' => 'Consulta de categorias funcional',
        'total_registros' => count($sample_data),
        'amostra_dados' => $sample_data
    ], JSON_PRETTY_PRINT);
    echo "\n\n";

    // 5. Testar endpoints principais
    $endpoints_testes = [
        'categorias.php' => 'Endpoint principal de categorias',
        'categoria-crud.php' => 'Endpoint CRUD alternativo',
        'salvar-ordem.php' => 'Endpoint de ordenação',
        'mover-categoria.php' => 'Endpoint de movimentação'
    ];
    
    foreach ($endpoints_testes as $endpoint => $descricao) {
        $arquivo = __DIR__ . '/' . $endpoint;
        if (file_exists($arquivo)) {
            echo json_encode([
                'teste' => 'endpoint_' . str_replace(['.php', '-'], ['', '_'], $endpoint),
                'status' => 'sucesso',
                'message' => $descricao . ' - arquivo encontrado',
                'arquivo' => $arquivo
            ], JSON_PRETTY_PRINT);
        } else {
            echo json_encode([
                'teste' => 'endpoint_' . str_replace(['.php', '-'], ['', '_'], $endpoint),
                'status' => 'erro',
                'message' => $descricao . ' - arquivo não encontrado',
                'arquivo' => $arquivo
            ], JSON_PRETTY_PRINT);
        }
        echo "\n\n";
    }

    // 6. Testar arquivos de controle
    $controles_testes = [
        'controles/db.php' => 'Arquivo de conexão com banco',
        'controles/categorias.php' => 'Funções de negócio de categorias',
        'controles/checkLogout.php' => 'Verificação de sessão'
    ];
    
    foreach ($controles_testes as $controle => $descricao) {
        $arquivo = __DIR__ . '/' . $controle;
        if (file_exists($arquivo)) {
            echo json_encode([
                'teste' => 'controle_' . str_replace(['controles/', '.php'], ['', ''], $controle),
                'status' => 'sucesso',
                'message' => $descricao . ' - arquivo encontrado',
                'arquivo' => $arquivo
            ], JSON_PRETTY_PRINT);
        } else {
            echo json_encode([
                'teste' => 'controle_' . str_replace(['controles/', '.php'], ['', ''], $controle),
                'status' => 'erro',
                'message' => $descricao . ' - arquivo não encontrado',
                'arquivo' => $arquivo
            ], JSON_PRETTY_PRINT);
        }
        echo "\n\n";
    }

    // 7. Resumo final
    echo json_encode([
        'status' => 'concluido',
        'message' => 'Verificação do sistema de categorias concluída',
        'timestamp' => date('Y-m-d H:i:s'),
        'recomendacoes' => [
            'Execute o arquivo garantir_coluna_ordem.sql no seu banco se a coluna ordem não existir',
            'Verifique se todos os endpoints estão acessíveis via navegador',
            'Teste a funcionalidade drag-and-drop na interface de categorias',
            'Confirme se as permissões de arquivo estão corretas'
        ]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'erro',
        'message' => 'Erro durante verificação: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>
