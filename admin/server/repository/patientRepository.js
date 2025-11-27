const db = require("../utility/db");

const patientSelect = `
  SELECT
    u.user_id,
    u.first_name,
    u.last_name,
    u.email,
    u.contactno,
    p.birth_date,
    p.medical_records
  FROM PATIENT p
  INNER JOIN USERS u ON u.user_id = p.user_id
`;

const createPatientProfile = async ({ user_id, birth_date, medical_records }) => {
  const sql = `
    INSERT INTO PATIENT (user_id, birth_date, medical_records)
    VALUES (?, ?, ?)`;
  return db.query(sql, [user_id, birth_date, medical_records || null]);
};

const updatePatientProfile = async (userId, data) => {
  const allowed = ["birth_date", "medical_records"];
  const fields = [];
  const values = [];

  allowed.forEach((field) => {
    if (data[field] !== undefined) {
      fields.push(`${field} = ?`);
      values.push(data[field]);
    }
  });

  if (!fields.length) {
    return { affectedRows: 0 };
  }

  values.push(userId);
  const sql = `UPDATE PATIENT SET ${fields.join(", ")} WHERE user_id = ?`;
  return db.query(sql, values);
};

const getAllPatients = async () => {
  const sql = `${patientSelect} ORDER BY u.created_at DESC`;
  return db.query(sql);
};

const deletePatientByUserId = async (userId) => {
  const sql = `DELETE FROM PATIENT WHERE user_id = ? LIMIT 1`;
  return db.query(sql, [userId]);
};

module.exports = {
  createPatientProfile,
  updatePatientProfile,
  getAllPatients,
  deletePatientByUserId,
};

