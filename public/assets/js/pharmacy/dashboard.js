const API_BASE = "../../src/api";

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  return res.json();
}

let allPrescriptions = [];

async function loadDashboard() {
  try {
    const [pharmacyRes, prescriptionsRes] = await Promise.all([
      fetchJSON(`${API_BASE}/pharmacyRoutes.php?action=profile`),
      fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=all`),
    ]);

    // Pharmacy profile
    if (pharmacyRes.success && pharmacyRes.profile) {
      const p = pharmacyRes.profile;
      document.getElementById("pharmacy-name").textContent = `Welcome, ${p.pharmacy_name}`;
      document.getElementById("pharmacy-address").textContent = `Address: ${p.address ?? '—'}`;
    }

    // Prescriptions
    allPrescriptions = prescriptionsRes.prescriptions ?? [];
    const totalPrescriptions = allPrescriptions.length;
    const pendingPrescriptions = allPrescriptions.filter(rx => rx.status === 'Pending').length;
    const filledPrescriptions = allPrescriptions.filter(rx => rx.status === 'Filled').length;

    document.getElementById("stat-total-prescriptions").textContent = totalPrescriptions;
    document.getElementById("stat-pending-prescriptions").textContent = pendingPrescriptions;
    document.getElementById("stat-filled-prescriptions").textContent = filledPrescriptions;

    renderPrescriptions(allPrescriptions);

  } catch (err) {
    console.error("Error loading dashboard:", err);
  }
}

function renderPrescriptions(prescriptions) {
  const tbody = document.querySelector("#prescriptions-table tbody");
  tbody.innerHTML = "";
  prescriptions.forEach(rx => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${rx.patient_name ?? '—'}</td>
      <td>${rx.doctor_name ?? '—'}</td>
      <td>${(rx.details && rx.details[0] && rx.details[0].medication_name) ?? (rx.medication_name ?? '—')}</td>
      <td>${(rx.details && rx.details[0] && rx.details[0].dosage) ?? (rx.dosage ?? '—')}</td>
      <td>${(rx.details && rx.details[0] && rx.details[0].duration) ?? (rx.duration ?? '—')}</td>
      <td>${rx.prescription_date ? new Date(rx.prescription_date).toLocaleDateString() : '—'}</td>
      <td>${rx.notes ?? '—'}</td>
      <td>${rx.status ?? '—'}</td>
      <td>
        ${rx.status === 'Pending' ? `<button class="btn small" onclick="updateStatus(${rx.prescription_id}, 'Filled')">Mark as Filled</button>` : ''}
      </td>
    `;
    tbody.appendChild(tr);
  });
}

async function updateStatus(prescriptionId, status) {
  try {
    const res = await fetchJSON(`${API_BASE}/prescriptionRoutes.php?action=update-status`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ prescription_id: prescriptionId, status: status }),
    });

    if (res.success) {
      loadDashboard();
    } else {
      alert(`Error: ${res.error}`);
    }
  } catch (err) {
    console.error("Error updating status:", err);
  }
}

function filterPrescriptions() {
  const patientFilter = document.getElementById("patient-name-filter").value.toLowerCase();
  const drugFilter = document.getElementById("drug-name-filter").value.toLowerCase();

  const filtered = allPrescriptions.filter(rx => {
    const patientName = (rx.patient_name ?? '').toLowerCase();
    const drugName = ((rx.details && rx.details[0] && rx.details[0].medication_name) ?? (rx.medication_name ?? '')).toLowerCase();
    return patientName.includes(patientFilter) && drugName.includes(drugFilter);
  });

  renderPrescriptions(filtered);
}

document.addEventListener("DOMContentLoaded", () => {
  loadDashboard();

  document.getElementById("patient-name-filter").addEventListener("input", filterPrescriptions);
  document.getElementById("drug-name-filter").addEventListener("input", filterPrescriptions);
});
