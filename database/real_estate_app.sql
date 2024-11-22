-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS real_estate_app;
USE real_estate_app;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    facebook_profile VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Properties table
CREATE TABLE IF NOT EXISTS properties (
    property_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    property_type ENUM('house', 'apartment', 'condo', 'land') NOT NULL,
    transaction_type ENUM('sale', 'rent') NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    bedrooms INT,
    bathrooms INT,
    area_sqft DECIMAL(10, 2),
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    down_payment DECIMAL(12, 2),
    monthly_payment DECIMAL(12, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Property images table
CREATE TABLE IF NOT EXISTS property_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(property_id) ON DELETE CASCADE
);

-- Add constraint for max images if it doesn't exist
DELIMITER //
CREATE PROCEDURE add_max_images_constraint()
BEGIN
    IF NOT EXISTS (
        SELECT * FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
        AND CONSTRAINT_NAME = 'max_images'
        AND TABLE_NAME = 'property_images'
    ) THEN
        ALTER TABLE property_images
        ADD CONSTRAINT max_images CHECK (
            (SELECT COUNT(*) FROM property_images pi WHERE pi.property_id = property_id) <= 30
        );
    END IF;
END //
DELIMITER ;

CALL add_max_images_constraint();
DROP PROCEDURE IF EXISTS add_max_images_constraint;

-- Property features table
CREATE TABLE IF NOT EXISTS property_features (
    feature_id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    feature_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (property_id) REFERENCES properties(property_id) ON DELETE CASCADE
);

-- Bookmarks table
CREATE TABLE IF NOT EXISTS bookmarks (
    bookmark_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(property_id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, property_id)
);

-- Create indexes for better performance if they don't exist
CREATE INDEX IF NOT EXISTS idx_properties_transaction_type ON properties(transaction_type);
CREATE INDEX IF NOT EXISTS idx_properties_price ON properties(price);
CREATE INDEX IF NOT EXISTS idx_properties_location ON properties(city, state, zip_code);
CREATE INDEX IF NOT EXISTS idx_property_images_property_id ON property_images(property_id);
CREATE INDEX IF NOT EXISTS idx_property_features_property_id ON property_features(property_id);
CREATE INDEX IF NOT EXISTS idx_bookmarks_user_id ON bookmarks(user_id);