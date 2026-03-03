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

    // // Adjust library table to change location_file to LONGTEXT
    // await connection.query(`
    //   ALTER TABLE \`library\`
    //   MODIFY COLUMN \`location_file\` LONGTEXT NOT NULL;
    //   `);
    // console.log(
    //   "✅ Library table updated! (location_file column changed to LONGTEXT)",
    // );

    // // Adjust library table to change description to LONGTEXT
    // await connection.query(`
    //   ALTER TABLE \`library\`
    //   MODIFY COLUMN description LONGTEXT NOT NULL;
    //   `);
    // console.log(
    //   "✅ Library table updated! (description column changed to LONGTEXT)",
    // );

    // // Remove unused columns from library table
    // await connection.query(`
    //   ALTER TABLE \`library\`
    //   DROP COLUMN \`fileUrl\`,
    //   DROP COLUMN \`uploadDate\`;
    //   `);
    // console.log(
    //   "✅ Library table updated! (fileUrl and uploadDate columns removed)",
    // );

    // // add column score_type to library table
    // await connection.query(`
    //   ALTER TABLE \`library\`
    //   ADD COLUMN score_type ENUM('Full Score', 'Orchestra Collections', 'Single') NOT NULL DEFAULT 'Full Score';
    //   `);
    // console.log("✅ Library table updated! (score_type column added)");

    // adjust column genre, add christmas and pop
    await connection.query(`
      ALTER TABLE \`library\`
      MODIFY COLUMN genre ENUM('Classical', 'Baroque', 'Romantic', 'Impressionist', 'Modern', 'Jazz', 'Contemporary', 'Christmas', 'Pop') NOT NULL;
    `);
    console.log(
      "✅ Library table updated! (genre column adjusted to include Christmas and Pop)",
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
