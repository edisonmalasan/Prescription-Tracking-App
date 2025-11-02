const API_BASE = "../../src/api";

async function fetchJSON(url) {
  const res = await fetch(url);
  return res.json();
}

async function fetchStatistics() {
  const endpoints = {
    users: `${API_BASE}/adminRoutes.php?action=all-users`,
    doctors: `${API_BASE}/doctorRoutes.php?action=all`,
    patients: `${API_BASE}/patientRoutes.php?action=all`,
    pharmacies: `${API_BASE}/adminRoutes.php?action=all-pharmacies`,
    prescriptions: `${API_BASE}/prescriptionRoutes.php?action=all`,
  };

  const [users, doctors, patients, pharmacies, prescriptions] =
    await Promise.all(Object.values(endpoints).map(fetchJSON));

  const setText = (id, val) =>
    (document.getElementById(id).textContent = val ?? 0);

  setText("total-users", users.users?.length || 0);
  setText("total-doctors", doctors.doctors?.length || 0);
  setText("total-patients", patients.patients?.length || 0);
  setText("total-pharmacies", pharmacies.pharmacies?.length || 0);

  if (prescriptions.success) {
    document.getElementById("system-stats").innerHTML = `
      <p><strong>Total Prescriptions:</strong> ${prescriptions.prescriptions.length}</p>`;
  }
}

document.addEventListener("DOMContentLoaded", fetchStatistics);
