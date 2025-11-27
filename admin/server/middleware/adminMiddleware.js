const { body, param, query } = require("express-validator");
const { allowedTables } = require("../repository/databaseRepository");

const supportedRoles = ["admin", "doctor", "patient", "pharmacy"];

const loginValidators = [
  body("email").isEmail().withMessage("Valid email required"),
  body("password").isLength({ min: 6 }).withMessage("Password required"),
];

const baseUserValidators = [
  body("user").isObject().withMessage("user payload is required"),
  body("user.first_name").notEmpty().withMessage("first_name is required"),
  body("user.last_name").notEmpty().withMessage("last_name is required"),
  body("user.email").isEmail().withMessage("Valid email is required"),
  body("user.contactno").notEmpty().withMessage("contact number is required"),
  body("user.address").notEmpty().withMessage("address is required"),
  body("user.role").isIn(supportedRoles).withMessage("role is invalid"),
];

const createUserValidators = [
  ...baseUserValidators,
  body("user.password")
    .isLength({ min: 6 })
    .withMessage("password must be at least 6 characters"),
];

const updateUserValidators = [
  param("userId").isInt().withMessage("userId must be numeric"),
  body("user").optional().isObject().withMessage("user must be an object"),
  body("user.email")
    .optional()
    .isEmail()
    .withMessage("Valid email is required"),
  body("user.role")
    .optional()
    .isIn(supportedRoles)
    .withMessage("role is invalid"),
  body("user.password")
    .optional()
    .isLength({ min: 6 })
    .withMessage("password must be at least 6 characters"),
  body("profile").optional().isObject(),
];

const verificationValidators = [
  param("userId").isInt().withMessage("userId must be numeric"),
  body("isVerified")
    .isInt({ min: 0, max: 1 })
    .withMessage("isVerified must be 0 or 1"),
];

const tableRecordValidators = [
  param("tableName")
    .custom((value) => allowedTables.has(value.toUpperCase()))
    .withMessage("Unsupported table"),
  query("limit")
    .optional()
    .isInt({ min: 1, max: 500 })
    .withMessage("limit must be 1-500"),
];

const userIdParamValidators = [
  param("userId").isInt().withMessage("userId must be numeric"),
];

module.exports = {
  loginValidators,
  createUserValidators,
  updateUserValidators,
  verificationValidators,
  tableRecordValidators,
  userIdParamValidators,
};
