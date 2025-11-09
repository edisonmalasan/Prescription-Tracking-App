const API_BASE = "../../src/api";

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  return res.json();
}

function formatAge(dob) {
  if (!dob) return '—';
  const birth = new Date(dob);
  const now = new Date();
  return now.getFullYear() - birth.getFullYear();
}

async function loadPatients(query = '') {
  try {
    const allRes = await fetchJSON(`${API_BASE}/patientRoutes.php?action=all`);
    const patients = allRes.patients ?? [];
    const tbody = document.querySelector("#patients-table tbody");
    tbody.innerHTML = "";

    const filtered = query
      ? patients.filter(p => `${p.first_name} ${p.last_name}`.toLowerCase().includes(query.toLowerCase()))
      : patients;

    if (filtered.length === 0) {
      tbody.innerHTML = "<tr><td colspan='5'>No patients found</td></tr>";
      return;
    }

    filtered.forEach(p => {
      const lastVisit = p.last_visit ? new Date(p.last_visit).toLocaleDateString() : '—';
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${p.first_name} ${p.last_name}</td>
        <td>${formatAge(p.birth_date)}</td>
        <td>${p.contactno ?? '—'}</td>
        <td>${lastVisit}</td>
        <td><button class="btn small view-btn" data-id="${p.user_id}">View</button></td>
      `;
      tbody.appendChild(tr);
    });

    // attach view buttons
    document.querySelectorAll(".view-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        openPatientModal(id);
      });
    });

  } catch (err) {
    console.error("Error loading patients", err);
  }
}

async function openPatientModal(userId) {
  try {
    // fetch profile and medical record
    const [profileRes, recordRes, prescriptionsRes] = await Promise.all([
      fetchJSON(`${API_BASE}/patientRoutes.php?action=profile&user_id=${userId}`),
      fetchJSON(`${API_BASE}/patientRoutes.php?action=medical-record&user_id=${userId}`),
      fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=by-patient&patient_id=${userId}`)
    ]);

    const content = document.getElementById("patient-modal-content");
    const profile = profileRes.profile ?? {};
    const record = recordRes.record ?? {};
    const prescriptions = prescriptionsRes.prescriptions ?? [];

    content.innerHTML = `
      <h3>Patient: ${profile.first_name ?? ''} ${profile.last_name ?? ''}</h3>
      <div class="patient-grid">
        <div class="card small">
          <h4>Patient Information</h4>
          <p><strong>Full Name:</strong> ${profile.first_name ?? ''} ${profile.last_name ?? ''}</p>
          <p><strong>Age:</strong> ${profile.birth_date ? (new Date().getFullYear() - new Date(profile.birth_date).getFullYear()) : '—'}</p>
          <p><strong>Contact:</strong> ${profile.contactno ?? '—'}</p>
        </div>

        <div class="card small">
          <h4>Patient Allergies</h4>
          <p>${record.allergies ?? 'None'}</p>
        </div>

        <div class="card">
          <h4>Active Prescriptions</h4>
          <table>
            <thead><tr><th>Medication</th><th>Dosage</th><th>Frequency</th></tr></thead>
            <tbody>
              ${prescriptions.map(rx => (
                (rx.details ?? []).map(d => `<tr><td>${d.medication_name ?? '—'}</td><td>${d.dosage ?? '—'}</td><td>${d.frequency ?? '—'}</td></tr>`).join('')
              )).join('') || '<tr><td colspan="3">No active prescriptions</td></tr>'}
            </tbody>
          </table>
        </div>

        <div class="card">
          <h4>Visit History</h4>
          ${(profile.visit_history ?? []).map(v => `<div class="visit">${new Date(v.date).toLocaleDateString()}</div>`).join('') || '<div>No visits found</div>'}
        </div>
      </div>
    `;

    showModal("patient-modal");
  } catch (err) {
    console.error("Error opening patient modal", err);
  }
}

/* Modal helpers */
function showModal(id) {
  const modal = document.getElementById(id);
  modal.classList.remove("hidden");
}
function hideModal(id) {
  const modal = document.getElementById(id);
  modal.classList.add("hidden");
}

document.addEventListener("DOMContentLoaded", () => {
  loadPatients();

  document.getElementById("search-btn").addEventListener("click", () => {
    loadPatients(document.getElementById("search-patient").value.trim());
  });

  document.querySelector(".close-modal").addEventListener("click", () => hideModal("patient-modal"));
  document.getElementById("add-patient-btn").addEventListener("click", () => {
    alert("Add patient form not implemented in this UI stub. Use your registration route to create a patient.");
  });
});
