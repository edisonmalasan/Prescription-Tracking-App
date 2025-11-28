const DoctorModel = require("./DoctorModel");
const PharmacyModel = require("./PharmacyModel");
const PatientModel = require("./PatientModel");

class RoleProfileMapper {
  static PROFILE_MODELS = {
    doctor: DoctorModel,
    pharmacy: PharmacyModel,
    patient: PatientModel,
  };

  static prepareProfileData(role, profileData) {
    const Model = this.PROFILE_MODELS[role];
    if (!Model) {
      return null;
    }
    return Model.prepareProfileData(profileData);
  }

  static prepareUpdateData(role, profileData) {
    const Model = this.PROFILE_MODELS[role];
    if (!Model) {
      return null;
    }
    return Model.prepareUpdateData(profileData);
  }

  static hasProfile(role) {
    return role in this.PROFILE_MODELS;
  }
}

module.exports = RoleProfileMapper;



