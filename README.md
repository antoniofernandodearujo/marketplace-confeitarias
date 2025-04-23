# 🎂 Marketplace Confeitarias 🍰

Bem-vindo ao Marketplace Confeitarias! Este é um projeto desenvolvido para conectar confeitarias e clientes, oferecendo uma plataforma para descobrir e encomendar deliciosos produtos de confeitaria. 🧁

## ✨ Requisitos

Para rodar este projeto localmente, você precisará ter instalado:

*   **PHP:** (Recomendado versão 8.1 ou superior)
*   **Composer:** Gerenciador de dependências do PHP
*   **Node.js & npm/yarn:** Para compilar os assets de frontend (Vue.js)
*   **🐘 PostgreSQL:** O banco de dados utilizado neste projeto. Certifique-se de que o serviço esteja rodando.

## 🚀 Instalação e Configuração

Siga estes passos para configurar o ambiente de desenvolvimento:

1.  **Clone o Repositório:**
    ```bash
    git clone https://github.com/antoniofernandodearujo/marketplace-confeitarias.git
    cd marketplace-confeitarias
    ```

2.  **Instale as Dependências do PHP:**
    ```bash
    composer install
    ```

3.  **Instale as Dependências do Node.js:**
    ```bash
    npm install
    # ou
    # yarn install
    ```

4.  **Configure o Ambiente:**
    *   Copie o arquivo de exemplo `.env.example` para `.env`:
        ```bash
        cp .env.example .env
        ```
    *   📄 Edite o arquivo `.env` e configure as variáveis de ambiente, especialmente as credenciais do **PostgreSQL** (`DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

5.  **Gere a Chave da Aplicação:** 🔑
    ```bash
    php artisan key:generate
    ```

6.  **Gere a Migration da Tabela de Sessões:** 🗄️
    *   O Laravel usa o banco de dados para gerenciar sessões. Crie a migration necessária:
        ```bash
        php artisan session:table
        ```

7.  **Rode as Migrations:** 🏗️
    *   Crie todas as tabelas no banco de dados PostgreSQL:
        ```bash
        php artisan migrate
        ```
    *   *Opcional:* Se precisar recriar o banco do zero (apagando todos os dados existentes):
        ```bash
        php artisan migrate:fresh
        ```

8.  **Compile os Assets de Frontend:** (Se necessário, dependendo da estrutura do projeto Vue/Blade)
    ```bash
    npm run dev
    # ou
    # yarn dev
    ```

## ▶️ Rodando o Projeto

1.  **Inicie o Servidor de Desenvolvimento Laravel (Backend):** 🚀
    ```bash
    php artisan serve --host=0.0.0.0 --port=8000
    ```
    *   O backend estará acessível em `http://localhost:8000` (ou o IP da sua máquina na rede local na porta 8000).

2.  **Acesse a Aplicação:**
    *   Abra seu navegador e acesse a URL informada pelo comando `php artisan serve`.

## 📊 Populando com Dados Mockados (Opcional)

Se precisar preencher o banco com dados de exemplo para teste:

1.  **Rode o Seeder Específico:**
    ```bash
    php artisan db:seed --class=MockDataSeeder
    ```
    *   *Alternativa (se configurado):* Rodar todas as migrations e seeders de uma vez:
        ```bash
        php artisan migrate:fresh --seed
        ```

## 🗺️ Rotas da Aplicação

Para visualizar todas as rotas disponíveis na aplicação, incluindo nomes e middlewares (se definidos), utilize o comando:

```bash
php artisan route:list
```

Abaixo estão as rotas principais identificadas:

| Método HTTP | URI                           | Controller@Action / Handler        | Descrição (Sugestão)             |
| :---------- | :---------------------------- | :------------------------------- | :------------------------------- |
| `GET HEAD`  | `/`                           | (Closure ou view padrão)         | Rota principal / Home page       |
| `GET HEAD`  | `confectioneries`             | `ConfectioneryController@index`  | Lista todas as confeitarias      |
| `POST`      | `confectioneries`             | `ConfectioneryController@store`  | Cria uma nova confeitaria        |
| `GET HEAD`  | `confectioneries/{confectionery}` | `ConfectioneryController@show`   | Mostra detalhes de uma confeitaria |
| `PATCH`     | `confectioneries/{confectionery}` | `ConfectioneryController@update` | Atualiza uma confeitaria         |
| `DELETE`    | `confectioneries/{confectionery}` | `ConfectioneryController@destroy`| Deleta uma confeitaria           |
| `POST`      | `products`                    | `ProductController@store`        | Cria um novo produto             |
| `PATCH`     | `products/{product}`          | `ProductController@update`       | Atualiza um produto              |
| `DELETE`    | `products/{product}`          | `ProductController@destroy`      | Deleta um produto                |
| `GET HEAD`  | `storage/{path}`              | `storage.local`                  | Acessa arquivos no storage linkado |
| `GET HEAD`  | `up`                          | (Framework Health Check)         | Verifica o status da aplicação   |

*(**Observação:** O comando `php artisan route:list` pode fornecer informações adicionais como nomes de rotas (`Name`) e middlewares aplicados. É recomendado verificar a saída completa do comando para detalhes mais precisos.)*

---

🎉 Pronto! Seu ambiente de desenvolvimento para o Marketplace Confeitarias deve estar configurado e rodando. Bom desenvolvimento!
