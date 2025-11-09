const API_BASE = "../../src/api";

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  return res.json();
}

// fetch doctor profile and stats + recent prescriptions
async function loadDashboard() {
  try {
    const [docRes, patientsRes, prescriptionsRes] = await Promise.all([
      fetchJSON(`${API_BASE}/doctorRoutes.php?action=profile`),
      fetchJSON(`${API_BASE}/patientRoutes.php?action=all`),
      fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=by-doctor`),
    ]);

    // Doctor profile
    if (docRes.success && docRes.profile) {
      const p = docRes.profile;
      document.getElementById("doctor-name").textContent = `Welcome, Dr. ${p.first_name} ${p.last_name}`;
      document.getElementById("doctor-prc").textContent = `PRC License: ${p.prc_license ?? '—'}`;
    }

    // Patients
    const totalPatients = patientsRes.patients?.length ?? 0;
    document.getElementById("stat-total-patients").textContent = totalPatients;

    // Prescriptions (by doctor)
    const prescriptions = prescriptionsRes.prescriptions ?? [];
    const activePrescriptions = prescriptions.filter(rx => rx.status === 'active').length;
    document.getElementById("stat-active-prescriptions").textContent = activePrescriptions;

    // time-based stats (simple heuristics)
    const now = new Date();
    const thisWeekCount = prescriptions.filter(rx => {
      if (!rx.prescription_date) return false;
      const d = new Date(rx.prescription_date);
      const diffDays = (now - d) / (1000 * 60 * 60 * 24);
      return diffDays <= 7;
    }).length;
    const thisMonthCount = prescriptions.filter(rx => {
      if (!rx.prescription_date) return false;
      const d = new Date(rx.prescription_date);
      return (now.getFullYear() === d.getFullYear() && now.getMonth() === d.getMonth());
    }).length;

    document.getElementById("stat-this-week").textContent = thisWeekCount;
    document.getElementById("stat-this-month").textContent = thisMonthCount;

    // Render recent prescriptions (latest 10)
    const tbody = document.querySelector("#recent-prescriptions tbody");
    tbody.innerHTML = "";
    prescriptions.slice(0, 10).forEach(rx => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${rx.patient_name ?? '—'}</td>
        <td>${(rx.details && rx.details[0] && rx.details[0].medication_name) ?? (rx.medication_name ?? '—')}</td>
        <td>${rx.prescription_date ? new Date(rx.prescription_date).toLocaleDateString() : '—'}</td>
        <td>${rx.status ?? '—'}</td>
      `;
      tbody.appendChild(tr);
    });

  } catch (err) {
    console.error("Error loading dashboard:", err);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  loadDashboard();

  // quick nav to create prescription
  document.getElementById("new-prescription-btn").addEventListener("click", () => {
    location.href = "PrescriptionManagement.php";
  });
});
