# Sistema de Gerenciamento de Biblioteca - API Laravel

Este projeto é uma API RESTful desenvolvida em Laravel que simula um sistema de gerenciamento de biblioteca. A API permite gerenciar autores, livros e empréstimos de livros, além de implementar autenticação usando JWT e enviar notificações por e-mail através de filas.

## Funcionalidades

- **Autenticação JWT**: Implementação de autenticação JWT para proteger os endpoints da API.
- **Gerenciamento de Autores**: Criar, listar, atualizar e deletar autores.
- **Gerenciamento de Livros**: Criar, listar, atualizar e deletar livros, com suporte a múltiplos autores por livro.
- **Gerenciamento de Empréstimos**: Registrar e listar empréstimos de livros.
- **Notificações por E-mail**: Enviar um e-mail ao usuário quando um livro for emprestado, usando filas para não bloquear a resposta da API.
- **Administração**: Somente administradores podem criar, atualizar ou deletar registros de usuários, livros e autores.

## Requisitos

- **PHP 8.0+**
- **Composer**
- **MySQL ou SQLite**
- **Laravel 8+**

## Instalação

1. **Clone o repositório:**

   ```bash
   git clone https://github.com/vagnerhf/library.git
   cd library
    ```
2. **Instale as dependências do PHP:**

    ```bash
    composer install
    ```
3. **Copie o arquivo .env.example para .env e configure as variáveis de ambiente:**

    ```bash
    cp .env.example .env
    ```

4. **Configure as seguintes variáveis no arquivo .env:**

    ```
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=nome_do_banco_de_dados
        DB_USERNAME=seu_usuario
        DB_PASSWORD=sua_senha
        JWT_SECRET=sua_chave_secreta_jwt
    ```

5. **Para gerar a chave secreta JWT, execute:**

    ```bash
    php artisan jwt:secret
    ```
6. **Configure o banco de dados e execute as migrations:**

    ```bash
    php artisan migrate
    ```
7. **Execute as seeders para criar o superusuário:**

    ```bash
    php artisan db:seed --class=AdminUserSeeder
    ```
8. **Inicie o servidor de desenvolvimento:**

    ```bash
        php artisan serve
    ```
    A aplicação estará disponível em http://localhost:8000.

## Uso
### Autenticação

- **Registrar**: Enviar uma requisição POST para /api/register com os campos name, email, password, e password_confirmation.
- **Login**: Enviar uma requisição POST para /api/login com email e password. A resposta incluirá um token JWT.
- **Logout**: Enviar uma requisição POST para /api/logout com o token JWT no cabeçalho Authorization.

### Acesso à API

Para acessar os endpoints protegidos, inclua o token JWT no cabeçalho Authorization como Bearer <seu_token>.

Exemplo de requisição para listar autores:
```bash
        curl -H "Authorization: Bearer <seu_token>" http://localhost:8000/api/authors
```

### Testes

1. Executar os testes:

    Para rodar os testes unitários e de integração, utilize o comando:

    ```bash
        php artisan test
    ```
2. Testes de Integração:

   Os testes de integração verificam a interação entre múltiplas partes do sistema, como a criação de empréstimos e a notificação de usuários.

3. Testes Unitários:

   Os testes unitários garantem que as funcionalidades individuais funcionam como esperado.

## Configuração Adicional
### Fila para Notificações

1. Configurar o sistema de filas:
No arquivo .env, defina o driver de fila, por exemplo:

    ```env
    QUEUE_CONNECTION=database
    ```
2. Criar a tabela de filas:

    ```bash
    php artisan queue:table
    php artisan migrate
    ```
3. Executar o worker de filas:

Para processar as filas em background, execute:

```bash
    php artisan queue:work
```

## Documentação da API

A API segue as convenções RESTful. Abaixo estão as principais rotas disponíveis:

    GET /api/authors - Listar autores
    GET /api/authors/{id} - Exibir um autor específico
    POST /api/authors - Criar um novo autor (admin)
    PUT /api/authors/{id} - Atualizar um autor existente (admin)
    DELETE /api/authors/{id} - Deletar um autor (admin)
    GET /api/books - Listar livros
    GET /api/books/{id} - Exibir um livro específico
    POST /api/books - Criar um novo livro (admin)
    PUT /api/books/{id} - Atualizar um livro existente (admin)
    DELETE /api/books/{id} - Deletar um livro (admin)
    GET /api/loans - Listar empréstimos
    POST /api/loans - Registrar um novo empréstimo
    GET /api/users - Listar usuários (admin)
    GET /api/users/{email} - Exibir um usuário específico (admin)
    POST /api/users - Criar um novo usuário (admin)
    PUT /api/users/{email} - Atualizar um usuário existente (admin)
    DELETE /api/users/{email} - Deletar um usuário (admin)

Mais informações em:
    api/documentation

## Considerações Finais

Este projeto foi desenvolvido como parte de um teste para desenvolvedor sênior em Laravel, focando em boas práticas de desenvolvimento, segurança, e eficiência no uso dos recursos do framework.

