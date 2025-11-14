# Gerenciador de Solicitações

## Descrição

Este é um sistema de gerenciamento de solicitações, onde os usuários podem criar, acompanhar e gerenciar solicitações. O sistema possui uma área administrativa para visualização e gerenciamento de todas as solicitações.

## Tecnologias Utilizadas

*   PHP
*   JavaScript
*   CSS
*   MySQL

## Instalação

1.  Clone o repositório:
    ```sh
    git clone https://github.com/seu-usuario/hackathon.git
    ```
2.  Importe o banco de dados `banco_de_dados/gerenciador_solicitacoes.sql` para o seu servidor MySQL.
3.  Configure a conexão com o banco de dados em `config/database.php`.
4.  Inicie o servidor web.

## Uso

1.  Acesse a página de login para entrar no sistema.
2.  Se você for um solicitante, pode criar uma nova solicitação, acompanhar suas solicitações existentes e ver o histórico de suas solicitações.
3.  Se você for um administrador, pode visualizar todas as solicitações, alterar seus status e ver os detalhes de cada uma.

## Usuários de teste (admin)

Para facilitar testes locais, o projeto já inclui dois usuários administrativos no banco de dados de exemplo. A senha padrão **é a mesma para os dois**: **`admin123`**. Use os e‑mails registrados no dump (por exemplo `ti@exemplo.com` e `manutencao@exemplo.com`) com essa senha para acessar a área administrativa.

## Banco de Dados

O arquivo de criação do banco de dados está localizado em `banco_de_dados/gerenciador_solicitacoes.sql`. O modelo do banco de dados está em `banco_de_dados/modelagem/gerenciador_solicitacoes.mwb`.

## Estrutura de Pastas

A estrutura de pastas do projeto é a seguinte:

*   `banco_de_dados/`: Contém os arquivos do banco de dados.
*   `config/`: Contém os arquivos de configuração.
*   `public/`: Contém os arquivos públicos, como CSS e JavaScript.
*   `src/`: Contém o código-fonte da aplicação.
    *   `controllers/`: Contém os controladores da aplicação.
    *   `models/`: Contém os modelos da aplicação.
    *   `views/`: Contém as views da aplicação.
*   `templates/`: Contém os templates do projeto.



