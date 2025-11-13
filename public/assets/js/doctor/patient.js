console.log("Patient Managememnt JS loaded");

const API_BASE = "../../../src/api";

const apiCall = async (url, options = {}) => {
  const response = await fetch(url, {
    headers: { "Content-Type": "application/json", ...options.headers },
    ...options,
  });
  const text = await response.text();

  //REMOVE DEBUG LINES
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
    post: (endpoint, data) => apiCall(`${API_BASE}/${endpoint}`, { method: 'POST', body: JSON.stringify(data) }),
    put: (endpoint, data) => apiCall(`${API_BASE}/${endpoint}`, { method: 'PUT', body: JSON.stringify(data) })
};

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  if (!res.ok) {
    const text = await res.text();
    throw new Error(`HTTP ${res.status}: ${text.slice(0, 200)}`);
  }
  try {
    return await res.json();
  } catch {
    const text = await res.text();
    console.error("Invalid JSON from server:", text);
    throw new Error("Invalid JSON response");
  }
}

// Age helper
function formatAge(dob) {
  if (!dob) return "—";
  const birth = new Date(dob);
  return new Date().getFullYear() - birth.getFullYear();
}

document.addEventListener("DOMContentLoaded", async () => {
  //auth
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "DOCTOR") {
    window.location.href = "../../../public/login.html";
    return;
  }

  await loadPatients(user.user_id);

  document.getElementById("search-btn").addEventListener("click", () => {
    const q = document.getElementById("search-patient").value.trim();
    loadPatients(user.user_id, q);
  });

  document.querySelector(".close-modal").addEventListener("click", () =>
    hideModal("patient-modal")
  );

  document.getElementById("add-patient-btn").addEventListener("click", () => {
    console.log("BUTTON CLICKED");
    showModal("add-patient-modal");
  });

  document.getElementById("save-patient-btn").addEventListener("click", saveNewPatient);
});


