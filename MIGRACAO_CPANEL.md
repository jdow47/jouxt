# 🚀 GUIA DE MIGRAÇÃO PARA CPANEL - XTREAM SERVER

## 📋 PRÉ-REQUISITOS

### 1. **Configurações do cPanel**
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Extensões PHP necessárias: PDO, mbstring, json, curl
- Mod_rewrite habilitado

### 2. **Banco de Dados**
- Criar banco de dados no cPanel
- Importar o arquivo SQL: `Banco de dados/xtserveropensource V.1.0.4 23-03-2025.sql`
- Executar comandos adicionais se necessário:
  ```sql
  ALTER TABLE categoria ADD COLUMN IF NOT EXISTS ordem INT DEFAULT 0;
  CREATE INDEX IF NOT EXISTS idx_categoria_tipo_ordem ON categoria(type, ordem);
  ```

## ⚙️ CONFIGURAÇÃO

### 1. **Editar config.php**
Abra o arquivo `config.php` na raiz do projeto e ajuste:

```php
// CONFIGURAÇÃO DO BANCO DE DADOS
define('DB_HOST', 'localhost');           // Geralmente 'localhost' no cPanel
define('DB_NAME', 'SEU_BANCO');           // Nome do seu banco no cPanel
define('DB_USER', 'SEU_USUARIO');         // Usuário do banco no cPanel
define('DB_PASS', 'SUA_SENHA');           // Senha do banco no cPanel

// CONFIGURAÇÃO DE DEBUG
define('DEBUG_MODE', true);               // Mude para false em produção
```

### 2. **Permissões de Arquivos**
No cPanel File Manager, definir permissões:
- Pastas: 755
- Arquivos PHP: 644
- Arquivos de configuração: 600

### 3. **Upload dos Arquivos**
- Fazer upload de todos os arquivos para a pasta `public_html` ou subdomínio
- Manter a estrutura de pastas intacta

## 🔧 VERIFICAÇÕES PÓS-MIGRAÇÃO

### 1. **Teste de Conexão**
Acesse: `seudominio.com/api/testes.php`
Deve retornar JSON válido.

### 2. **Teste de Login**
Acesse: `seudominio.com/index.php`
Tente fazer login com:
- Usuário: admin
- Senha: admin

### 3. **Teste das APIs**
- Panel API: `seudominio.com/panel_api.php?username=admin&password=admin`
- Player API: `seudominio.com/player_api.php?username=admin&password=admin`

## 🐛 SOLUÇÃO DE PROBLEMAS

### **Erro de Conexão com Banco**
1. Verificar credenciais no `config.php`
2. Confirmar se o banco foi criado no cPanel
3. Verificar se o usuário tem permissões

### **Erro 500 - Internal Server Error**
1. Verificar permissões de arquivos
2. Verificar se `.htaccess` está correto
3. Verificar logs de erro no cPanel

### **Erro de Caminho**
1. Verificar se todos os arquivos foram uploadados
2. Verificar estrutura de pastas
3. Verificar includes/requires

### **Problemas de CORS**
1. Verificar headers no `.htaccess`
2. Verificar configurações de domínio
3. Verificar se APIs estão retornando JSON válido

## 📁 ESTRUTURA DE ARQUIVOS

```
public_html/
├── config.php                    # Configuração centralizada
├── .htaccess                     # Configurações do servidor
├── index.php                     # Página de login
├── dashboard.php                 # Dashboard principal
├── categorias.php               # Gerenciamento de categorias
├── api/                         # APIs do sistema
│   ├── controles/               # Lógica de negócio
│   ├── categorias.php           # API de categorias
│   ├── clientes.php             # API de clientes
│   └── ...
├── js/                          # Scripts JavaScript
├── css/                         # Estilos CSS
├── img/                         # Imagens
└── Banco de dados/              # Scripts SQL
```

## 🔒 SEGURANÇA

### **Arquivos Protegidos**
- `config.php` - Bloqueado via .htaccess
- `*.sql` - Bloqueado via .htaccess
- `*.log` - Bloqueado via .htaccess

### **Headers de Segurança**
- X-Content-Type-Options: nosniff
- X-Frame-Options: DENY
- X-XSS-Protection: 1; mode=block

## 📞 SUPORTE

### **Logs de Erro**
- cPanel > Logs > Error Log
- Verificar erros específicos do PHP

### **Teste de Funcionalidades**
1. Login e autenticação
2. CRUD de categorias
3. Drag-and-drop de ordenação
4. APIs de streaming
5. Upload de arquivos

## ✅ CHECKLIST FINAL

- [ ] Banco de dados criado e importado
- [ ] Configurações do `config.php` ajustadas
- [ ] Permissões de arquivos configuradas
- [ ] Login funcionando
- [ ] APIs retornando JSON válido
- [ ] Drag-and-drop funcionando
- [ ] Upload de arquivos funcionando
- [ ] Testes em dispositivos móveis
- [ ] Logs de erro verificados

## 🎯 RESULTADO ESPERADO

Após a migração, o sistema deve funcionar exatamente como no host anterior, com:
- ✅ Todas as funcionalidades operacionais
- ✅ APIs respondendo corretamente
- ✅ Interface responsiva
- ✅ Segurança mantida
- ✅ Performance otimizada

---

**Nota:** Em caso de problemas, ative o modo debug no `config.php` para obter informações detalhadas de erro. 