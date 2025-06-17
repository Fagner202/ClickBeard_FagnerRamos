CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE barbeiros (
    cliente_id INT PRIMARY KEY,
    idade INT NOT NULL,
    data_contratacao DATE NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

CREATE TABLE especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

CREATE TABLE barbeiro_especialidade (
    barbeiro_id INT,
    especialidade_id INT,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (barbeiro_id, especialidade_id),
    FOREIGN KEY (barbeiro_id) REFERENCES barbeiros(cliente_id),
    FOREIGN KEY (especialidade_id) REFERENCES especialidades(id)
);



CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    barbeiro_id INT,
    especialidade_id INT,
    data_hora DATETIME,
    cancelado BOOLEAN DEFAULT FALSE,
    status ENUM('aberto', 'finalizado') DEFAULT 'aberto', -- NOVA COLUNA
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (barbeiro_id) REFERENCES barbeiros(cliente_id),
    FOREIGN KEY (especialidade_id) REFERENCES especialidades(id)
);
