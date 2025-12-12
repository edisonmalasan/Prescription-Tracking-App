console.log("Patient Dashboard JS loaded");

const API_BASE = "../../../src/api";

//api helper
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
  put: (endpoint, body) =>
    apiCall(`${API_BASE}/${endpoint}`, {
      method: "PUT",
      body: JSON.stringify(body),
    }),
};

// state vars
let allPrescriptions = []; //stores all fetched data
let currentTab = "active"; //'active' or 'history'

//dom refs
const nameEl = document.getElementById("patient-name");
const idEl = document.getElementById("patient-id-value");
const totalEl = document.getElementById("stat-total-prescriptions");
const activeEl = document.getElementById("stat-active-prescriptions");
const tableBody = document.querySelector("#recent-prescriptions tbody");
const tabActive = document.getElementById("tab-active");
const tabHistory = document.getElementById("tab-history");

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "PATIENT") {
    window.location.href = "../../../public/login.html";
    return;
  }

  setupTabs();

  try {
    await loadPatientProfile(user.user_id);
    await loadPrescriptions(user.user_id);
  } catch (err) {
    console.error("Error loading patient dashboard:", err);
    alert("Failed to load dashboard data.");
  }
});

//tab logic
function setupTabs() {
  const updateTabStyles = (activeTabId, inactiveTabId) => {
    document.getElementById(activeTabId).className = "px-4 py-2 text-sm font-medium rounded-md bg-white text-blue-600 shadow-sm transition-all duration-200";
    document.getElementById(inactiveTabId).className = "px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 transition-all duration-200";
  };

  tabActive.addEventListener("click", () => {
    currentTab = "active";
    updateTabStyles("tab-active", "tab-history");
    renderPrescriptions();
  });

  tabHistory.addEventListener("click", () => {
    currentTab = "history";
    updateTabStyles("tab-history", "tab-active");
    renderPrescriptions();
  });
}

//data loading
async function loadPatientProfile(patientId) {
  const res = await api.get(`patientRoutes.php?action=profile&user_id=${patientId}`);
  if (!res.success || !res.patient) throw new Error(res.error || "Profile not found");

  const p = res.patient;
  nameEl.textContent = `Welcome, ${p.first_name ?? "—"} ${p.last_name ?? ""}`;
  idEl.textContent = `${patientId}`;
}

async function loadPrescriptions(patientId) {
  const res = await api.get(`prescriptionRoutes.php?action=by-patient-doctor&patient_id=${patientId}`);

  if (!res.success || !Array.isArray(res.prescriptions)) {
    allPrescriptions = [];
    renderPrescriptions();
    return;
  }

  const prescriptions = res.prescriptions;

  //update Stats
  totalEl.textContent = prescriptions.length;
  activeEl.textContent = prescriptions.filter(p => p.status === "active").length;

  //fetch deets
  allPrescriptions = await Promise.all(
    prescriptions.map(async (rx) => {
      const detailsRes = await api.get(
        `prescriptionRoutes.php?action=details&prescription_id=${rx.prescription_id}`
      );
      const details = detailsRes.details || [];

      //fetch generic names
      for (let d of details) {
        if (!d.drug_id) continue;
        try {
          const drugRes = await api.get(`drugRoutes.php?action=get&drug_id=${d.drug_id}`);
          d.generic_name = drugRes.drug?.generic_name || "Unknown";
        } catch {
          d.generic_name = "Unknown";
        }
      }
      return { ...rx, details };
    })
  );

  renderPrescriptions();
}

