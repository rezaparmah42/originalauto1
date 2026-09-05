CREATE TABLE vehicle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE brand (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE model (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    vehicle_id INT,
    FOREIGN KEY (vehicle_id) REFERENCES vehicle(id)
);

CREATE TABLE compatibility (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    model_id INT,
    FOREIGN KEY (product_id) REFERENCES product(id),
    FOREIGN KEY (model_id) REFERENCES model(id)
);