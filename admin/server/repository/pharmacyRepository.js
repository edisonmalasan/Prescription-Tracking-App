const db = require("../utility/db");

const pharmacySelect = `
  SELECT
    u.user_id,
    u.first_name,
    u.last_name,
    u.email,
    u.contactno,
    p.pharmacy_name,
    p.phar_license,
    p.open_time,
    p.close_time,
    p.dates_open,
    p.isVerified
  FROM pharmacy p
  INNER JOIN users u ON u.user_id = p.user_id
`;

const getAllPharmacies = async () => {
  const sql = `${pharmacySelect} ORDER BY u.created_at DESC`;
  return db.query(sql);
};

const getPharmacyByUserId = async (userId) => {
  const sql = `${pharmacySelect} WHERE u.user_id = ? LIMIT 1`;
  const rows = await db.query(sql, [userId]);
  return rows[0];
};

const createPharmacyProfile = async ({
  user_id,
  pharmacy_name,
  phar_license,
  open_time,
  close_time,
  dates_open,
  isVerified = 0,
}) => {
  const sql = `
    INSERT INTO pharmacy (
      user_id,
      pharmacy_name,
      phar_license,
      open_time,
      close_time,
      dates_open,
      isVerified
    )
    VALUES (?, ?, ?, ?, ?, ?, ?)`;
  return db.query(sql, [
    user_id,
    pharmacy_name,
    phar_license,
    open_time,
    close_time,
    dates_open,
    isVerified,
  ]);
};

const updatePharmacyProfile = async (userId, data) => {
  const allowed = [
    "pharmacy_name",
    "phar_license",
    "open_time",
    "close_time",
    "dates_open",
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

  const sql = `UPDATE pharmacy SET ${fields.join(", ")} WHERE user_id = ?`;
  return db.query(sql, values);
};

const setPharmacyVerification = async (userId, isVerified) => {
  const sql = `UPDATE phharmacy SET isVerified = ? WHERE user_id = ?`;
  return db.query(sql, [isVerified, userId]);
};

const deletePharmacyByUserId = async (userId) => {
  const sql = `DELETE FROM pharmacy WHERE user_id = ? LIMIT 1`;
  return db.query(sql, [userId]);
};

module.exports = {
  getAllPharmacies,
  getPharmacyByUserId,
  createPharmacyProfile,
  updatePharmacyProfile,
  setPharmacyVerification,
  deletePharmacyByUserId,
};

