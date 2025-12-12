console.log("Prescription Management JS loaded — MULTI ITEM ENABLED");

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

//state vars
let selectedPatient = null;
let selectedDrug = null;
let prescriptionItems = []; //for multiple prescrips

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

const cancelBtn = document.getElementById("cancel-presc");

let addItemBtn = null;
let itemsTable = null;
let finalCreateBtn = null;

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "DOCTOR") {
    window.location.href = "../../../public/login.html";
    return;
  }

  //inject dynamic UI (button + table)
  injectMultiItemUI();

  //patient saerch
  searchInput.addEventListener("input", async (e) => {
    const q = e.target.value.trim();
    if (!q) return (patientResults.innerHTML = "");

    try {
      const res = await api.get(`patientRoutes.php?action=all-fields`);
      const allPatients = res.patients ?? [];

      const matches = allPatients.filter((p) =>
        `${p.first_name} ${p.last_name}`.toLowerCase().includes(q.toLowerCase())
      );

      patientResults.innerHTML = matches
        .map(
          (p) => `
          <div class="search-item cursor-pointer px-4 py-2 hover:bg-blue-50 hover:text-blue-700 transition-colors border-b border-gray-100 last:border-0" data-id="${p.user_id}">
            <div class="font-medium">${p.first_name} ${p.last_name}</div>
          </div>`
        )
        .join("");

      document.querySelectorAll(".search-item").forEach((el) =>
        el.addEventListener("click", () => selectPatient(el.dataset.id))
      );
    } catch (err) {
      console.error("Error searching patients:", err);
    }
  });

  //drug search
  medInput.addEventListener("input", async (e) => {
    const q = e.target.value.trim();
    if (!q) return (drugSuggestions.innerHTML = "");

    try {
      const res = await api.get(`drugRoutes.php?action=search&search=${encodeURIComponent(q)}`);
      const drugs = res.drugs ?? [];

      drugSuggestions.innerHTML = drugs
        .map(
          (d) =>
            `<div class="search-item px-4 py-2 cursor-pointer hover:bg-blue-50 hover:text-blue-700 border-b border-gray-100 last:border-0 transition-colors" data-id="${d.drug_id}">
              <span class="font-bold">${d.brand}</span> <span class="text-sm text-gray-500">(${d.generic_name})</span>
            </div>`
        )
        .join("");

      document
        .querySelectorAll("#drug-suggestions .search-item")
        .forEach((el) =>
          el.addEventListener("click", () => {
            selectedDrug = { drug_id: el.dataset.id, name: el.textContent.trim() };
            medInput.value = selectedDrug.name;
            drugSuggestions.innerHTML = "";
          })
        );
    } catch (err) {
      console.error("Error fetching drugs:", err);
    }
  });

  addItemBtn.addEventListener("click", addItemToList);
  if(cancelBtn) cancelBtn.addEventListener("click", resetForm);
});

//multi item ui injection - UPDATED UI STRINGS
function injectMultiItemUI() {
  const detailsContainer = document.querySelector(".pt-4.border-t");

  // Create wrapper
  const wrapper = document.createElement("div");
  wrapper.className = "w-full";
  
  wrapper.innerHTML = `
      <div class="flex justify-end mb-6">
        <button id="add-item-btn"
          class="bg-gray-800 text-white px-5 py-2.5 rounded-lg hover:bg-gray-900 transition-colors font-medium text-sm flex items-center gap-2 shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Add to Prescription
        </button>
      </div>

      <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden mb-6 hidden" id="table-container">
        <table class="w-full text-left">
          <thead class="bg-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
            <tr>
              <th class="px-4 py-3">Drug</th>
              <th class="px-4 py-3">Dosage</th>
              <th class="px-4 py-3">Freq</th>
              <th class="px-4 py-3">Dur</th>
              <th class="px-4 py-3">Notes</th>
              <th class="px-4 py-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody id="items-table" class="bg-white divide-y divide-gray-100 text-sm"></tbody>
        </table>
      </div>

      <button id="final-create-btn"
        class="w-full bg-blue-600 text-white py-4 rounded-xl hover:bg-blue-700 transition-all font-bold text-lg shadow-lg shadow-blue-200 hidden transform hover:-translate-y-0.5">
        Issue Prescription
      </button>
  `;

  const cardBody = document.querySelector(".bg-white .p-6.space-y-5");
  if(cardBody) {
      cardBody.appendChild(wrapper);
  }

  addItemBtn = document.getElementById("add-item-btn");
  itemsTable = document.getElementById("items-table");
  finalCreateBtn = document.getElementById("final-create-btn");

  finalCreateBtn.addEventListener("click", () => {
    const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
    createPrescription(user);
  });
}

