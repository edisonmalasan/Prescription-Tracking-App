const db = require("../utility/db");

const mapRecord = (row) => ({
  prescription_id: row.prescriptionId,
  prescription_date: row.prescriptionDate,
  status: row.status,
  created_at: row.prescriptionDate,
  doctor_id: row.doctorId,
  doctor_name: row.doctorName,
  patient_id: row.patientId,
  patient_name: row.patientName,
});

const getPrescriptionList = async ({ limit = 10, offset = 0 } = {}) => {
  const limitNum = Math.max(1, Math.min(1000, parseInt(limit, 10) || 10));
  const offsetNum = Math.max(0, parseInt(offset, 10) || 0);

  let sql = `
    SELECT
      p.prescription_id AS prescriptionId,
      p.prescription_date AS prescriptionDate,
      p.status,
      docUser.user_id AS doctorId,
      CONCAT(docUser.first_name, ' ', docUser.last_name) AS doctorName,
      patUser.user_id AS patientId,
      CONCAT(patUser.first_name, ' ', patUser.last_name) AS patientName
    FROM PRESCRIPTION p
    INNER JOIN USERS docUser ON docUser.user_id = p.prescribing_doctor
    LEFT JOIN MEDICALRECORD mr ON mr.record_id = p.record_id
    LEFT JOIN PATIENT pat ON pat.user_id = mr.user_id
    LEFT JOIN USERS patUser ON patUser.user_id = pat.user_id
    ORDER BY p.prescription_id DESC
    LIMIT ${limitNum}${offsetNum > 0 ? ` OFFSET ${offsetNum}` : ""}
  `;

  const rows = await db.query(sql);
  return rows.map(mapRecord);
};

module.exports = {
  getPrescriptionList,
};
