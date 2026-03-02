const mysql = require("mysql2/promise");
require("dotenv").config();

const adjustDatabase = async () => {
  try {
    // Create connection to the database
    const connection = await mysql.createConnection({
      host: process.env.DB_HOST || "localhost",
      user: process.env.DB_USER || "root",
      password: process.env.DB_PASSWORD || "",
      port: process.env.DB_PORT || 3306,
    });
    console.log("Connected to MySQL server");

    // Use database
    await connection.query(
      `USE ${process.env.DB_NAME || "resonanz_music_library"}`,
    );
    console.log(
      `✅ Database '${process.env.DB_NAME || "resonanz_music_library"}' verified`,
    );

    // Adjust library table to add location column
    await connection.query(`
      ALTER TABLE \`library\`
      ADD COLUMN \`location\` VARCHAR(255) NOT NULL AFTER instruments,
    `);
    console.log("✅ Library table updated! (add location column)");

    await connection.end();
    console.log("🎉 Database adjustment complete!");
    process.exit(0);
  } catch (error) {
    console.error("❌ Initialization failed:", error.message);
    process.exit(1);
  }
};

adjustDatabase();
