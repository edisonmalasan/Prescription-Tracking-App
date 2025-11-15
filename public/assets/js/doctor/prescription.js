console.log("Prescription Management JS loaded");

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
    apiCall(`${API_BASE}/${endpoint}`, { method: "POST", body: JSON.stringify(data) }),
};

let selectedPatient = null;
let selectedDrug = null;

//DOM refs
const searchInput = document.getElementById("presc-search-patient");
const patientResults = document.getElementById("patient-search-results");
const selectedPatientBox = document.getElementById("selected-patient");
const allergiesBox = document.getElementById("patient-allergies");
const medicationsBox = document.getElementById("current-medications");

const medInput = document.getElementById("medication-name");
const drugSuggestions = document.getElementById("drug-suggestions");

const dosageInput = document.getElementById("dosage");
const freqInput = document.getElementById("frequency");
const durationInput = document.getElementById("duration");
const refillsInput = document.getElementById("refills");
const instructionsInput = document.getElementById("instructions");

const createBtn = document.getElementById("create-presc");
const cancelBtn = document.getElementById("cancel-presc");

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "DOCTOR") {
    window.location.href = "../../../public/login.html";
    return;
  }

  //patient search
  searchInput.addEventListener("input", async (e) => {
    const q = e.target.value.trim();
    if (!q) {
      patientResults.innerHTML = "";
      return;
    }

    try {
      const res = await api.get(`patientRoutes.php?action=by-doctor&user_id=${user.user_id}`);
      
      const allPatients = res.patients ?? [];
      const matches = allPatients.filter(p =>
        `${p.first_name} ${p.last_name}`.toLowerCase().includes(q.toLowerCase())
      );

      patientResults.innerHTML = matches
        .map(
          (p) =>
            `<div class="search-item" data-id="${p.user_id}">
              ${p.first_name} ${p.last_name} — ${p.contactno ?? ""}
            </div>`
        )
        .join("");

      document.querySelectorAll(".search-item").forEach((el) => {
        el.addEventListener("click", () => selectPatient(el.dataset.id, user));
      });
    } catch (err) {
      console.error("Error searching patients:", err);
    }
  });

  //drug auto
  medInput.addEventListener("input", async (e) => {
    const q = e.target.value.trim();
    if (!q) {
      drugSuggestions.innerHTML = "";
      return;
    }

    try {
      const res = await api.get(`drugRoutes.php?action=search&search=${encodeURIComponent(q)}`);
      const drugs = res.drugs ?? [];
      drugSuggestions.innerHTML = drugs
        .map((d) => `<div class="search-item" data-id="${d.drug_id}">${d.brand_name} (${d.generic_name})</div>`)
        .join("");  

      document.querySelectorAll("#drug-suggestions .search-item").forEach((el) => {
        el.addEventListener("click", () => {
          selectedDrug = { drug_id: el.dataset.id, name: el.textContent };
          medInput.value = selectedDrug.name;
          drugSuggestions.innerHTML = "";
        });
      });
    } catch (err) {
      console.error("Error fetching drugs:", err);
    }
  });

  //event listeners
  createBtn.addEventListener("click", () => createPrescription(user));
  cancelBtn.addEventListener("click", resetForm);
});

//patients
async function selectPatient(patientId, doctor) {
  try {
    const [profileRes, recordRes] = await Promise.all([
      api.get(`patientRoutes.php?action=profile&user_id=${patientId}`),
      api.get(`patientRoutes.php?action=medical-record&user_id=${patientId}`),
    ]);

    const profile = profileRes.patient ?? {};
    const record = recordRes.medical_record ?? {};

    selectedPatient = profile;

    selectedPatientBox.innerHTML = `
      <strong>${profile.first_name ?? "Unknown"} ${profile.last_name ?? ""}</strong><br/>
      ${profile.contactno ?? ""}
    `;

    allergiesBox.textContent = record.allergies ?? "—";
    medicationsBox.textContent = record.medications ?? "—";

    patientResults.innerHTML = "";
    searchInput.value = `${profile.first_name} ${profile.last_name}`;
  } catch (err) {
    console.error("Error selecting patient:", err);
    alert("Failed to load patient info");
  }
}

async function createPrescription(user) {
  if (!selectedPatient) {
    return alert("Please select a patient first.");
  }

  if (!selectedDrug) {
    return alert("Please select a medication.");
  }

  try {
    //get record for linking
    const recordRes = await api.get(
      `patientRoutes.php?action=medical-record&user_id=${selectedPatient.user_id}`
    );
    const record = recordRes.medical_record ?? null;
    if (!record) {
      return alert("No medical record found for this patient.");
    }

    const payload = {
      prescribing_doctor: user.user_id,
      record_id: record.record_id,
      prescription_date: new Date().toISOString().split("T")[0],
      status: "pending",
      details: [
        {
          drug_id: selectedDrug.drug_id,
          dosage: dosageInput.value.trim(),
          frequency: freqInput.value.trim(),
          duration: durationInput.value.trim(),
          refills: parseInt(refillsInput.value) || 0,
          special_instructions: instructionsInput.value.trim(),
        },
      ],
    };

    const response = await api.post("prescriptionRoutes.php?action=create", payload);

    if (response.success) {
      alert("Prescription created successfully!");
      resetForm();
    } else {
      console.error("Prescription error:", response);
      alert("Failed to create prescription.");
    }
  } catch (err) {
    console.error("Error creating prescription:", err);
    alert("Server error while creating prescription.");
  }
}

// === Reset Form ===
function resetForm() {
  selectedPatient = null;
  selectedDrug = null;

  searchInput.value = "";
  patientResults.innerHTML = "";
  selectedPatientBox.textContent = "No patient selected";
  allergiesBox.textContent = "—";
  medicationsBox.textContent = "—";

  medInput.value = "";
  drugSuggestions.innerHTML = "";
  dosageInput.value = "";
  freqInput.value = "";
  durationInput.value = "";
  refillsInput.value = 0;
  instructionsInput.value = "";
}
