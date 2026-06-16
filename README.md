# PitchBooking

Web App de um sistema de reservas de campos desportivos de um clube de ténis e pádel de pequeno porte.

## Descrição

Aplicação web para a gestão integral de um clube desportivo. Permite que atletas pesquisem e reservem tipos de campo, giram e consultem as suas reservas mediante autenticação, e que a equipa do clube (gestor e rececionista) administre campos, atletas, reservas, registe pagamentos simulados, efetue a confirmação de comparência (check-in) e extraia relatórios.

## Funcionalidades

- Registo e login de utilizadores com hashing de password
- Pesquisa e reserva de campos por tipo (Pádel Coberto, Pádel Descoberto, Ténis Terra Batida, Ténis Rápido)
- Suplementos de iluminação noturna e aluguer de material (raquetes/bolas)
- Gestão de reservas pelo utilizador (listar, editar e cancelar até 24h antes)
- Backoffice com dois perfis: gestor e rececionista
- Registo de pagamentos simulados (parcial/total) e check-in
- Relatórios de ocupação, estado das reservas e receita

## Stack Tecnológica

- **HTML5 / CSS** — estrutura e estilo das páginas
- **JavaScript** — comunicação assíncrona com a API
- **PHP** — backend e ligação à base de dados (com prepared statements)
- **MySQL** — base de dados relacional
- **Apache (MAMP)** — servidor local
- **Bootstrap** — ícones
- **Google Fonts** — tipografia

## Instalação

1. Clonar o repositório para a pasta `htdocs` do MAMP:
   ```
   git clone https://github.com/HamiltonPrado/PitchBooking.git
   ```
2. Iniciar o MAMP (Apache + MySQL).
3. Criar a base de dados importando o script `database/PitchBooking` no MySQL Workbench ou phpMyAdmin.
4. Confirmar as credenciais em `backend/db.php` (host, porta, user, password).
5. Aceder no browser a `http://localhost/PitchBooking`.

## Autor

Francisco Baptista — Engenharia Informática, 2º ano
Universidade Europeia
