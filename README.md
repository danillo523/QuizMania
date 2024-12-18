![QuizMania Logo](path/to/your/image.png)
## QuizMania

QuizMania é uma API de quizzes de verdadeiro ou falso, desenvolvida com Laravel 11 e PHP 8.2. O sistema inclui autenticação de usuários, diferentes níveis de permissão (usuário comum e administrador), tentativas de resposta e classificação no leaderboard. Usuários podem se registrar, fazer login, participar de quizzes e visualizar suas pontuações. Administradores têm permissões adicionais para criar, atualizar e excluir quizzes e perguntas.

### Tecnologias Utilizadas
- Laravel 11
- PHP 8.2
- MySQL
- Laravel Pint
- Docker
- Postman

### Funcionalidades
- Registro, login e logout de usuários e perfil
- Criação, atualização e exclusão de quizzes e perguntas (somente para administradores)
- Participação em quizzes
- Visualização de tentativas e pontuações
- Leaderboard com pontuações dos usuários
- 
### Documentação da API
A documentação da API está disponível tanto no link quanto em arquivo JSON para importação no Postman.:
- **Postman:** https://www.postman.com/tearing5/quizmania/folder/eqw3ydz/quizmania?action=share&source=copy-link&creator=2630047&ctx=documentation e no arquivo `\docs\postman\QuizMania.postman_collection.json`

### Como Rodar o Projeto

1. **Clone o repositório:**
   ```sh
   git clone https://github.com/danillo523/QuizMania.git
   cd QuizMania
   ```

2. **Configure o arquivo `.env`:**
   Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente, especialmente as configurações do banco de dados.

3. **Instale as dependências:**
   ```sh
   composer install
   ```

4. **Gere a chave da aplicação:**
   ```sh
   php artisan key:generate
   ```

5. **Construa e inicie os containers Docker:**
   ```sh
   docker-compose up -d
   ```

6. **Acesse a aplicação:**
   A aplicação estará disponível em `http://localhost:8000/api` ao rodar o docker container.

### Estrutura de Rotas
- **Autenticação:**
    - `POST /register` - Registro de usuário
    - `POST /login` - Login de usuário
    - `POST /logout` - Logout de usuário (requer autenticação)
    - `GET /me` - Informações do usuário autenticado

- **Quizzes (Admin):**
    - `POST /quizzes` - Criar quiz
    - `PUT /quizzes/{quiz}` - Atualizar quiz
    - `DELETE /quizzes/{quiz}` - Deletar quiz

- **Perguntas (Admin):**
    - `POST /quizzes/{quiz}/questions` - Criar pergunta
    - `PUT /quizzes/{quiz}/questions/{question}` - Atualizar pergunta
    - `DELETE /quizzes/{quiz}/questions/{question}` - Deletar pergunta

- **Quizzes (Usuário):**
    - `GET /quizzes` - Listar quizzes
    - `GET /quizzes/{quiz}` - Detalhes de um quiz específico 

- **Tentativas:**
    - `POST /quizzes/{quiz}/start` - Iniciar tentativa
    - `POST /attempts/answer` - Responder pergunta
    - `GET /attempts/{attempt}` - Detalhes da tentativa
    - `GET /attempts` - Listar tentativas do usuário

- **Leaderboard:**
    - `GET /leaderboard` - Listar leaderboard
    - `GET /leaderboard/{order}` - Listar leaderboard ordenado
    - `GET /leaderboard/user/{user}` - Pontuação do usuário

### Docker
O projeto utiliza Docker para facilitar a configuração e execução do ambiente de desenvolvimento. Certifique-se de ter o Docker e o Docker Compose instalados em sua máquina.           
**ATENÇÃO** : Ao iniciar os containers O usuário **admin** no banco de dados MySQL é criado automaticamente com a senha **password**, a migration também é executada automaticamente para criar as tabelas necessárias.

Para iniciar os containers, execute:
```sh
docker-compose up -d
```

Para parar os containers, execute:
```sh
docker-compose down
```

