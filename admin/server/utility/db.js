require("dotenv").config();
const mysql = require("mysql2/promise");

const pool = mysql.createPool({
  host: process.env.MYSQL_HOST || "localhost",
  user: process.env.MYSQL_USER || "root",
  password:
    process.env.MYSQL_PASSWORD === undefined ? "" : process.env.MYSQL_PASSWORD,
  database: process.env.MYSQL_DATABASE || "wium_lie_demo",
  waitForConnections: true,
  connectionLimit: Number(process.env.MYSQL_POOL_LIMIT || 10),
  queueLimit: 0,
});

const query = async (sql, params = []) => {
  const [rows] = await pool.execute(sql, params);
  return rows;
};

module.exports = {
  pool,
  query,
};
