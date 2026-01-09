# ML Admin

Sistema administrativo para integração com a API do **Mercado Livre**, permitindo:

- Cadastro e edição de anúncios (produtos)
- Sincronização de estoque e preços
- Recebimento de pedidos via Webhooks
- Gerenciamento de categorias e atributos
- Integração completa com OAuth do Mercado Livre

---

## 🧰 Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 20
- NPM ou Yarn
- Banco de dados (MySQL ou PostgreSQL)
- Conta de vendedor no Mercado Livre

---

## 🚀 Instalação do Projeto

### 1. Clonar o repositório

```bash
git clone https://github.com/wcostaprijo/mercado-livre-admin.git
cd mercado-livre-admin
```

### 2. Instalar dependências do backend

```bash
composer install
```

### 3. Instalar dependências do frontend

```bash
npm install
```

### 4. Compilar assets

```bash
npm run build
```

ou para desenvolvimento:

```bash
npm run dev
```

### 5. Criar o arquivo de ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 6. Configurar o banco de dados

No arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mercado_livre_admin
DB_USERNAME=root
DB_PASSWORD=
```

Rodar as migrations:

```bash
php artisan migrate
```

---

## 🔐 Configuração da API do Mercado Livre

No `.env`, configure:

```env
MERCADO_LIVRE_CLIENT_ID=SEU_CLIENT_ID
MERCADO_LIVRE_CLIENT_SECRET=SEU_CLIENT_SECRET
```
*As demais envs do mercado livre, devem permanecer iguais*

Esses dados são obtidos em:
👉 https://developers.mercadolivre.com.br/

### Fluxo de autenticação

1. Usuário clica em **Conectar Mercado Livre**
2. Redirecionamento para OAuth do Mercado Livre
3. Mercado Livre retorna o `code`
4. O sistema troca o `code` por `access_token` e `refresh_token`
5. Tokens são armazenados no banco de dados

---

## 🔔 Configuração de Webhooks (Pedidos)

### 1. Criar Webhook no Mercado Livre

No painel do desenvolvedor https://developers.mercadolivre.com.br/devcenter crie uma aplicação para obter suas credenciais e na seção de configurações desta aplicação configure sua URL de notificações.

Para que tudo funcione, ative os seguintes tópicos para receber notificações:
- Tópicos:
  - `orders_v2`
  - `items` (Todos disponíveis)

- URL de notificação:
```
https://seu-dominio.com/api/webhooks/mercado-livre
```

### 2. Endpoint no sistema

O sistema recebe notificações e:

- Identifica o **seller** via `user_id`
- Busca detalhes do pedido via API
- Salva:
  - ID do pedido
  - Comprador
  - Produtos
  - Preço total
  - Status

⚠️ O `user_id` do webhook **sempre representa o vendedor**.

---

## 📦 Sincronização de Estoque e Preços

Foi criado um comando Artisan para sincronização automática a cada 5 minutos:

```bash
php artisan mercadolivre:sync-products
```

Esse comando:

- Atualiza estoque no Mercado Livre
- Calcula o preço correto considerando promoções ativas
- Registra logs das operações

### Agendamento (Scheduler)

No servidor, configure o cron:

```bash
* * * * * php /caminho/do-projeto/artisan schedule:run >> /dev/null 2>&1
```

O Laravel cuidará da execução automática.

---

## 🪵 Logs

Logs específicos do Mercado Livre:

```bash
storage/logs/mercadolivre.log
```

Incluem:
- Sincronizações
- Erros de API
- Processamento de pedidos

---

## 🧪 Ambiente de Desenvolvimento

Rodar o servidor local:

```bash
php artisan serve
```

Acessar:
```
http://localhost:8000
```

---

## 📄 Licença

Projeto desenvolvido para fins de desafio técnico.

---
 - *“Testes automatizados não foram implementados neste desafio por limitação de tempo.”*