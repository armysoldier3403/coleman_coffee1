mysql> CREATE DATABASE daily_grind_db;
Query OK, 1 row affected (0.002 sec)

mysql> USE daily_grind_db;
Database changed
mysql> CREATE TABLE customers (
    ->     customer_id INT AUTO_INCREMENT PRIMARY KEY,
    ->     first_name VARCHAR(50) NOT NULL,
    ->     last_name VARCHAR(50) NOT NULL,
    ->     email VARCHAR(100) NOT NULL UNIQUE,
    ->     login_username VARCHAR(50) NOT NULL UNIQUE,
    ->     password_hash VARCHAR(255) NOT NULL,
    ->     security_question_1 TEXT NOT NULL,
    ->     security_answer_1 VARCHAR(255) NOT NULL,
    ->     security_question_2 TEXT NOT NULL,
    ->     security_answer_2 VARCHAR(255) NOT NULL,
    ->     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    -> );
Query OK, 0 rows affected (0.010 sec)

mysql> USE daily_grind_db;
Database changed
mysql> CREATE TABLE products (
    ->     product_id INT AUTO_INCREMENT PRIMARY KEY,
    ->     name VARCHAR(255) NOT NULL,
    ->     description TEXT,
    ->     image_url VARCHAR(255) NOT NULL,
    ->     price DECIMAL(10, 2) NOT NULL,
    ->     inventory INT NOT NULL,
    ->     category VARCHAR(50),
    ->     weight_kg DECIMAL(10, 2),
    ->     roast_level VARCHAR(50),
    ->     country_of_origin VARCHAR(50)
    -> );
Query OK, 0 rows affected (0.006 sec)

mysql> INSERT INTO products (name, description, image_url, price, inventory, category, weight_kg, roast_level, country_of_origin) VALUES
    -> ('House Blend - Traditional', 'Our signature blend of Latin American beans, perfect for a classic cup of coffee.', 'https://placehold.co/300x300/A37A59/fff?text=House+Blend', 14.99, 50, 'Coffee Beans', 0.50, 'Medium', 'Colombia'),
    -> ('House Blend - Decaf', 'A decaffeinated version of our popular house blend with the same great taste.', 'https://placehold.co/300x300/A37A59/fff?text=Decaf+Blend', 15.99, 30, 'Coffee Beans', 0.50, 'Medium', 'Brazil'),
    -> ('Morning Glory Espresso', 'A bold and rich espresso blend with notes of dark chocolate and nuts.', 'https://placehold.co/300x300/A37A59/fff?text=Espresso', 16.99, 45, 'Coffee Beans', 0.50, 'Dark', 'Ethiopia'),
    -> ('French Roast', 'A very dark roast with a smoky flavor and low acidity.', 'https://placehold.co/300x300/A37A59/fff?text=French+Roast', 13.99, 60, 'Coffee Beans', 0.50, 'Dark', 'Indonesia'),
    -> ('Ethiopian Yirgacheffe', 'A light-bodied coffee with floral and citrus notes.', 'https://placehold.co/300x300/A37A59/fff?text=Yirgacheffe', 18.50, 25, 'Coffee Beans', 0.50, 'Light', 'Ethiopia'),
    -> ('Guatemalan Antigua', 'A sweet and balanced coffee with a hint of spice.', 'https://placehold.co/300x300/A37A59/fff?text=Antigua', 17.25, 40, 'Coffee Beans', 0.50, 'Medium', 'Guatemala'),
    -> ('Coffee Mug - The Daily Grind', 'Our branded ceramic coffee mug, perfect for your daily brew.', 'https://placehold.co/300x300/A37A59/fff?text=Coffee+Mug', 9.50, 100, 'Merchandise', 0.35, NULL, NULL),
    -> ('Pour-Over Coffee Maker', 'A simple and elegant pour-over brewer for the perfect single cup.', 'https://placehold.co/300x300/A37A59/fff?text=Pour-Over', 25.00, 15, 'Equipment', 0.80, NULL, NULL),
    -> ('French Press', 'A classic French press for a rich and full-bodied coffee.', 'https://placehold.co/300x300/A37A59/fff?text=French+Press', 29.00, 20, 'Equipment', 0.90, NULL, NULL),
    -> ('Organic Green Tea', 'A refreshing and smooth organic green tea blend.', 'https://placehold.co/300x300/A37A59/fff?text=Green+Tea', 11.00, 70, 'Tea', 0.25, NULL, 'China');
Query OK, 10 rows affected (0.006 sec)
Records: 10  Duplicates: 0  Warnings: 0

mysql> 
mysql> CREATE TABLE orders (
    ->     order_id INT AUTO_INCREMENT PRIMARY KEY,
    ->     customer_id INT,
    ->     paypal_order_id VARCHAR(255) NOT NULL,
    ->     total_amount DECIMAL(10, 2) NOT NULL,
    ->     order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    ->     FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
    -> );
Query OK, 0 rows affected (0.008 sec)

mysql> CREATE TABLE order_items (
    ->     order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    ->     order_id INT,
    ->     product_id INT,
    ->     quantity INT,
    ->     price DECIMAL(10, 2),
    ->     FOREIGN KEY (order_id) REFERENCES orders(order_id),
    ->     FOREIGN KEY (product_id) REFERENCES products(product_id)
    -> );
Query OK, 0 rows affected (0.015 sec)
