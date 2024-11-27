CREATE TABLE state (
    state_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    ddd VARCHAR(3) NOT NULL,
    ibge VARCHAR(10) NOT NULL,
    pais VARCHAR(50) NOT NULL
);

CREATE TABLE city (
    city_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    state_id INT,
    FOREIGN KEY (state_id) REFERENCES state(state_id)
);

CREATE TABLE build (
    build_id INT AUTO_INCREMENT PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    city_id INT,
    state_id INT,
    photos LONGTEXT,
    info_area_total DECIMAL(10, 2) NOT NULL,
    info_parking_space INT NOT NULL,
    info_rooms INT NOT NULL,
    bairro VARCHAR(255) NOT NULL,
    condominium_price DECIMAL(10, 2),
    iptu_price DECIMAL(10, 2) NOT NULL,
    build_type VARCHAR(50) NOT NULL,
    FOREIGN KEY (city_id) REFERENCES city(city_id),
    FOREIGN KEY (state_id) REFERENCES state(state_id)
);

CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    cpf VARCHAR(11) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    photo LONGBLOB,
    user_type_id INT CHECK (user_type_id IN (1, 2))
);

CREATE TABLE announcement (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    build_id INT NOT NULL,
    user_id INT NOT NULL,
	isHighlighted BOOLEAN NOT NULL,
    FOREIGN KEY (build_id) REFERENCES build(build_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE announcement_photos (
    photo_id INT AUTO_INCREMENT PRIMARY KEY,
    announcement_id INT NOT NULL,
    photo LONGTEXT,
    FOREIGN KEY (announcement_id) REFERENCES announcement(announcement_id)
);

CREATE TABLE visitor_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    detailed_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    detailed_message TEXT,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- Inserir dados na tabela state
INSERT INTO state (name, ddd, ibge, pais) VALUES
('São Paulo', '11', '3550308', 'Brasil'),
('Rio de Janeiro', '21', '3304557', 'Brasil');

-- Inserir dados na tabela city
INSERT INTO city (name, state_id) VALUES
('São Paulo', 1),
('Campinas', 1),
('Rio de Janeiro', 2),
('Niterói', 2);

-- Inserir dados na tabela build
INSERT INTO build (address, city_id, state_id, info_area_total, info_parking_space, info_rooms, bairro, condominium_price, iptu_price, build_type) VALUES
('Avenida Paulista, 1000', 1, 1, 120.5, 1, 3, 'Bela Vista', 500.00, 200.00, 'Apartamento'),
('Rua das Flores, 200', 2, 1, 150.0, 2, 4, 'Centro', NULL, 250.00, 'Casa'),
('Avenida Atlântica, 1500', 3, 2, 90.0, 1, 2, 'Copacabana', 700.00, 300.00, 'Apartamento'),
('Rua das Palmeiras, 300', 4, 2, 110.0, 1, 3, 'Icaraí', 400.00, 220.00, 'Apartamento');

-- Inserir dados na tabela user
INSERT INTO user (username, password, email, cpf, phone, photo, user_type_id) VALUES
('admin1', 'senha123', 'admin1@example.com', '12345678901', '11999998888', 'admin1.jpg', 1),
('corretor1', 'senha123', 'corretor1@example.com', '98765432109', '21999997777', 'corretor1.jpg', 2),
('corretor2', 'senha123', 'corretor2@example.com', '12312312345', '21988886666', 'corretor2.jpg', 2);

-- Inserir dados na tabela announcement
INSERT INTO announcement (title, description, price, user_id, build_id) VALUES
('Apartamento na Avenida Paulista', 'Ótimo apartamento de 3 quartos na Avenida Paulista.', 800000.00, 2, 1),
('Casa no Centro de Campinas', 'Casa espaçosa com 4 quartos no centro de Campinas.', 600000.00, 3, 2),
('Apartamento na Copacabana', 'Apartamento aconchegante de 2 quartos na Avenida Atlântica.', 1200000.00, 2, 3),
('Apartamento em Icaraí', 'Apartamento de 3 quartos em bairro nobre de Niterói.', 850000.00, 3, 4);