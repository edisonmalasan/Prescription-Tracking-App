const db = require("../utility/db");

const allowedTables = new Set([
  "USERS",
  "DOCTOR",
  "PATIENT",
  "PHARMACY",
  "ADMIN",
  "MEDICALRECORD",
  "DRUG",
  "PRESCRIPTION",
  "PRESCRIPTIONDETAILS",
]);

const getEntityCounts = async () => {
  const sql = `
    SELECT
      (SELECT COUNT(*) FROM USERS) AS users,
      (SELECT COUNT(*) FROM DOCTOR) AS doctors,
      (SELECT COUNT(*) FROM PATIENT) AS patients,
      (SELECT COUNT(*) FROM PHARMACY) AS pharmacies,
      (SELECT COUNT(*) FROM MEDICALRECORD) AS medicalRecords,
      (SELECT COUNT(*) FROM PRESCRIPTION) AS prescriptions,
      (SELECT COUNT(*) FROM DRUG) AS drugs
  `;
  const rows = await db.query(sql);
  return rows[0];
};

const getTableMetadata = async () => {
  const sql = `
    SELECT
      TABLE_NAME as tableName,
      TABLE_ROWS as estimatedRows,
      DATA_LENGTH as dataLength,
      INDEX_LENGTH as indexLength,
      CREATE_TIME as createdAt,
      UPDATE_TIME as updatedAt
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = ?
      AND TABLE_NAME IN (${Array.from(allowedTables)
        .map(() => "?")
        .join(",")})
    ORDER BY TABLE_NAME ASC
  `;
  const params = [
    process.env.MYSQL_DATABASE,
    ...Array.from(allowedTables),
  ];
  return db.query(sql, params);
};

const getTableRecords = async (tableName, limit = 50) => {
  if (!allowedTables.has(tableName)) {
    const error = new Error("Table is not whitelisted");
    error.status = 400;
    throw error;
  }
  const sql = `SELECT * FROM ${tableName} ORDER BY 1 DESC LIMIT ?`;
  return db.query(sql, [limit]);
};

module.exports = {
  getEntityCounts,
  getTableMetadata,
  getTableRecords,
  allowedTables,
};

