const userRepository = require("../repository/userRepository");
const doctorRepository = require("../repository/doctorRepository");
const pharmacyRepository = require("../repository/pharmacyRepository");
const patientRepository = require("../repository/patientRepository");
const databaseRepository = require("../repository/databaseRepository");
const adminRepository = require("../repository/adminRepository");

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
  const counts = await databaseRepository.getEntityCounts();
  const roleBreakdown = await userRepository.getRoleCounts();
  return { counts, roleBreakdown };
};

const listUsers = async () => userRepository.getAllUsers();

const ensureUniqueEmail = async (email, currentUserId = null) => {
  const existing = await userRepository.findByEmail(email);
  if (existing && existing.user_id !== currentUserId) {
    throw ServiceError("Email already in use", 409);
  }
};

const attachRoleProfile = async (role, userId, profile = {}) => {
  if (role === "admin") {
    await adminRepository.createAdminRecord(userId);
    return;
  }

  const profileData = RoleProfileMapper.prepareProfileData(role, {
    ...profile,
    user_id: userId,
  });

  if (!profileData) {
    return;
  }

  switch (role) {
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
  switch (role) {
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
  UserModel.validateRole(targetRole);

  if (profile && RoleProfileMapper.hasProfile(targetRole)) {
    const profileUpdateData = RoleProfileMapper.prepareUpdateData(
      targetRole,
      profile
    );
    
    if (profileUpdateData && Object.keys(profileUpdateData).length) {
      switch (targetRole) {
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
          await patientRepository.updatePatientProfile(userId, profileUpdateData);
          break;
      }
    }
  }

  if (targetRole === "admin" && existingUser.role !== "admin") {
    await adminRepository.createAdminRecord(userId);
  }

  return { user_id: userId };
};

const listDoctors = async () => doctorRepository.getAllDoctors();

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

const getTableRecords = async (tableName, limit) =>
  databaseRepository.getTableRecords(tableName.toUpperCase(), limit);

const getTableMetadata = async () => databaseRepository.getTableMetadata();

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
  verifyDoctor,
  listPharmacies,
  verifyPharmacy,
  getTableRecords,
  getTableMetadata,
  deleteUser,
};
