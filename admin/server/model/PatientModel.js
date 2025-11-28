class PatientModel {
  static PROFILE_FIELDS = ["birth_date", "medical_records"];

  static prepareProfileData(profileData = {}) {
    return {
      user_id: profileData.user_id,
      birth_date: profileData.birth_date || null,
      medical_records: profileData.medical_records || null,
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

module.exports = PatientModel;



