-- Table user
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    roles LONGTEXT NOT NULL COMMENT 'Stocke les rôles au format JSON (ex. ["ROLE_ADMIN"])',
    created_at DATETIME NOT NULL
);

-- Table habitat
CREATE TABLE habitat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    images LONGTEXT COMMENT 'Stocke les URLs d’images au format JSON (ex. ["image1.jpg"])'
);

-- Table animal
CREATE TABLE animal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    breed VARCHAR(100) NOT NULL,
    habitat_id INT NOT NULL,
    images LONGTEXT COMMENT 'Stocke les URLs d’images au format JSON (ex. ["image1.jpg"])',
    FOREIGN KEY (habitat_id) REFERENCES habitat(id) ON DELETE CASCADE
);

-- Table service
CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL
);

-- Table review
CREATE TABLE review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL,
    comment TEXT NOT NULL,
    is_valid BOOLEAN DEFAULT FALSE,
    created_at DATETIME NOT NULL
);

-- Table report
CREATE TABLE report (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    food VARCHAR(100) NOT NULL,
    food_quantity INT NOT NULL,
    visit_date DATETIME NOT NULL,
    details TEXT,
    vet_id INT NOT NULL,
    FOREIGN KEY (animal_id) REFERENCES animal(id) ON DELETE CASCADE,
    FOREIGN KEY (vet_id) REFERENCES user(id) ON DELETE CASCADE
);

-- Table feeding
CREATE TABLE feeding (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    food VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    date DATETIME NOT NULL,
    employee_id INT NOT NULL,
    FOREIGN KEY (animal_id) REFERENCES animal(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES user(id) ON DELETE CASCADE
);

-- Table contact
CREATE TABLE contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME NOT NULL
);

-- Table habitat_comment
CREATE TABLE habitat_comment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habitat_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    vet_id INT NOT NULL,
    FOREIGN KEY (habitat_id) REFERENCES habitat(id) ON DELETE CASCADE,
    FOREIGN KEY (vet_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day VARCHAR(20) NOT NULL UNIQUE,
    opening_time TIME,
    closing_time TIME,
    is_closed BOOLEAN DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);