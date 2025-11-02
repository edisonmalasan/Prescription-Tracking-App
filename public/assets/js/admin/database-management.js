const API_BASE = "../../src/api";

async function fetchStatistics() {
  const [prescriptionsResponse, drugsResponse] = await Promise.all([
    fetch(`${API_BASE}/prescriptionRoutes.php?action=all`),
    fetch(`${API_BASE}/drugRoutes.php?action=all`),
  ]);

  const prescriptions = await prescriptionsResponse.json();
  const drugs = await drugsResponse.json();

  document.getElementById("total-prescriptions").textContent =
    prescriptions.success ? prescriptions.prescriptions.length : 0;
  document.getElementById("total-drugs").textContent = drugs.success
    ? drugs.drugs.length
    : 0;

  const total =
    (prescriptions.success ? prescriptions.prescriptions.length : 0) +
    (drugs.success ? drugs.drugs.length : 0);
  document.getElementById("total-records").textContent = total;

  try {
    const patientsResponse = await fetch(
      `${API_BASE}/patientRoutes.php?action=all`
    );
    const patients = await patientsResponse.json();

    document.getElementById("total-medical-records").textContent =
      patients.success ? patients.patients.length : 0;
  } catch (error) {
    document.getElementById("total-medical-records").textContent = "N/A";
  }
}

document.addEventListener("DOMContentLoaded", () => {
  fetchStatistics();

  document
    .getElementById("refresh-stats-btn")
    .addEventListener("click", fetchStatistics);
});
