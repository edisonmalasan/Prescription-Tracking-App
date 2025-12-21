const db = require("../utility/db");

const getEntityCounts = async () => {
  const sql = `
    SELECT
      (SELECT COUNT(*) FROM users) AS users,
      (SELECT COUNT(*) FROM doctor) AS doctors,
      (SELECT COUNT(*) FROM patient) AS patients,
      (SELECT COUNT(*) FROM pharmacy) AS pharmacies,
      (SELECT COUNT(*) FROM medicalrecord) AS medicalRecords,
      (SELECT COUNT(*) FROM prescription) AS prescriptions,
      (SELECT COUNT(*) FROM drug) AS drugs
  `;
  const rows = await db.query(sql);
  return rows[0];
};

module.exports = {
  getEntityCounts,
};


