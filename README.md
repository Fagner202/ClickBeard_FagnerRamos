# ClickBeard - Sistema de Agendamento para Barbearia 💈

Este é um projeto desenvolvido como parte do processo seletivo para a vaga de Desenvolvedor PHP Júnior na ClickAtivo.

## 📌 Objetivo

Sistema web simples para agendamento de serviços em uma barbearia. Os clientes podem se cadastrar, realizar login e agendar horários com barbeiros disponíveis. O administrador pode visualizar os agendamentos do dia e futuros.

---

## ⚙️ Tecnologias Utilizadas

- PHP 8.2
- MySQL 8
- Apache
- Docker + Docker Compose
- JWT (para autenticação)
- phpMyAdmin (para visualização do banco)

---

## 🚀 Como executar o projeto

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/ClickBeard_FagnerRamos.git
cd ClickBeard_FagnerRamos
```
### 2. Configure as variáveis de ambiente

Crie um arquivo `.env` na raiz do projeto com o seguinte conteúdo:

```env
DB_ROOT_PASSWORD=root
DB_NAME=clickbeard
DB_USER=clickuser
DB_PASSWORD=click123
```

### 3. Suba os containers com Docker

```bash
docker-compose up -d --build
```

### 4. Instale as dependências do Composer

Acesse o container do PHP:

```bash
docker exec -it clickbeard-php bash
```

Dentro do container, instale o pacote JWT:

```bash
composer require firebase/php-jwt
```

Acesse a aplicação em: [http://localhost:8080](http://localhost:8080)

Acesse o phpMyAdmin em: [http://localhost:8081](http://localhost:8081)

## 📁 Estrutura do Projeto

```bash
ClickBeard_FagnerRamos/
├── docker/
│   └── apache-php/
│       ├── Dockerfile
│       └── apache-config.conf
├── src/                 # Código PHP
│   └── index.php
├── .env
├── docker-compose.yml
└── README.md
```

## ✅ Funcionalidades a implementar

- Ambiente Docker com PHP, MySQL e phpMyAdmin OK
- Cadastro e login de clientes
- Cadastro de barbeiros e especialidades
- Agendamento com regras de negócio
- Painel de visualização para administrador
- Diagrama ER OK
- Scripts SQL de criação do banco OK