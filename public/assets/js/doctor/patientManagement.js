console.log("Patient Managemet JS loaded");

const API_BASE = "/Prescription-Tracking-App/src/api";

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  return res.json();
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
    const res = await fetchJSON(`${API_BASE}/patientRoutes.php?action=all`);
    const allPatients = res.patients ?? [];
    const patients = allPatients.filter(p => p.assigned_doctor == doctorId);

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

    const profile = profileRes.profile ?? {};
    const record = recordRes.record ?? {};
    const prescriptions = prescriptionsRes.prescriptions ?? [];

    const activeRows =
      prescriptions
        .flatMap(rx => rx.details ?? [])
        .map(
          d =>
            `<tr>
              <td>${d.generic_name ?? "—"}</td>
              <td>${d.dosage ?? "—"}</td>
              <td>${d.frequency ?? "—"}</td>
            </tr>`
        )
        .join("") ||
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
          <h4>Active Prescriptions</h4>
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
    birth_date: document.getElementById("new-birthdate").value,
    contactno: document.getElementById("new-contact").value.trim(),
    address: document.getElementById("new-address").value.trim(),
    assigned_doctor: user.user_id
  };

  if (!payload.first_name || !payload.last_name) {
    return alert("First and last name are required.");
  }

  try {
    const res = await fetchJSON(`${API_BASE}/patientRoutes.php?action=register`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    if (!res.success) {
      console.error("Register patient response:", res);
      return alert("Failed to add patient.");
    }

    hideModal("add-patient-modal");
    alert("Patient added successfully!");

    loadPatients(user.user_id);

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
