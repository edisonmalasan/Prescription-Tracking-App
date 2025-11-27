const db = require("../utility/db");

const createAdminRecord = async (userId, isAdmin = 1) => {
  const sql = `INSERT INTO ADMIN (user_id, isAdmin) VALUES (?, ?)`;
  return db.query(sql, [userId, isAdmin]);
};

const deleteAdminRecord = async (userId) => {
  const sql = `DELETE FROM ADMIN WHERE user_id = ? LIMIT 1`;
  return db.query(sql, [userId]);
};

module.exports = {
  createAdminRecord,
  deleteAdminRecord,
};

