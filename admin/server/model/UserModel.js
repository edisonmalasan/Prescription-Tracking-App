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
    "pass_hash",
  ];

  static SUPPORTED_ROLES = new Set(["admin", "doctor", "patient", "pharmacy"]);

  static validateRole(role) {
    const normalizedRole = role?.toLowerCase();
    if (!this.SUPPORTED_ROLES.has(normalizedRole)) {
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
      role: userData.role?.toUpperCase() || userData.role,
      email: userData.email,
      contactno: userData.contactno?.trim() || null,
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
        } else if (field === "role") {
          updatePayload[field] =
            userData[field]?.toUpperCase() || userData[field];
        } else if (field === "contactno") {
          updatePayload[field] = userData[field]?.trim() || null;
        } else {
          updatePayload[field] = userData[field];
        }
      }
    });

    return updatePayload;
  }
}

module.exports = UserModel;
