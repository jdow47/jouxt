-- Adicionar coluna ordem na tabela categoria
-- Execute este comando no phpMyAdmin ou MySQL

ALTER TABLE categoria ADD COLUMN IF NOT EXISTS ordem INT DEFAULT 0;

-- Verificar se foi adicionada
DESCRIBE categoria; 