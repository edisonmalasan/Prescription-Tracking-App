const db = require("../utility/db");

const doctorSelect = `
  SELECT
    u.user_id,
    u.first_name,
    u.last_name,
    u.email,
    u.contactno,
    d.birth_date,
    d.specialization,
    d.prc_license,
    d.clinic_name,
    d.isVerified
  FROM doctor d
  INNER JOIN users u ON u.user_id = d.user_id
`;

const getAllDoctors = async () => {
  const sql = `${doctorSelect} ORDER BY u.created_at DESC`;
  return db.query(sql);
};

const getDoctorByUserId = async (userId) => {
  const sql = `${doctorSelect} WHERE u.user_id = ? LIMIT 1`;
  const rows = await db.query(sql, [userId]);
  return rows[0];
};

const createDoctorProfile = async ({
  user_id,
  birth_date,
  specialization,
  prc_license,
  clinic_name,
  isVerified = 0,
}) => {
  const sql = `
    INSERT INTO doctor (
      user_id,
      birth_date,
      specialization,
      prc_license,
      clinic_name,
      isVerified
    )
    VALUES (?, ?, ?, ?, ?, ?)`;
  return db.query(sql, [
    user_id,
    birth_date,
    specialization,
    prc_license,
    clinic_name,
    isVerified,
  ]);
};

const updateDoctorProfile = async (userId, data) => {
  const allowed = [
    "birth_date",
    "specialization",
    "prc_license",
    "clinic_name",
    "isVerified",
  ];
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

  const sql = `UPDATE doctor SET ${fields.join(", ")} WHERE user_id = ?`;
  return db.query(sql, values);
};

const setDoctorVerification = async (userId, isVerified) => {
  const sql = `UPDATE doctor SET isVerified = ? WHERE user_id = ?`;
  return db.query(sql, [isVerified, userId]);
};

const deleteDoctorByUserId = async (userId) => {
  const sql = `DELETE FROM doctor WHERE user_id = ? LIMIT 1`;
  return db.query(sql, [userId]);
};

module.exports = {
  getAllDoctors,
  getDoctorByUserId,
  createDoctorProfile,
  updateDoctorProfile,
  setDoctorVerification,
  deleteDoctorByUserId,
};

