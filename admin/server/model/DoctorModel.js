class DoctorModel {
  static PROFILE_FIELDS = [
    "birth_date",
    "specialization",
    "prc_license",
    "clinic_name",
    "isVerified",
  ];

  static prepareProfileData(profileData = {}) {
    return {
      user_id: profileData.user_id,
      birth_date: profileData.birth_date || null,
      specialization: profileData.specialization || null,
      prc_license: profileData.prc_license || null,
      clinic_name: profileData.clinic_name || null,
      isVerified: profileData.isVerified || 0,
    };
  }

  static prepareUpdateData(profileData = {}) {
    const updatePayload = {};

    this.PROFILE_FIELDS.forEach((field) => {
      if (profileData[field] !== undefined) {
        updatePayload[field] = profileData[field];
      }
    });

    return updatePayload;
  }
}

module.exports = DoctorModel;



