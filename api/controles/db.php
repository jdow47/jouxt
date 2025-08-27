<?php
/**
 * Arquivo de conexão com banco de dados
 * Compatível com migração cPanel
 */

// Incluir configuração centralizada
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

// Função de conexão com banco de dados
function conectar_bd() {
    // Verificar se as constantes estão definidas
    if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
        // Configuração padrão para desenvolvimento local
        $endereco = "localhost"; 
        $banco = "xtream"; 
        $dbusuario = "root"; 
        $dbsenha = ""; 
    } else {
        // Usar configurações do config.php
        $endereco = DB_HOST;
        $banco = DB_NAME;
        $dbusuario = DB_USER;
        $dbsenha = DB_PASS;
    }

    try {
        $conexao = new PDO("mysql:host=$endereco;dbname=$banco;charset=utf8mb4", $dbusuario, $dbsenha);
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conexao;
    } catch(PDOException $e) {
        error_log("Erro de conexão com banco: " . $e->getMessage());
        return null;
    }
}