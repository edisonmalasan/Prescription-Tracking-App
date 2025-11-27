const { validationResult } = require("express-validator");
const adminService = require("../service/adminService");

const handleValidation = (req) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    const error = new Error("Validation failed");
    error.status = 422;
    error.details = errors.array();
    throw error;
  }
};

const login = async (req, res, next) => {
  try {
    handleValidation(req);
    const admin = await adminService.login(req.body);
    res.json({ success: true, admin });
  } catch (error) {
    next(error);
  }
};

const getDashboardSummary = async (req, res, next) => {
  try {
    const summary = await adminService.getDashboardSummary();
    res.json({ success: true, summary });
  } catch (error) {
    next(error);
  }
};

const listUsers = async (req, res, next) => {
  try {
    const users = await adminService.listUsers();
    res.json({ success: true, users });
  } catch (error) {
    next(error);
  }
};

const createUser = async (req, res, next) => {
  try {
    handleValidation(req);
    const result = await adminService.createUser(req.body);
    res.status(201).json({ success: true, ...result });
  } catch (error) {
    next(error);
  }
};

const updateUser = async (req, res, next) => {
  try {
    handleValidation(req);
    const result = await adminService.updateUser(
      Number(req.params.userId),
      req.body
    );
    res.json({ success: true, ...result });
  } catch (error) {
    next(error);
  }
};

const listDoctors = async (req, res, next) => {
  try {
    const doctors = await adminService.listDoctors();
    res.json({ success: true, doctors });
  } catch (error) {
    next(error);
  }
};

const verifyDoctor = async (req, res, next) => {
  try {
    handleValidation(req);
    const { isVerified } = req.body;
    const result = await adminService.verifyDoctor(
      Number(req.params.userId),
      Number(isVerified)
    );
    res.json({ success: true, ...result });
  } catch (error) {
    next(error);
  }
};

const listPharmacies = async (req, res, next) => {
  try {
    const pharmacies = await adminService.listPharmacies();
    res.json({ success: true, pharmacies });
  } catch (error) {
    next(error);
  }
};

const verifyPharmacy = async (req, res, next) => {
  try {
    handleValidation(req);
    const { isVerified } = req.body;
    const result = await adminService.verifyPharmacy(
      Number(req.params.userId),
      Number(isVerified)
    );
    res.json({ success: true, ...result });
  } catch (error) {
    next(error);
  }
};

const getTableMetadata = async (req, res, next) => {
  try {
    const tables = await adminService.getTableMetadata();
    res.json({ success: true, tables });
  } catch (error) {
    next(error);
  }
};

const getTableRecords = async (req, res, next) => {
  try {
    handleValidation(req);
    const { tableName } = req.params;
    const { limit } = req.query;
    const records = await adminService.getTableRecords(
      tableName,
      limit ? Number(limit) : undefined
    );
    res.json({ success: true, records });
  } catch (error) {
    next(error);
  }
};

const deleteUser = async (req, res, next) => {
  try {
    const result = await adminService.deleteUser(Number(req.params.userId));
    res.json({ success: true, ...result });
  } catch (error) {
    next(error);
  }
};

module.exports = {
  login,
  getDashboardSummary,
  listUsers,
  createUser,
  updateUser,
  listDoctors,
  verifyDoctor,
  listPharmacies,
  verifyPharmacy,
  getTableMetadata,
  getTableRecords,
  deleteUser,
};
