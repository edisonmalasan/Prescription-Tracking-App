const db = require("../utility/db");

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

module.exports = {
  getEntityCounts,
};


