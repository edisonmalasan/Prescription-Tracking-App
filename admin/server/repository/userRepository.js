const db = require("../utility/db");

const baseUserFields = [
  "user_id",
  "last_name",
  "first_name",
  "role",
  "email",
  "contactno",
  "address",
  "created_at",
];

const mapResult = (rows) =>
  rows.map((row) => {
    const mapped = {};
    baseUserFields.forEach((field) => {
      mapped[field] = row[field];
    });
    return mapped;
  });

const findAdminByEmail = async (email) => {
  const sql = `
    SELECT u.*, a.isAdmin
    FROM USERS u
    INNER JOIN ADMIN a ON a.user_id = u.user_id
    WHERE u.email = ?
    LIMIT 1`;
  const rows = await db.query(sql, [email]);
  return rows[0];
};

const findByEmail = async (email) => {
  const sql = `SELECT * FROM USERS WHERE email = ? LIMIT 1`;
  const rows = await db.query(sql, [email]);
  return rows[0];
};

const findById = async (userId) => {
  const sql = `SELECT * FROM USERS WHERE user_id = ? LIMIT 1`;
  const rows = await db.query(sql, [userId]);
  return rows[0];
};

const getAllUsers = async () => {
  const sql = `
    SELECT ${baseUserFields.join(", ")}
    FROM USERS
    ORDER BY created_at DESC`;
  const rows = await db.query(sql);
  return mapResult(rows);
};

const createUser = async ({
  last_name,
  first_name,
  role,
  email,
  contactno,
  pass_hash,
  address,
}) => {
  const contactnoValue =
    contactno && contactno.trim() !== "" ? contactno.trim() : null;

  const sql = `
    INSERT INTO USERS (last_name, first_name, role, email, contactno, pass_hash, address)
    VALUES (?, ?, ?, ?, ?, ?, ?)`;
  const result = await db.query(sql, [
    last_name,
    first_name,
    role,
    email,
    contactnoValue,
    pass_hash,
    address,
  ]);
  return result.insertId;
};

const updateUser = async (userId, data) => {
  const allowed = [
    "last_name",
    "first_name",
    "role",
    "email",
    "contactno",
    "pass_hash",
    "address",
  ];

  const fields = [];
  const values = [];

  allowed.forEach((field) => {
    if (data[field] !== undefined) {
      fields.push(`${field} = ?`);
      if (field === "contactno") {
        values.push(
          data[field] && data[field].trim() !== "" ? data[field].trim() : null
        );
      } else {
        values.push(data[field]);
      }
    }
  });

  if (!fields.length) {
    return { affectedRows: 0 };
  }

  values.push(userId);

  const sql = `UPDATE USERS SET ${fields.join(", ")} WHERE user_id = ?`;
  return db.query(sql, values);
};

const deleteUser = async (userId) => {
  const sql = `DELETE FROM USERS WHERE user_id = ? LIMIT 1`;
  return db.query(sql, [userId]);
};

const getRoleCounts = async () => {
  const sql = `
    SELECT role, COUNT(*) as count
    FROM USERS
    GROUP BY role`;
  return db.query(sql);
};

module.exports = {
  findAdminByEmail,
  findByEmail,
  findById,
  getAllUsers,
  createUser,
  updateUser,
  deleteUser,
  getRoleCounts,
};
