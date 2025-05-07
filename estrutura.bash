/agendamento
│
├── /config
│   └── db.php                   → conexão com MySQL
│
├── /controllers
│   ├── AuthController.php       → login/cadastro para usuários internos
│   ├── ClienteController.php    → login/cadastro de cliente
│   ├── HorarioController.php    → salva/edita horários de atendimento
│   └── AgendamentoController.php → realiza e gerencia agendamentos
│
├── /models
│   ├── User.php
│   ├── Cliente.php
│   ├── Horario.php
│   └── Agendamento.php
│
├── /views
│   ├── /auth                    → login/registro interno
│   ├── /clientes                → login/registro/agenda pública
│   ├── /horarios                → configuração de horários
│   ├── /agendamentos            → listar, criar, visualizar
│   └── layout.php               → cabeçalho/rodapé padrão
│
├── /painel
│   └── dashboard.php
│
├── /public
│   ├── index.php                → home pública (área cliente)
│   ├── cadastro.php             → registro cliente
│   ├── login.php                → login cliente
│   ├── agendar.php              → agenda cliente
│   └── /assets                  → css, js, imagens
│
├── /routes
│   └── web.php                  → roteamento simples via ?page=
│
└── .htaccess                    → redirecionamento amigável (opcional)
