const userRepository = require("../repository/userRepository");
const doctorRepository = require("../repository/doctorRepository");
const pharmacyRepository = require("../repository/pharmacyRepository");
const patientRepository = require("../repository/patientRepository");
const adminRepository = require("../repository/adminRepository");
const statsRepository = require("../repository/statsRepository");
const prescriptionRepository = require("../repository/prescriptionRepository");
const drugRepository = require("../repository/drugRepository");

// Models
const UserModel = require("../model/UserModel");
const RoleProfileMapper = require("../model/RoleProfileMapper");

const ServiceError = (message, status = 500) => {
  const error = new Error(message);
  error.status = status;
  return error;
};

const login = async ({ email, password }) => {
  const adminRecord = await userRepository.findAdminByEmail(email);
  if (!adminRecord) {
    throw ServiceError("Invalid credentials", 401);
  }

  if (password !== adminRecord.pass_hash) {
    throw ServiceError("Invalid credentials", 401);
  }

  return UserModel.sanitize(adminRecord);
};

const getDashboardSummary = async () => {
  const [counts, roleBreakdown, prescriptions] = await Promise.all([
    statsRepository.getEntityCounts(),
    userRepository.getRoleCounts(),
    prescriptionRepository.getPrescriptionList({ limit: 10 }),
  ]);

  return { counts, roleBreakdown, prescriptions };
};

const listUsers = async () => userRepository.getAllUsers();

const ensureUniqueEmail = async (email, currentUserId = null) => {
  const existing = await userRepository.findByEmail(email);
  if (existing && existing.user_id !== currentUserId) {
    throw ServiceError("Email already in use", 409);
  }
};

const attachRoleProfile = async (role, userId, profile = {}) => {
  // Normalize role to lowercase for internal logic
  const normalizedRole = role?.toLowerCase();
  
  if (normalizedRole === "admin") {
    await adminRepository.createAdminRecord(userId);
    return;
  }

  const profileData = RoleProfileMapper.prepareProfileData(normalizedRole, {
    ...profile,
    user_id: userId,
  });

  if (!profileData) {
    return;
  }

  switch (normalizedRole) {
    case "doctor":
      await doctorRepository.createDoctorProfile(profileData);
      break;
    case "pharmacy":
      await pharmacyRepository.createPharmacyProfile(profileData);
      break;
    case "patient":
      await patientRepository.createPatientProfile(profileData);
      break;
  }
};

const deleteRoleProfile = async (role, userId) => {
  const normalizedRole = role?.toLowerCase();
  switch (normalizedRole) {
    case "doctor":
      await doctorRepository.deleteDoctorByUserId(userId);
      break;
    case "pharmacy":
      await pharmacyRepository.deletePharmacyByUserId(userId);
      break;
    case "patient":
      await patientRepository.deletePatientByUserId(userId);
      break;
    case "admin":
      await adminRepository.deleteAdminRecord(userId);
      break;
    default:
      break;
  }
};

const createUser = async ({ user, profile }) => {
  UserModel.validateRole(user.role);

  await ensureUniqueEmail(user.email);

  const userData = UserModel.prepareCreateData(user);
  const userId = await userRepository.createUser(userData);

  await attachRoleProfile(user.role, userId, profile);

  return { user_id: userId };
};

const updateUser = async (userId, { user, profile }) => {
  const existingUser = await userRepository.findById(userId);
  if (!existingUser) {
    throw ServiceError("User not found", 404);
  }

  if (user && user.email) {
    await ensureUniqueEmail(user.email, userId);
  }

  if (user) {
    const updatePayload = UserModel.prepareUpdateData(user);
    if (Object.keys(updatePayload).length) {
      await userRepository.updateUser(userId, updatePayload);
    }
  }

  const targetRole = user?.role || existingUser.role;
  UserModel.validateRole(targetRole?.toLowerCase());

  const normalizedTargetRole = targetRole?.toLowerCase();
  if (profile && RoleProfileMapper.hasProfile(normalizedTargetRole)) {
    const profileUpdateData = RoleProfileMapper.prepareUpdateData(
      normalizedTargetRole,
      profile
    );

    if (profileUpdateData && Object.keys(profileUpdateData).length) {
      switch (normalizedTargetRole) {
        case "doctor":
          await doctorRepository.updateDoctorProfile(userId, profileUpdateData);
          break;
        case "pharmacy":
          await pharmacyRepository.updatePharmacyProfile(
            userId,
            profileUpdateData
          );
          break;
        case "patient":
          await patientRepository.updatePatientProfile(
            userId,
            profileUpdateData
          );
          break;
      }
    }
  }

  if (normalizedTargetRole === "admin" && existingUser.role?.toUpperCase() !== "ADMIN") {
    await adminRepository.createAdminRecord(userId);
  }

  return { user_id: userId };
};

const listDoctors = async () => doctorRepository.getAllDoctors();

const listPrescriptions = async ({ limit, offset }) =>
  prescriptionRepository.getPrescriptionList({
    limit: limit ? Number(limit) : undefined,
    offset: offset ? Number(offset) : undefined,
  });

const verifyDoctor = async (userId, isVerified) => {
  const result = await doctorRepository.setDoctorVerification(
    userId,
    isVerified
  );
  if (!result.affectedRows) {
    throw ServiceError("Doctor not found", 404);
  }
  return { user_id: userId, isVerified };
};

const listPharmacies = async () => pharmacyRepository.getAllPharmacies();

const verifyPharmacy = async (userId, isVerified) => {
  const result = await pharmacyRepository.setPharmacyVerification(
    userId,
    isVerified
  );
  if (!result.affectedRows) {
    throw ServiceError("Pharmacy not found", 404);
  }
  return { user_id: userId, isVerified };
};

const listDrugs = async () => drugRepository.getAllDrugs();

const createDrug = async (payload) => {
  const insertId = await drugRepository.createDrug(payload);
  return { drug_id: insertId };
};

const updateDrug = async (drugId, payload) => {
  const result = await drugRepository.updateDrug(drugId, payload);
  if (!result.affectedRows) {
    throw ServiceError("Drug not found", 404);
  }
  return { drug_id: drugId };
};

const deleteDrug = async (drugId) => {
  const result = await drugRepository.deleteDrug(drugId);
  if (!result.affectedRows) {
    throw ServiceError("Drug not found", 404);
  }
  return { drug_id: drugId };
};

const deleteUser = async (userId) => {
  const existingUser = await userRepository.findById(userId);
  if (!existingUser) {
    throw ServiceError("User not found", 404);
  }

  await deleteRoleProfile(existingUser.role, userId);
  const result = await userRepository.deleteUser(userId);

  if (!result.affectedRows) {
    throw ServiceError("Failed to delete user", 500);
  }

  return { user_id: userId };
};

module.exports = {
  login,
  getDashboardSummary,
  listUsers,
  createUser,
  updateUser,
  listDoctors,
  listPrescriptions,
  verifyDoctor,
  listPharmacies,
  verifyPharmacy,
  listDrugs,
  createDrug,
  updateDrug,
  deleteDrug,
  deleteUser,
};