async function loadPatients(doctorId, query = "") {
  try {
    const res = await fetchJSON(`${API_BASE}/patientRoutes.php?action=by-doctor&user_id=${doctorId}`);
    const patients = res.patients ?? [];

    const tbody = document.querySelector("#patients-table tbody");
    tbody.innerHTML = "";

    const filtered = query
      ? patients.filter(p =>
          `${p.first_name} ${p.last_name}`.toLowerCase().includes(query.toLowerCase())
        )
      : patients;

    if (!filtered.length) {
      tbody.innerHTML = `<tr><td colspan="4">No patients found</td></tr>`;
      return;
    }

    filtered.forEach(p => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${p.first_name} ${p.last_name}</td>
        <td>${formatAge(p.birth_date)}</td>
        <td>${p.contactno ?? "—"}</td>
        <td><button class="btn small view-btn" data-id="${p.user_id}">View</button></td>
      `;
      tbody.appendChild(tr);
    });

    document.querySelectorAll(".view-btn").forEach(btn => {
      btn.addEventListener("click", () => openPatientModal(btn.dataset.id));
    });

  } catch (err) {
    console.error("Error loading patients:", err);
  }
}

async function openPatientModal(userId) {
  try {
    const [profileRes, recordRes, prescriptionsRes] = await Promise.all([
      fetchJSON(`${API_BASE}/patientRoutes.php?action=profile&user_id=${userId}`),
      fetchJSON(`${API_BASE}/patientRoutes.php?action=medical-record&user_id=${userId}`),
      fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=by-patient&patient_id=${userId}`)
    ]);

    const profile = profileRes.patient ?? {};
    const record = recordRes.medical_record ?? {};
    const prescriptions = prescriptionsRes.prescriptions ?? [];

    const prescriptionsWithDetails = await Promise.all(
      prescriptions.map(async (rx) => {
        try {
          const detailRes = await fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=details&prescription_id=${rx.prescription_id}`);
          rx.details = detailRes.details ?? [];

          //now fetch drug name from drug
          rx.details = await Promise.all(
            rx.details.map(async (d) => {
              if (d.drug_id) {
                try {
                  const drugRes = await fetchJSON(`${API_BASE}/drugRoutes.php?action=get&drug_id=${d.drug_id}`);
                  d.generic_name = drugRes.drug?.generic_name ?? "Unknown";
                } catch {
                  d.generic_name = "Unknown";
                }
              } else {
                d.generic_name = "—";
              }
              return d;
            })
          );
        } catch {
          rx.details = [];
        }
        return rx;
      })
    );

    const activePrescriptions = prescriptionsWithDetails.filter(rx => rx.status === "pending");

    const activeRows =
      activePrescriptions.flatMap(rx => rx.details ?? []).map(d => `
        <tr>
          <td>${d.generic_name ?? "—"}</td>
          <td>${d.dosage ?? "—"}</td>
          <td>${d.frequency ?? "—"}</td>
        </tr>
      `).join("") ||
      `<tr><td colspan="3">No active prescriptions</td></tr>`;

    document.getElementById("patient-modal-content").innerHTML = `
      <h3>Patient: ${profile.first_name} ${profile.last_name}</h3>
      <div class="patient-grid">

        <div class="card small">
          <h4>Patient Information</h4>
          <p><strong>Age:</strong> ${formatAge(profile.birth_date)}</p>
          <p><strong>Contact:</strong> ${profile.contactno ?? "—"}</p>
        </div>

        <div class="card small">
          <h4>Allergies</h4>
          <p>${record.allergies ?? "None"}</p>
        </div>

        <div class="card">
          <h4>Pending Prescriptions</h4>
          <table>
            <thead><tr><th>Medication</th><th>Dosage</th><th>Frequency</th></tr></thead>
            <tbody>${activeRows}</tbody>
          </table>
        </div>

      </div>
    `;

    showModal("patient-modal");

  } catch (err) {
    console.error("Error opening patient modal:", err);
  }
}

async function saveNewPatient() {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));

  const payload = {
    first_name: document.getElementById("new-first-name").value.trim(),
    last_name: document.getElementById("new-last-name").value.trim(),
    email: `patient_${Date.now()}@temp.com`,
    password: 'temp123',
    birth_date: document.getElementById("new-birthdate").value,
    contactno: " " + document.getElementById("new-contact").value.trim(),
    address: document.getElementById("new-address").value.trim(),
  };

  if (!payload.first_name || !payload.last_name) {
    return alert("First and last name are required.");
  }

  try {
    const response = await api.post('patientRoutes.php?action=register', payload);
    if (response.success) {

    const medResponse = await api.post(
      `patientRoutes.php?action=medical-record&user_id=${response.user_id}`,
      {
        allergies: "N/A",
        medications: "N/A",
        height: null,
        weight: null,
      }
    );

    console.log("MEDICAL RECORD RESPONSE:", medResponse);

    //create blank prescription for linking patient -> doctor
    const prescripResponse = await api.post(
      `prescriptionRoutes.php?action=create`,
      {
        prescribing_doctor: user.user_id, // logged-in doctor
        record_id: medResponse.record_id, // link to the new record if available
        prescription_date: new Date().toISOString().split("T")[0],
        status: "pending", // or "draft"
        details: [], // no drugs yet
      }
    );

    
    console.log("Sending patient data:", payload);

    if (!response.success) {
      console.error("Register patient response:", response);
      return alert("Failed to add patient.");
    }

    hideModal("add-patient-modal");
    alert("Patient added successfully!");

    loadPatients(user.user_id);
  }
  } catch (err) {
    console.error("Error adding patient:", err);
    alert("Server error while adding patient.");
  }
}

function showModal(id) {
  const el = document.getElementById(id);
  console.log("showModal called for:", id, "element:", el);
  if (!el) {
    console.error("Modal not found:", id);
    return;
  }
  el.classList.remove("hidden");
}
function hideModal(id) {
  document.getElementById(id).classList.add("hidden");
}
