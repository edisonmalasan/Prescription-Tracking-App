console.log("My Prescription JS loaded — Tailwind UI Version");

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
    console.error("Invalid JSON:", text);
    throw err;
  }
};

const api = {
  get: (endpoint) => apiCall(`${API_BASE}/${endpoint}`),
};

//doms lmao
const nameEl = document.getElementById("patient-name");
const idEl = document.getElementById("patient-id");
const container = document.getElementById("prescription-container");

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "PATIENT") {
    window.location.href = "../../../public/login.html";
    return;
  }

  if (!PRESCRIPTION_ID) {
    alert("No prescription selected.");
    window.location.href = "./patientDashboard.php";
    return;
  }

  await loadPatientProfile(user.user_id);
  await loadPrescriptionDetails(PRESCRIPTION_ID);
});

async function loadPatientProfile(patientId) {
  const res = await api.get(`patientRoutes.php?action=profile&user_id=${patientId}`);
  if (!res.success || !res.patient) throw new Error("Profile not found");

  const p = res.patient;
  nameEl.textContent = `Welcome, ${p.first_name} ${p.last_name}`;
  idEl.textContent = `Patient ID: ${patientId}`;
}

async function loadPrescriptionDetails(id) {
  const res = await api.get(`prescriptionRoutes.php?action=details&prescription_id=${id}`);

  if (!res.success || !Array.isArray(res.details) || res.details.length === 0) {
    container.innerHTML = `<p class="text-center text-gray-500">No prescription details found.</p>`;
    return;
  }

  const detailedItems = await Promise.all(
    res.details.map(async (d) => {
      if (d.drug_id) {
        try {
          const drugRes = await api.get(
            `drugRoutes.php?action=get&drug_id=${d.drug_id}`
          );
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

  renderPrescriptionCard(detailedItems);
}

//dynamic card ui
function renderPrescriptionCard(items) {
  container.innerHTML = `
    <div class="bg-white shadow-md rounded-xl p-6 border border-gray-200 space-y-6">
    
      <h2 class="text-xl font-bold text-gray-800 border-b pb-3">
        Prescription Details
      </h2>

      <div class="space-y-4">
        ${items
          .map(
            (d) => `
          <div class="border rounded-lg p-4 bg-gray-50">
            
            <h3 class="font-semibold text-lg text-blue-700 mb-2">
              ${d.generic_name}
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
              <div>
                <p class="text-gray-500">Dosage</p>
                <p class="font-medium">${d.dosage ?? "—"}</p>
              </div>

              <div>
                <p class="text-gray-500">Frequency</p>
                <p class="font-medium">${d.frequency ?? "—"}</p>
              </div>

              <div>
                <p class="text-gray-500">Duration</p>
                <p class="font-medium">${d.duration ?? "—"} days</p>
              </div>

              <div>
                <p class="text-gray-500">Refills</p>
                <p class="font-medium">${d.refills ?? 0}</p>
              </div>
            </div>

            <div class="mt-3">
              <p class="text-gray-500">Special Instructions</p>
              <p class="font-medium">${d.special_instructions || "None"}</p>
            </div>

          </div>
        `
          )
          .join("")}
      </div>

    </div>
  `;
}
