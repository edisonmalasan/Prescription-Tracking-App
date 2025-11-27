const express = require("express");
const adminController = require("../controller/adminController");
const {
  loginValidators,
  createUserValidators,
  updateUserValidators,
  verificationValidators,
  tableRecordValidators,
  userIdParamValidators,
} = require("../middleware/adminMiddleware");

const router = express.Router();

router.post("/auth/login", loginValidators, adminController.login);

router.get("/dashboard/summary", adminController.getDashboardSummary);
router.get("/users", adminController.listUsers);
router.get("/doctors", adminController.listDoctors);
router.get("/pharmacies", adminController.listPharmacies);
router.get("/database/metadata", adminController.getTableMetadata);

router.get(
  "/database/records/:tableName",
  tableRecordValidators,
  adminController.getTableRecords
);

router.post("/users", createUserValidators, adminController.createUser);

router.put("/users/:userId", updateUserValidators, adminController.updateUser);

router.delete(
  "/users/:userId",
  userIdParamValidators,
  adminController.deleteUser
);

router.patch(
  "/doctors/:userId/verify",
  verificationValidators,
  adminController.verifyDoctor
);

router.patch(
  "/pharmacies/:userId/verify",
  verificationValidators,
  adminController.verifyPharmacy
);

module.exports = router;
