const express = require("express");
const adminController = require("../controller/adminController");
const {
  loginValidators,
  createUserValidators,
  updateUserValidators,
  verificationValidators,
  userIdParamValidators,
  drugCreateValidators,
  drugUpdateValidators,
  drugIdParamValidators,
} = require("../middleware/adminMiddleware");

const router = express.Router();

router.post("/auth/login", loginValidators, adminController.login);

router.get("/dashboard/summary", adminController.getDashboardSummary);
router.get("/prescriptions", adminController.listPrescriptions);
router.get("/users", adminController.listUsers);
router.get("/doctors", adminController.listDoctors);
router.get("/pharmacies", adminController.listPharmacies);
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

router.get("/drugs", adminController.listDrugs);
router.post("/drugs", drugCreateValidators, adminController.createDrug);
router.put(
  "/drugs/:drugId",
  drugUpdateValidators,
  adminController.updateDrug
);
router.delete(
  "/drugs/:drugId",
  drugIdParamValidators,
  adminController.deleteDrug
);

module.exports = router;
