console.log("Doctor Profile JS loaded");

const API_BASE = "../../../src/api";

const apiCall = async (url, options = {}) => {
  const response = await fetch(url, {
    headers: { "Content-Type": "application/json", ...options.headers },
    ...options,
  });
  const text = await response.text();

  const jsonStart = text.indexOf("{");
  const cleanText = jsonStart >= 0 ? text.slice(jsonStart) : text;

  try {
    return JSON.parse(cleanText);
  } catch (err) {
    console.error("Invalid JSON from server:", text);
    throw err;
  }
};

const api = {
  get: (endpoint) => apiCall(`${API_BASE}/${endpoint}`),
  post: (endpoint, data) =>
    apiCall(`${API_BASE}/${endpoint}`, {
      method: "POST",
      body: JSON.stringify(data),
    }),
  put: (endpoint, data) =>
    apiCall(`${API_BASE}/${endpoint}`, {
      method: "PUT",
      body: JSON.stringify(data),
    }),
};

//dom refs
const profileView = document.getElementById("profile-view");
const profileForm = document.getElementById("profile-form");
const editBtn = document.getElementById("edit-profile-btn");
const cancelBtn = document.getElementById("cancel-profile");
const saveBtn = document.getElementById("save-profile");

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));

  if (!user || user.role !== "DOCTOR") {
    window.location.href = "../../../public/login.html";
    return;
  }

  try {
    await loadDoctorProfile(user.user_id);
  } catch (err) {
    console.error("Failed to load doctor profile:", err);
    alert("Failed to load profile data.");
  }

  //event listeners
  editBtn.addEventListener("click", enableEditMode);
  cancelBtn.addEventListener("click", cancelEdit);
  profileForm.addEventListener("submit", saveProfile);
});

async function loadDoctorProfile(doctorId) {
  const res = await api.get(
    `doctorRoutes.php?action=profile&user_id=${doctorId}`
  );

  if (!res || !res.success || !res.doctor) {
    throw new Error("Invalid response from server");
  }

  const d = res.doctor;

  //display refs
  document.getElementById(
    "profile-name"
  ).textContent = `Dr. ${d.first_name} ${d.last_name}`;
  document.getElementById("profile-special").textContent = `${
    d.specialization ?? "—"
  }`;
  document.getElementById("profile-prc").textContent = `${
    d.prc_license ?? "—"
  }`;
  document.getElementById("profile-contact").textContent = `${
    d.contactno ?? "—"
  }`;
  document.getElementById("profile-email").textContent = `${d.email ?? "—"}`;
  document.getElementById("profile-clinic").textContent = `${
    d.clinic_name ?? "—"
  }`;

  //form refs
  profileForm.querySelector("#first_name").value = d.first_name ?? "";
  profileForm.querySelector("#last_name").value = d.last_name ?? "";
  profileForm.querySelector("#contactno").value = d.contactno ?? "";
  profileForm.querySelector("#email").value = d.email ?? "";
  profileForm.querySelector("#prc_license").value = d.prc_license ?? "";
  profileForm.querySelector("#specialization").value = d.specialization ?? "";
  profileForm.querySelector("#clinic_name").value = d.clinic_name ?? "";

  toggleForm(false);
}

function toggleForm(editMode) {
  profileForm.querySelectorAll("input").forEach((inp) => {
    const alwaysDisabled = ["first_name", "last_name", "contactno", "email"];
    inp.disabled = alwaysDisabled.includes(inp.id) || !editMode;
  });

  profileView.style.display = editMode ? "none" : "block";
  profileForm.style.display = editMode ? "block" : "none";
}

function enableEditMode() {
  toggleForm(true);
}

function cancelEdit() {
  toggleForm(false);
}

async function saveProfile(e) {
  e.preventDefault();
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user) return alert("Session expired. Please log in again.");

  const updated = {
    // first_name: document.getElementById("first_name").value.trim(),
    // last_name: document.getElementById("last_name").value.trim(),
    // contactno: document.getElementById("contactno").value.trim(),
    // email: document.getElementById("email").value.trim(),
    prc_license: document.getElementById("prc_license").value.trim(),
    specialization: document.getElementById("specialization").value.trim(),
    clinic_name: document.getElementById("clinic_name").value.trim(),
    // address: document.getElementById("address").value.trim(),
  };

  try {
    const res = await api.put(
      `doctorRoutes.php?action=profile&user_id=${user.user_id}`,
      updated
    );
    if (res.success) {
      alert("Profile updated successfully!");
      await loadDoctorProfile(user.user_id);
      toggleForm(false);
    } else {
      console.error("Update failed:", res);
      alert(res.error || "Failed to update profile.");
    }
  } catch (err) {
    console.error("Error updating profile:", err);
    alert("Server error while saving profile.");
  }
}
