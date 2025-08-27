-- Ajustes para funcionalidade de ordenação de categorias
-- Execute estes comandos no seu banco de dados MySQL/MariaDB

-- 1. Adicionar coluna 'ordem' se não existir
ALTER TABLE categoria ADD COLUMN IF NOT EXISTS ordem INT DEFAULT 0;

-- 2. Criar índice para otimizar consultas por tipo e ordem
CREATE INDEX IF NOT EXISTS idx_categoria_tipo_ordem ON categoria(type, ordem);

-- 3. Atualizar ordem existente baseada no ID (opcional - para categorias já existentes)
-- UPDATE categoria SET ordem = id WHERE ordem = 0 OR ordem IS NULL;

-- 4. Verificar se as alterações foram aplicadas
-- DESCRIBE categoria;
-- SHOW INDEX FROM categoria; 