class PharmacyModel {
  static PROFILE_FIELDS = [
    "pharmacy_name",
    "phar_license",
    "open_time",
    "close_time",
    "dates_open",
    "isVerified",
  ];

  static prepareProfileData(profileData = {}) {
    return {
      user_id: profileData.user_id,
      pharmacy_name: profileData.pharmacy_name || null,
      phar_license: profileData.phar_license || null,
      open_time: profileData.open_time || null,
      close_time: profileData.close_time || null,
      dates_open: profileData.dates_open || null,
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

module.exports = PharmacyModel;



