-- Create Users table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE
);

-- Create Products table
CREATE TABLE Products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    tags VARCHAR(255),  -- comma-separated tags
    stock INT NOT NULL DEFAULT 0,
    discount_type ENUM('percentage', 'buy_n_get_m'),
    discount_value DECIMAL(5, 2),
    discount_buy INT,  -- 'n' in 'buy n get m'
    discount_get INT,  -- 'm' in 'buy n get m'
    start_date DATE,
    end_date DATE
);

-- Create Orders table
CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    product_ids VARCHAR(255) NOT NULL,  -- comma-separated product IDs
    quantities VARCHAR(255) NOT NULL,  -- comma-separated quantities matching product IDs
    prices VARCHAR(255) NOT NULL,  -- comma-separated prices matching product IDs
    total_price DECIMAL(10, 2) NOT NULL
);

-- Create Stock_Adjustments table
CREATE TABLE Stock_Adjustments (
    stock_adjustment_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    adjustment_type ENUM('addition', 'subtraction') NOT NULL,
    quantity INT NOT NULL,
    adjustment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE Discounts (
    discount_id INT AUTO_INCREMENT PRIMARY KEY,
    discount_name VARCHAR(100) NOT NULL,
    discount_type ENUM('percentage', 'buy_n_get_m') NOT NULL,
    discount_value DECIMAL(5, 2) NOT NULL,
    buy_quantity INT,  -- Only applicable for "buy n get m" discounts
    tags VARCHAR(255),  -- Comma-separated list of tags for products eligible for the discount
    start_date DATE NOT NULL,
    end_date DATE NOT NULL
);
