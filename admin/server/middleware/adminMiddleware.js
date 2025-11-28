const { body, param } = require("express-validator");

const supportedRoles = ["admin", "doctor", "patient", "pharmacy"];

const loginValidators = [
  body("email").isEmail().withMessage("Valid email required"),
  body("password").isLength({ min: 6 }).withMessage("Password required"),
];

const baseUserValidators = [
  body("user")
    .isObject()
    .withMessage("user payload is required")
    .custom((value) => {
      if (!value || typeof value !== "object") {
        throw new Error("user must be an object");
      }
      return true;
    }),
  body("user.first_name")
    .trim()
    .notEmpty()
    .withMessage("first_name is required"),
  body("user.last_name").trim().notEmpty().withMessage("last_name is required"),
  body("user.email").trim().isEmail().withMessage("Valid email is required"),
  body("user.contactno")
    .optional({ nullable: true, checkFalsy: true })
    .trim()
    .custom((value) => {
      if (value === "" || value === null || value === undefined) {
        return true;
      }
      return true;
    }),
  body("user.address").trim().notEmpty().withMessage("address is required"),
  body("user.role")
    .trim()
    .toLowerCase()
    .isIn(supportedRoles)
    .withMessage(`role must be one of: ${supportedRoles.join(", ")}`),
];

const createUserValidators = [
  ...baseUserValidators,
  body("user.password")
    .trim()
    .notEmpty()
    .withMessage("password is required")
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

const userIdParamValidators = [
  param("userId").isInt().withMessage("userId must be numeric"),
];

const buildDrugValidators = (isOptional = false) => [
  isOptional
    ? body("generic_name").optional().trim()
    : body("generic_name")
        .trim()
        .notEmpty()
        .withMessage("generic_name is required"),
  body("brand").optional().trim(),
  body("chemical_name").optional().trim(),
  body("category").optional().trim(),
  body("isControlled")
    .optional()
    .isInt({ min: 0, max: 1 })
    .withMessage("isControlled must be 0 or 1"),
];

const drugCreateValidators = buildDrugValidators(false);

const drugUpdateValidators = [
  param("drugId").isInt().withMessage("drugId must be numeric"),
  ...buildDrugValidators(true),
];

const drugIdParamValidators = [
  param("drugId").isInt().withMessage("drugId must be numeric"),
];

module.exports = {
  loginValidators,
  createUserValidators,
  updateUserValidators,
  verificationValidators,
  userIdParamValidators,
  drugCreateValidators,
  drugUpdateValidators,
  drugIdParamValidators,
};