function renderPrescriptions() {
  const container = document.querySelector("#recent-prescriptions tbody");
  container.innerHTML = "";

  //filter logic
  //active Tab: status is 'active' or 'pending'
  //history Tab: status is 'completed', 'filled', or 'cancelled'
  const filteredList = allPrescriptions.filter(rx => {
    if (currentTab === "active") return rx.status === "active" || rx.status === "pending";
    return rx.status === "completed" || rx.status === "filled" || rx.status === "cancelled";
  });

  if (!filteredList.length) {
    container.innerHTML = `
      <tr>
        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
          No ${currentTab} prescriptions found.
        </td>
      </tr>`;
    return;
  }

  filteredList.forEach((rx) => {
    // Styling status badges
    let statusColor = "text-gray-600 bg-gray-200";
    if (rx.status === "active") statusColor = "text-green-600 bg-green-100";
    if (rx.status === "completed" || rx.status === "filled") statusColor = "text-blue-600 bg-blue-100";

    const detailsHTML = rx.details
      .map(d => `
        <div class="border-b py-3 last:border-0">
          <div class="font-semibold text-gray-800">${d.generic_name}</div>
          <div class="text-sm text-gray-600">
            <span class="font-medium">Dosage:</span> ${d.dosage ?? "—"} &middot; 
            <span class="font-medium">Freq:</span> ${d.frequency ?? "—"} &middot; 
            <span class="font-medium">Dur:</span> ${d.duration ?? "—"} days
          </div>
          <div class="text-sm text-gray-500 italic mt-1">${d.special_instructions ?? ""}</div>
        </div>`)
      .join("");

    const item = document.createElement("tr");
    
    const markCompletedBtn = (currentTab === "active" && rx.status !== "pending" && rx.status !== "filled")
      ? `<button class="mt-4 mr-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition mark-complete-btn shadow-sm" data-id="${rx.prescription_id}">
           Mark as Completed
         </button>` 
      : "";

    item.innerHTML = `
      <td colspan="5" class="p-0">
        <div class="border rounded-xl overflow-hidden mb-4 hover:shadow-md transition-shadow duration-200">
          <button class="w-full flex justify-between items-center px-6 py-4 bg-white hover:bg-gray-50 transition toggle-btn">
            <div class="text-left">
              <div class="font-bold text-lg text-gray-800">Prescription #${rx.prescription_id}</div>
              <div class="text-sm text-gray-600 mt-1">
                Dr. ${rx.doctor_first_name ?? "—"} ${rx.doctor_last_name ?? "—"}
                <span class="mx-2 text-gray-300">|</span>
                ${formatDate(rx.prescription_date)}
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span class="${statusColor} px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide">
                ${rx.status}
              </span>
              <svg class="w-5 h-5 text-gray-400 accordion-icon transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </button>

          <div class="hidden accordion-body bg-gray-50 px-6 py-4 border-t border-gray-100">
            <div class="mb-4">
              <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Medications</h5>
              <div class="bg-white rounded-lg border border-gray-200 px-4">${detailsHTML}</div>
            </div>
            
            <div class="flex flex-wrap gap-2">
              ${markCompletedBtn}
              <button class="mt-4 px-4 py-2 border border-blue-600 text-blue-600 bg-white rounded-lg hover:bg-blue-50 transition view-full-btn shadow-sm" data-id="${rx.prescription_id}">
                View Details
              </button>
            </div>
          </div>
        </div>
      </td>
    `;

    container.appendChild(item);

    //event Listeners
    const toggleBtn = item.querySelector(".toggle-btn");
    const body = item.querySelector(".accordion-body");
    const icon = item.querySelector(".accordion-icon");

    toggleBtn.addEventListener("click", () => {
      body.classList.toggle("hidden");
      icon.classList.toggle("rotate-180");
    });

    item.querySelector(".view-full-btn").addEventListener("click", (e) => {
      const id = e.target.dataset.id;
      sessionStorage.setItem("selectedPrescriptionId", id);
      window.location.href = `./myprescription.php?prescription_id=${id}`;
    });

    //listener for Mark Complete only if button exists
    const completeBtn = item.querySelector(".mark-complete-btn");
    if (completeBtn) {
      completeBtn.addEventListener("click", (e) => markAsCompleted(e.target.dataset.id));
    }
  });
}

//action Logic ---
async function markAsCompleted(id) {
  if (!confirm("Are you sure you want to mark this prescription as completed? It will move to your history.")) return;

  try {
    const res = await api.put(`prescriptionRoutes.php?action=update&prescription_id=${id}`, {
      status: "completed" 
    });

    if (res.success) {
      //ppdate local state locally to avoid a full reload
      const rx = allPrescriptions.find(p => p.prescription_id == id);
      if (rx) rx.status = "completed";
      
      const activeCount = allPrescriptions.filter(p => p.status === "active").length;
      activeEl.textContent = activeCount;

      //will remove the item from Active view
      renderPrescriptions();
    } else {
      alert("Failed to update status: " + (res.error || "Unknown error"));
    }
  } catch (err) {
    console.error("Error updating status:", err);
    alert("An error occurred while updating the prescription.");
  }
}

function formatDate(dateStr) {
  if (!dateStr) return "—";
  const d = new Date(dateStr);
  return isNaN(d) ? "—" : d.toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" });
}