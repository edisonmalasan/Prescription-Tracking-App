const API_BASE = "../../src/api";

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  return res.json();
}

let selectedPatient = null;
let selectedDrug = null;

async function searchPatients(query) {
  if (!query) return [];
  // get all patients and filter client-side for simplicity
  const res = await fetchJSON(`${API_BASE}/patientRoutes.php?action=all`);
  const patients = res.patients ?? [];
  return patients.filter(p => (`${p.first_name} ${p.last_name}`).toLowerCase().includes(query.toLowerCase()));
}

async function searchDrugs(query) {
  if (!query) return [];
  // use drugRoutes search if exists, fallback to all
  try {
    const res = await fetchJSON(`${API_BASE}/drugRoutes.php?action=search&q=${encodeURIComponent(query)}`);
    return res.drugs ?? [];
  } catch (e) {
    const res = await fetchJSON(`${API_BASE}/drugRoutes.php?action=all`);
    return (res.drugs ?? []).filter(d => (d.generic_name + ' ' + d.brand).toLowerCase().includes(query.toLowerCase()));
  }
}

// Wire UI
document.addEventListener("DOMContentLoaded", () => {
  const patientInput = document.getElementById("presc-search-patient");
  const patientResults = document.getElementById("patient-search-results");
  const selectedPatientBox = document.getElementById("selected-patient");
  const allergiesBox = document.getElementById("patient-allergies");
  const currentMedsBox = document.getElementById("current-medications");

  patientInput.addEventListener("input", async (e) => {
    const q = e.target.value.trim();
    patientResults.innerHTML = '';
    if (!q) return;
    const hits = await searchPatients(q);
    hits.slice(0, 6).forEach(p => {
      const div = document.createElement("div");
      div.className = 'search-item';
      div.textContent = `${p.first_name} ${p.last_name} — ${p.contactno ?? ''}`;
      div.addEventListener("click", async () => {
        selectedPatient = p;
        selectedPatientBox.innerHTML = `<strong>${p.first_name} ${p.last_name}</strong><div>Age: ${p.birth_date ? (new Date().getFullYear() - new Date(p.birth_date).getFullYear()) : '—'}</div><div>Contact: ${p.contactno ?? '—'}</div>`;
        patientResults.innerHTML = '';
        // fetch medical record for allergies + current meds via patientRoutes.php?action=medical-record
        const record = await fetchJSON(`${API_BASE}/patientRoutes.php?action=medical-record&user_id=${p.user_id}`);
        allergiesBox.textContent = (record.record && record.record.allergies) ? record.record.allergies : 'None';
        // current medications - fetch prescriptions by patient
        const rxRes = await fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=by-patient&patient_id=${p.user_id}`);
        const meds = (rxRes.prescriptions ?? []).flatMap(r => r.details ?? []).map(d => d.medication_name).filter(Boolean);
        currentMedsBox.innerHTML = meds.length ? `<ul>${meds.map(m => `<li>${m}</li>`).join('')}</ul>` : 'None';
      });
      patientResults.appendChild(div);
    });
  });

  // Drug autocomplete
  const medInput = document.getElementById("medication-name");
  const drugSuggestions = document.getElementById("drug-suggestions");
  medInput.addEventListener("input", async (e) => {
    const q = e.target.value.trim();
    drugSuggestions.innerHTML = '';
    if (!q) return;
    const drugs = await searchDrugs(q);
    drugs.slice(0, 8).forEach(d => {
      const div = document.createElement("div");
      div.className = 'search-item';
      div.textContent = `${d.generic_name} ${d.brand ? '('+d.brand+')' : ''}`;
      div.addEventListener("click", () => {
        selectedDrug = d;
        medInput.value = d.generic_name + (d.brand ? ` (${d.brand})` : '');
        drugSuggestions.innerHTML = '';
      });
      drugSuggestions.appendChild(div);
    });
  });

  document.getElementById("cancel-presc").addEventListener("click", () => {
    // reset fields
    selectedPatient = null;
    selectedDrug = null;
    medInput.value = '';
    document.getElementById("dosage").value = '';
    document.getElementById("frequency").value = '';
    document.getElementById("duration").value = '';
    document.getElementById("refills").value = 0;
    document.getElementById("instructions").value = '';
    document.getElementById("selected-patient").textContent = 'No patient selected';
    document.getElementById("patient-allergies").textContent = '—';
    document.getElementById("current-medications").textContent = '—';
  });

  document.getElementById("create-presc").addEventListener("click", async () => {
    if (!selectedPatient) return alert("Please select a patient first.");
    const medicationName = document.getElementById("medication-name").value.trim();
    if (!medicationName) return alert("Please enter medication name.");

    // Create prescription (POST) -> prescriptionRoutes.php?action=create
    // assumed payload: { prescribing_doctor: (server uses session to determine doctor), record_id or patient_id, prescription_date }
    // We'll send patient_id and prescription_date; controller should map to models.
    try {
      const prescRes = await fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=create`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          patient_id: selectedPatient.user_id,
          record_id: selectedPatient.record_id ?? null,
          prescription_date: new Date().toISOString(),
          status: "active"
        })
      });

      if (!prescRes.success || !prescRes.prescription_id) {
        console.error("Create prescription response", prescRes);
        return alert("Failed to create prescription. Check console for details.");
      }

      const prescriptionId = prescRes.prescription_id;

      // Add prescription detail
      const detailPayload = {
        prescription_id: prescriptionId,
        drug_id: selectedDrug?.drug_id ?? null,
        medication_name: medicationName,
        dosage: document.getElementById("dosage").value,
        frequency: document.getElementById("frequency").value,
        duration: document.getElementById("duration").value,
        refills: parseInt(document.getElementById("refills").value || 0),
        special_instructions: document.getElementById("instructions").value
      };

      const detailRes = await fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=add-detail`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(detailPayload)
      });

      if (!detailRes.success) {
        console.error("Add detail response", detailRes);
        return alert("Prescription created but failed to add details.");
      }

      alert("Prescription created successfully!");
      // redirect to dashboard or clear form
      location.href = "DoctorDashboard.php";

    } catch (err) {
      console.error("Error creating prescription", err);
      alert("Server error when creating prescription.");
    }
  });
});
