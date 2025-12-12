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

const createBtn = document.getElementById("create-presc");
const cancelBtn = document.getElementById("cancel-presc");

let addItemBtn = null;
let itemsTable = null;

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
          <div class="search-item cursor-pointer p-2 hover:bg-gray-100" data-id="${p.user_id}">
            ${p.first_name} ${p.last_name}
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
            `<div class="search-item p-2 cursor-pointer hover:bg-gray-100" data-id="${d.drug_id}">
              ${d.brand} (${d.generic_name})
            </div>`
        )
        .join("");

      document
        .querySelectorAll("#drug-suggestions .search-item")
        .forEach((el) =>
          el.addEventListener("click", () => {
            selectedDrug = { drug_id: el.dataset.id, name: el.textContent };
            medInput.value = selectedDrug.name;
            drugSuggestions.innerHTML = "";
          })
        );
    } catch (err) {
      console.error("Error fetching drugs:", err);
    }
  });

  addItemBtn.addEventListener("click", addItemToList);
  createBtn.addEventListener("click", () => createPrescription(user));
  cancelBtn.addEventListener("click", resetForm);
});

//multi item ui injecxtion
function injectMultiItemUI() {
  const detailsCard = medInput.closest(".bg-white");

  const wrapper = document.createElement("div");
  wrapper.innerHTML = `
      <button id="add-item-btn"
        class="mt-4 w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
        Add Item to Prescription
      </button>

      <table class="mt-4 w-full text-left border">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2">Drug</th>
            <th class="p-2">Dosage</th>
            <th class="p-2">Frequency</th>
            <th class="p-2">Duration</th>
            <th class="p-2">Refills</th>
            <th class="p-2">Instructions</th>
            <th class="p-2">Action</th>
          </tr>
        </thead>
        <tbody id="items-table"></tbody>
      </table>

      <!-- Create Rx button (HIDDEN until items exist) -->
      <button id="final-create-btn"
        class="mt-6 w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition hidden">
        Create Prescription
      </button>
  `;

  detailsCard.appendChild(wrapper);

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

    selectedPatientBox.innerHTML = `
      <strong>${selectedPatient.first_name} ${selectedPatient.last_name}</strong><br/>
      ${selectedPatient.contactno ?? ""}
    `;

    allergiesBox.textContent = record.allergies ?? "—";
    medicationsBox.textContent = record.medications ?? "—";

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
  itemsTable.innerHTML = prescriptionItems
    .map(
      (item, i) => `
      <tr class="border-t">
        <td class="p-2">${item.name}</td>
        <td class="p-2">${item.dosage}</td>
        <td class="p-2">${item.frequency}</td>
        <td class="p-2">${item.duration}</td>
        <td class="p-2">${item.refills}</td>
        <td class="p-2">${item.special_instructions}</td>
        <td class="p-2">
          <button class="text-red-600 hover:underline" onclick="removeItem(${i})">Remove</button>
        </td>
      </tr>
    `
    )
    .join("");

    if (prescriptionItems.length > 0) {
    finalCreateBtn.classList.remove("hidden");
  } else {
    finalCreateBtn.classList.add("hidden");
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
  selectedPatientBox.textContent = "No patient selected";
  allergiesBox.textContent = "—";
  medicationsBox.textContent = "—";

  medInput.value = "";
  dosageInput.value = "";
  freqInput.value = "";
  durationInput.value = "";
  refillsInput.value = 0;
  instructionsInput.value = "";

  itemsTable.innerHTML = "";
}
