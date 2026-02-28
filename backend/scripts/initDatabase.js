const mysql = require("mysql2/promise");
require("dotenv").config();

const initDatabase = async () => {
  try {
    // Create connection without database
    const connection = await mysql.createConnection({
      host: process.env.DB_HOST || "localhost",
      user: process.env.DB_USER || "root",
      password: process.env.DB_PASSWORD || "",
      port: process.env.DB_PORT || 3306,
    });

    console.log("Connected to MySQL server");

    // Create database if not exists
    await connection.query(`
      CREATE DATABASE IF NOT EXISTS ${process.env.DB_NAME || "resonanz_music_library"}
      CHARACTER SET utf8mb4 
      COLLATE utf8mb4_unicode_ci
    `);

    console.log(
      `✅ Database '${process.env.DB_NAME || "resonanz_music_library"}' created/verified`,
    );

    // Use database
    await connection.query(
      `USE ${process.env.DB_NAME || "resonanz_music_library"}`,
    );

    // Create expenses table
    await connection.query(`
      CREATE TABLE IF NOT EXISTS \`library\` (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        composer VARCHAR(255) NOT NULL,
        genre VARCHAR(255) NOT NULL,
        difficulty ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') NOT NULL DEFAULT 'Beginner',
        pages INT(4) NOT NULL,
        description VARCHAR(255) NOT NULL,
        coverImage VARCHAR(255) NOT NULL,
        fileUrl VARCHAR(255) NOT NULL, 
        uploadDate DATE NOT NULL,
        instruments JSON NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_title (title),
        INDEX idx_composer (composer),
        INDEX idx_created (created_at)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    `);

    console.log("✅ Library table created/verified");

    // Create users table for development login/register
    await connection.query(`
      CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_users_email (email)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    `);

    console.log("✅ Users table created/verified");

    await connection.end();
    console.log("🎉 Database initialization complete!");
    process.exit(0);
  } catch (error) {
    console.error("❌ Initialization failed:", error.message);
    process.exit(1);
  }
};

initDatabase();
