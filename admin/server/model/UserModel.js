class UserModel {
  static FIELDS = [
    "last_name",
    "first_name",
    "role",
    "email",
    "contactno",
    "address",
  ];

  static UPDATABLE_FIELDS = [
    "last_name",
    "first_name",
    "role",
    "email",
    "contactno",
    "address",
    "pass_hash", // will be converted to pass_hash
  ];

  static SUPPORTED_ROLES = new Set(["admin", "doctor", "patient", "pharmacy"]);

  static validateRole(role) {
    if (!this.SUPPORTED_ROLES.has(role)) {
      throw new Error(`Unsupported role: ${role}`);
    }
    return true;
  }

  static sanitize(user) {
    if (!user) return null;
    const { pass_hash, ...rest } = user;
    return rest;
  }

  static prepareCreateData(userData) {
    return {
      last_name: userData.last_name,
      first_name: userData.first_name,
      role: userData.role,
      email: userData.email,
      contactno: userData.contactno,
      pass_hash: userData.password,
      address: userData.address,
    };
  }

  static prepareUpdateData(userData) {
    const updatePayload = {};

    this.UPDATABLE_FIELDS.forEach((field) => {
      if (userData[field] !== undefined) {
        if (field === "password") {
          updatePayload.pass_hash = userData[field];
        } else {
          updatePayload[field] = userData[field];
        }
      }
    });

    return updatePayload;
  }
}

module.exports = UserModel;
