-- Création de la base de données
CREATE DATABASE IF NOT EXISTS restaurant_aldar;

USE restaurant_aldar;

-- Table des utilisateurs (administrateurs et clients)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nom_complet VARCHAR(255),
    telephone VARCHAR(20),
    role ENUM('admin', 'client') DEFAULT 'client',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des produits
CREATE TABLE IF NOT EXISTS produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT 'default.png',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    categorie_id INT,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE
    SET
        NULL
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    total DECIMAL(10, 2) NOT NULL,
    status ENUM('en_attente', 'annulee', 'validee') DEFAULT 'en_attente',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des détails de commande (Recettes commandés)
CREATE TABLE IF NOT EXISTS details_commande (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT,
    produit_id INT,
    quantite INT NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
);

-- Ajout d'un administrateur par défaut
INSERT INTO
    users (email, password, nom_complet, telephone, role)
VALUES
    (
        'admin@example.com',
        'admin123',
        'Admin Restaurant',
        '0123456789',
        'admin'
    );

-- Ajout de catégories de plats
INSERT INTO
    categories (nom)
VALUES
    ('Pizzas'),
    ('Pâtes'),
    ('Salades'),
    ('Desserts'),
    ('Boissons');

-- Ajout de produits (30 plats)
INSERT INTO
    produits (nom, prix, description, image, categorie_id)
VALUES
    (
        'Margherita',
        8.50,
        'Pizza traditionnelle avec sauce tomate et mozzarella.',
        'default.png',
        1
    ),
    (
        'Pepperoni',
        9.50,
        'Pizza au pepperoni et mozzarella fondue.',
        'default.png',
        1
    ),
    (
        'Quatre Fromages',
        10.00,
        'Pizza garnie de quatre fromages italiens.',
        'default.png',
        1
    ),
    (
        'Calzone',
        11.00,
        'Pizza pliée avec jambon, mozzarella et sauce tomate.',
        'default.png',
        1
    ),
    (
        'Prosciutto e Funghi',
        12.00,
        'Pizza avec jambon et champignons.',
        'default.png',
        1
    ),
    (
        'Spaghetti Carbonara',
        12.00,
        'Spaghetti à la crème avec lardons et parmesan.',
        'default.png',
        2
    ),
    (
        'Penne Arrabbiata',
        10.50,
        'Pâtes avec une sauce tomate épicée.',
        'default.png',
        2
    ),
    (
        'Lasagne',
        13.00,
        'Lasagnes maison avec bœuf et sauce tomate.',
        'default.png',
        2
    ),
    (
        'Tagliatelle al Pesto',
        11.50,
        'Tagliatelle fraîches au pesto maison.',
        'default.png',
        2
    ),
    (
        'Ravioli Ricotta',
        14.00,
        'Raviolis farcis à la ricotta et épinards.',
        'default.png',
        2
    ),
    (
        'Salade César',
        9.50,
        'Salade avec poulet, croutons et parmesan.',
        'default.png',
        3
    ),
    (
        'Salade Caprese',
        8.00,
        'Salade fraîche avec tomates, mozzarella et basilic.',
        'default.png',
        3
    ),
    (
        'Salade Niçoise',
        9.00,
        'Salade niçoise avec thon, œuf et olives.',
        'default.png',
        3
    ),
    (
        'Salade Grecque',
        8.50,
        'Salade avec feta, concombres et tomates.',
        'default.png',
        3
    ),
    (
        'Salade de Quinoa',
        9.00,
        'Salade à base de quinoa, avocat et légumes frais.',
        'default.png',
        3
    ),
    (
        'Tiramisu',
        5.50,
        'Dessert italien traditionnel avec mascarpone et café.',
        'default.png',
        4
    ),
    (
        'Panna Cotta',
        5.00,
        'Dessert crémeux avec coulis de fruits rouges.',
        'default.png',
        4
    ),
    (
        'Gelato',
        4.50,
        'Glace italienne artisanale.',
        'default.png',
        4
    ),
    (
        'Cannoli',
        6.00,
        'Pâtisserie italienne avec une garniture sucrée.',
        'default.png',
        4
    ),
    (
        'Gâteau au Chocolat',
        5.00,
        'Gâteau au chocolat fondant.',
        'default.png',
        4
    ),
    (
        'Coca-Cola',
        2.50,
        'Boisson rafraîchissante gazeuse.',
        'default.png',
        5
    ),
    (
        'Eau Minérale',
        2.00,
        'Eau minérale naturelle.',
        'default.png',
        5
    ),
    (
        'Limonade Maison',
        3.50,
        'Limonade maison avec du citron frais.',
        'default.png',
        5
    ),
    (
        'Thé Glacé',
        3.00,
        'Boisson froide à base de thé.',
        'default.png',
        5
    ),
    (
        'Café Espresso',
        2.50,
        'Café italien serré.',
        'default.png',
        5
    ),
    (
        'Vin Rouge',
        15.00,
        'Vin rouge italien (bouteille).',
        'default.png',
        5
    ),
    (
        'Vin Blanc',
        15.00,
        'Vin blanc italien (bouteille).',
        'default.png',
        5
    ),
    (
        'Prosecco',
        18.00,
        'Vin pétillant italien.',
        'default.png',
        5
    ),
    (
        'Apéritif Spritz',
        7.00,
        'Cocktail italien à base d Apérol.',
        'default.png',
        5
    ),
    (
        'Limoncello',
        6.00,
        'Digestif italien à base de citron.',
        'default.png',
        5
    );

-- Ajout de clients pour test
INSERT INTO
    users (email, password, nom_complet, telephone, role)
VALUES
    (
        'client1@example.com',
        'client123',
        'Client 1',
        '0123456789',
        'client'
    ),
    (
        'client2@example.com',
        'client123',
        'Client 2',
        '0987654321',
        'client'
    );

-- Ajout de commandes test
INSERT INTO
    commandes (client_id, total, status)
VALUES
    (1, 40.50, 'validee'),
    (2, 25.00, 'en_attente');

-- Détails des commandes (exemples)
INSERT INTO
    details_commande (commande_id, produit_id, quantite, prix)
VALUES
    (1, 1, 2, 8.50),
    -- 2x Pizza Margherita
    (1, 6, 1, 12.00),
    -- 1x Spaghetti Carbonara
    (2, 3, 1, 10.00),
    -- 1x Quatre Fromages
    (2, 20, 1, 5.00);

-- 1x Tiramisu