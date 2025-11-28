const db = require("../utility/db");

const baseSelect = `
  SELECT
    drug_id,
    generic_name,
    brand,
    chemical_name,
    category,
    isControlled,
    created_at
  FROM DRUG
`;

const getAllDrugs = async () => {
  const sql = `${baseSelect} ORDER BY created_at DESC`;
  return db.query(sql);
};

const createDrug = async ({
  generic_name,
  brand,
  chemical_name,
  category,
  isControlled = 0,
}) => {
  const sql = `
    INSERT INTO DRUG (
      generic_name,
      brand,
      chemical_name,
      category,
      isControlled
    ) VALUES (?, ?, ?, ?, ?)
  `;

  const result = await db.query(sql, [
    generic_name,
    brand,
    chemical_name,
    category,
    Number(isControlled) ? 1 : 0,
  ]);

  return result.insertId;
};

const updateDrug = async (
  drugId,
  { generic_name, brand, chemical_name, category, isControlled }
) => {
  const fields = [];
  const params = [];

  const addField = (column, value) => {
    if (value !== undefined) {
      fields.push(`${column} = ?`);
      params.push(value);
    }
  };

  addField("generic_name", generic_name);
  addField("brand", brand);
  addField("chemical_name", chemical_name);
  addField("category", category);
  if (isControlled !== undefined) {
    fields.push("isControlled = ?");
    params.push(Number(isControlled) ? 1 : 0);
  }

  if (!fields.length) {
    return { affectedRows: 0 };
  }

  params.push(drugId);
  const sql = `UPDATE DRUG SET ${fields.join(", ")} WHERE drug_id = ?`;
  return db.query(sql, params);
};

const deleteDrug = async (drugId) => {
  const sql = `DELETE FROM DRUG WHERE drug_id = ? LIMIT 1`;
  return db.query(sql, [drugId]);
};

module.exports = {
  getAllDrugs,
  createDrug,
  updateDrug,
  deleteDrug,
};


