console.log("Patient Profile JS loaded");

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
};

//dom refs
const nameEl = document.getElementById("patient-name");
const idEl = document.getElementById("patient-id");
const firstNameEl = document.getElementById("first-name");
const lastNameEl = document.getElementById("last-name");
const emailEl = document.getElementById("email");
const contactEl = document.getElementById("contactno");
const addressEl = document.getElementById("address");
const birthDateEl = document.getElementById("birth-date");
const ageEl = document.getElementById("age");
const allergiesEl = document.getElementById("allergies");

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "PATIENT") {
    window.location.href = "../../../public/login.html";
    return;
  }

  try {
    await loadPatientProfile(user.user_id);
    await loadMedicalRecord(user.user_id);
  } catch (err) {
    console.error("Error loading patient profile:", err);
    alert("Failed to load patient profile.");
  }
});

async function loadPatientProfile(patientId) {
  const res = await api.get(`patientRoutes.php?action=profile&user_id=${patientId}`);
  if (!res.success || !res.patient) throw new Error(res.error || "Profile not found");

  const p = res.patient;

  nameEl.textContent = `Welcome, ${p.first_name ?? "—"} ${p.last_name ?? ""}`;
  idEl.textContent = `Patient ID: ${patientId}`;

  firstNameEl.textContent = p.first_name ?? "—";
  lastNameEl.textContent = p.last_name ?? "—";
  emailEl.textContent = p.email ?? "—";
  contactEl.textContent = p.contactno ?? "—";
  addressEl.textContent = p.address ?? "—";
  birthDateEl.textContent = p.birth_date ? formatDate(p.birth_date) : "—";
  ageEl.textContent = formatAge(p.birth_date);
}

async function loadMedicalRecord(patientId) {
  const res = await api.get(`patientRoutes.php?action=medical-record&user_id=${patientId}`);
  if (!res.success || !res.medical_record) {
    allergiesEl.textContent = "—";
    return;
  }

  const record = res.medical_record;
  allergiesEl.textContent = record.allergies ?? "—";
}

function formatDate(dateStr) {
  if (!dateStr) return "—";
  const d = new Date(dateStr);
  return isNaN(d) ? "—" : d.toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" });
}

function formatAge(dob) {
  if (!dob) return "—";
  const birth = new Date(dob);
  const today = new Date();
  let age = today.getFullYear() - birth.getFullYear();
  const m = today.getMonth() - birth.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
    age--;
  }
  return age >= 0 ? age : "—";
}