//select patient
async function selectPatient(patientId) {
  try {
    const [profileRes, recordRes] = await Promise.all([
      api.get(`patientRoutes.php?action=profile&user_id=${patientId}`),
      api.get(`patientRoutes.php?action=medical-record&user_id=${patientId}`),
    ]);

    selectedPatient = profileRes.patient ?? {};
    const record = recordRes.medical_record ?? {};

    // Updated UI for selected box
    selectedPatientBox.innerHTML = `
      <div class="flex items-center justify-between">
        <div>
            <div class="font-bold text-lg text-blue-900">${selectedPatient.first_name} ${selectedPatient.last_name}</div>
            <div class="text-xs text-blue-600">ID: ${selectedPatient.user_id}</div>
        </div>
        <div class="text-right">
             <div class="text-xs text-blue-500 uppercase font-bold">Contact</div>
             <div class="text-blue-800 font-medium">${selectedPatient.contactno ?? "N/A"}</div>
        </div>
      </div>
    `;
    selectedPatientBox.classList.remove("bg-blue-50", "border-blue-100");
    selectedPatientBox.classList.add("bg-blue-50", "border-blue-200", "shadow-inner");

    // Sidebar update
    allergiesBox.textContent = record.allergies ?? "None Reported";
    if((record.allergies || "").toLowerCase().includes("none")) {
         allergiesBox.className = "p-3 bg-green-50 border border-green-100 rounded-lg text-green-800 text-sm font-medium";
    } else {
         allergiesBox.className = "p-3 bg-red-50 border border-red-100 rounded-lg text-red-800 text-sm font-medium";
    }

    medicationsBox.textContent = record.medications ?? "None";

    patientResults.innerHTML = "";
    searchInput.value = `${selectedPatient.first_name} ${selectedPatient.last_name}`;
  } catch (err) {
    console.error("Error selecting patient:", err);
  }
}

//adding item to prescrip description
function addItemToList() {
  if (!selectedDrug) return alert("Please select a medication first.");

  const item = {
    drug_id: selectedDrug.drug_id,
    name: selectedDrug.name,
    dosage: dosageInput.value,
    frequency: freqInput.value,
    duration: durationInput.value,
    refills: parseInt(refillsInput.value) || 0,
    special_instructions: instructionsInput.value,
  };

  prescriptionItems.push(item);
  renderItemsTable();

  // clear fields
  medInput.value = "";
  dosageInput.value = "";
  freqInput.value = "";
  durationInput.value = "";
  refillsInput.value = 0;
  instructionsInput.value = "";
  selectedDrug = null;
}

function renderItemsTable() {
  const container = document.getElementById("table-container");
  
  itemsTable.innerHTML = prescriptionItems
    .map(
      (item, i) => `
      <tr class="hover:bg-blue-50 transition-colors group">
        <td class="px-4 py-3 font-medium text-gray-900">${item.name}</td>
        <td class="px-4 py-3 text-gray-600">${item.dosage}</td>
        <td class="px-4 py-3 text-gray-600">${item.frequency}</td>
        <td class="px-4 py-3 text-gray-600">${item.duration}</td>
        <td class="px-4 py-3 text-gray-500 italic truncate max-w-xs">${item.special_instructions || "-"}</td>
        <td class="px-4 py-3 text-right">
          <button class="text-red-400 hover:text-red-600 transition-colors text-xs font-bold uppercase" onclick="removeItem(${i})">
            Remove
          </button>
        </td>
      </tr>
    `
    )
    .join("");

    if (prescriptionItems.length > 0) {
      finalCreateBtn.classList.remove("hidden");
      container.classList.remove("hidden");
    } else {
      finalCreateBtn.classList.add("hidden");
      container.classList.add("hidden");
    }
}

window.removeItem = function (index) {
  prescriptionItems.splice(index, 1);
  renderItemsTable();
};

//creating prescrip
async function createPrescription(user) {
  if (!selectedPatient) return alert("Select a patient first.");
  if (prescriptionItems.length === 0) return alert("Add at least one prescription item.");

  try {
    // fetch record_id
    const recordRes = await api.get(
      `patientRoutes.php?action=medical-record&user_id=${selectedPatient.user_id}`
    );
    const record = recordRes.medical_record;

    const payload = {
      prescribing_doctor: user.user_id,
      record_id: record.record_id,
      prescription_date: new Date().toISOString().split("T")[0],
      status: "pending",
      details: prescriptionItems.map((item) => ({
        drug_id: item.drug_id,
        dosage: item.dosage,
        frequency: item.frequency,
        duration: item.duration,
        refills: item.refills,
        special_instructions: item.special_instructions,
      })),
    };

    const res = await api.post("prescriptionRoutes.php?action=create", payload);

    if (res.success) {
      alert("Prescription created successfully!");
      resetForm();
    } else {
      alert(res.error || "Failed to create prescription.");
    }
  } catch (err) {
    console.error("Error creating prescription:", err);
  }
}

function resetForm() {
  selectedPatient = null;
  selectedDrug = null;
  prescriptionItems = [];

  searchInput.value = "";
  
  // Reset UI Box
  selectedPatientBox.innerHTML = `<span class="text-blue-400 italic">No patient selected yet.</span>`;
  selectedPatientBox.className = "mt-4 p-4 bg-blue-50 border border-blue-100 rounded-lg text-blue-900 text-sm";
  
  allergiesBox.textContent = "—";
  allergiesBox.className = "p-3 bg-red-50 border border-red-100 rounded-lg text-red-800 text-sm font-medium"; // default
  medicationsBox.textContent = "—";

  medInput.value = "";
  dosageInput.value = "";
  freqInput.value = "";
  durationInput.value = "";
  refillsInput.value = 0;
  instructionsInput.value = "";

  itemsTable.innerHTML = "";
  document.getElementById("table-container").classList.add("hidden");
  document.getElementById("final-create-btn").classList.add("hidden");
}