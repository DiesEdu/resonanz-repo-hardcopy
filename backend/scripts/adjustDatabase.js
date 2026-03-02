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

    // Adjust library table to change location_file to LONGTEXT
    await connection.query(`
      ALTER TABLE \`library\`
      MODIFY COLUMN \`location_file\` LONGTEXT NOT NULL;
      `);
    console.log(
      "✅ Library table updated! (location_file column changed to LONGTEXT)",
    );

    // Adjust library table to change description to LONGTEXT
    await connection.query(`
      ALTER TABLE \`library\`
      MODIFY COLUMN description LONGTEXT NOT NULL;
      `);
    console.log(
      "✅ Library table updated! (description column changed to LONGTEXT)",
    );

    await connection.end();
    console.log("🎉 Database adjustment complete!");
    process.exit(0);
  } catch (error) {
    console.error("❌ Initialization failed:", error.message);
    process.exit(1);
  }
};

adjustDatabase();
