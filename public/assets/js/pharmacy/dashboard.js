console.log("Pharmacy Dashboard Workflow JS loaded");

const API_BASE = "../../../src/api";

const apiCall = async (url, options = {}) => {
  try {
    const response = await fetch(url, {
      headers: { "Content-Type": "application/json", ...options.headers },
      ...options,
    });
    const text = await response.text();
    const jsonStart = text.indexOf("{");
    const cleanText = jsonStart >= 0 ? text.slice(jsonStart) : text;
    return JSON.parse(cleanText);
  } catch (err) {
    console.error("API Error", err);
    return { success: false, error: "Network error" };
  }
};

const api = {
  get: (endpoint) => apiCall(`${API_BASE}/${endpoint}`),
  put: (endpoint, data) =>
    apiCall(`${API_BASE}/${endpoint}`, { method: "PUT", body: JSON.stringify(data) }),
};

// --- State ---
let rawPrescriptions = [];
let groupedOrders = [];
let currentTab = "pending"; 
let selectedOrderId = null;

// --- DOM Refs ---
const tableBody = document.getElementById("table-body");
const globalFilter = document.getElementById("global-filter");
const tabs = document.querySelectorAll(".tab-btn");
const modal = document.getElementById("verify-modal");
const readyCard = document.getElementById("card-ready-orders"); // NEW

document.addEventListener("DOMContentLoaded", async () => {
  const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
  if (!user || user.role !== "PHARMACY") {
    window.location.href = "../../../public/login.html";
    return;
  }

  loadPharmacyProfile(user.user_id);
  await loadData();

  setInterval(async () => {
    // Only refresh data if the user isn't currently in the modal
    if (modal && modal.classList.contains("hidden")) {
       await loadData();
    }
  }, 4000); // Check every 4 seconds

  tabs.forEach(tab => {
    tab.addEventListener("click", (e) => {
      const btn = e.currentTarget;
      tabs.forEach(t => {
        t.classList.remove("bg-white", "text-blue-700", "shadow-sm");
        t.classList.add("text-gray-500", "hover:text-gray-700");
      });
      btn.classList.remove("text-gray-500", "hover:text-gray-700");
      btn.classList.add("bg-white", "text-blue-700", "shadow-sm");

      currentTab = btn.dataset.tab;
      renderView();
    });
  });

  globalFilter.addEventListener("input", renderView);

  document.getElementById("btn-confirm-fill").addEventListener("click", confirmFillOrder);
  document.getElementById("close-modal").addEventListener("click", hideModal);
  document.getElementById("btn-cancel-modal").addEventListener("click", hideModal);
});

async function loadData() {
  const res = await api.get(`prescriptionRoutes.php?action=all`);
  if (res.success && Array.isArray(res.prescriptions)) {
    rawPrescriptions = res.prescriptions;
    processData();
  }
}

async function loadPharmacyProfile(id) {
    const res = await api.get(`pharmacyRoutes.php?action=profile&user_id=${id}`);
    const p = res.profile || {};
    if(p.pharmacy_name) document.getElementById("pharmacy-name").textContent = p.pharmacy_name;
}

function processData() {
    const groups = {};
    rawPrescriptions.forEach(row => {
        const id = row.prescription_id;
        if (!groups[id]) {
            groups[id] = {
                id: id,
                patient: row.patient_name || "Unknown",
                doctor: row.doctor_name || "Unknown",
                date: row.prescription_date,
                status: row.status,
                items: []
            };
        }
        groups[id].items.push({
            drug_id: row.drug_id,
            drug: row.medication_name,
            dosage: row.dosage,
            freq: row.frequency,
            instructions: row.notes,
            quantity: row.quantity,
        });
    });

    groupedOrders = Object.values(groups);
    groupedOrders.sort((a, b) => new Date(b.date) - new Date(a.date));

    updateStats();
    renderView();
}

function updateStats() {
    const pending = groupedOrders.filter(o => isPending(o.status)).length;
    const ready = groupedOrders.filter(o => isFilled(o.status)).length;
    
    document.getElementById("stat-pending-orders").textContent = pending;
    document.getElementById("stat-ready-orders").textContent = ready;
    document.getElementById("stat-total-prescriptions").textContent = groupedOrders.length;

    if (readyCard) {
        if (ready > 0) {
            readyCard.classList.remove("hidden");
        } else {
            readyCard.classList.add("hidden");
        }
    }
}

// Helpers
const isPending = (s) => ["pending", "active", "partial-pending"].includes((s || "").toLowerCase());
const isFilled = (s) => ["filled", "partial"].includes((s || "").toLowerCase());
const isHistory = (s) => !isPending(s) && !isFilled(s);

function renderView() {
    const query = globalFilter.value.toLowerCase();
    const dataset = groupedOrders.filter(order => {
        let matchTab = false;
        if (currentTab === "pending") matchTab = isPending(order.status);
        else if (currentTab === "filled") matchTab = isFilled(order.status);
        else matchTab = isHistory(order.status);

        const matchSearch = order.patient.toLowerCase().includes(query) || 
                            order.items.some(i => i.drug.toLowerCase().includes(query));
        return matchTab && matchSearch;
    });

    tableBody.innerHTML = "";
    
    if (dataset.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="4" class="px-6 py-12 text-center text-gray-400">No orders found in this stage.</td></tr>`;
        return;
    }

    dataset.forEach(order => {
        const row = document.createElement("tr");
        row.className = "hover:bg-blue-50/50 transition-colors group";

        const medsHtml = order.items.map(item => `
            <div class="mb-2 last:mb-0 border-l-2 border-blue-200 pl-3">
                <div class="font-bold text-gray-800">${item.drug}</div>
                <div class="text-xs text-gray-500">${item.dosage} &bull; ${item.freq}</div>
            </div>
        `).join("");

        let btnHtml = "";
        if (currentTab === "pending") {
            const btnText = (order.status === 'partial-pending') ? "Fill Remaining" : "Review & Fill";
            
            btnHtml = `<button onclick="openVerifyModal('${order.id}')" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-md transition-all">
                ${btnText}
            </button>`;
        } else if (currentTab === "filled") {
            if (order.status === 'partial') {
                btnHtml = `<button onclick="markDispensed('${order.id}')" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold shadow-md transition-all">Handout Partial</button>`;
            } else {
                btnHtml = `<button onclick="markDispensed('${order.id}')" class="w-full py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-bold shadow-md transition-all">Dispense / Complete</button>`;
            }        
        } else {
            btnHtml = `<span class="px-3 py-1 bg-gray-100 text-gray-500 rounded text-xs font-semibold">${order.status}</span>`;
        }

        row.innerHTML = `
            <td class="px-6 py-4 align-top">
                <div class="text-gray-900 font-bold">#${order.id}</div>
                <div class="text-xs text-gray-500 mt-1">${formatDate(order.date)}</div>
            </td>
            <td class="px-6 py-4 align-top">
                <div class="font-medium text-gray-900">${order.patient}</div>
                <div class="text-xs text-gray-400 mt-1">Doc: ${order.doctor}</div>
            </td>
            <td class="px-6 py-4 align-top">${medsHtml}</td>
            <td class="px-6 py-4 align-top w-48">${btnHtml}</td>
        `;
        tableBody.appendChild(row);
    });
}

window.openVerifyModal = (id) => {
    const order = groupedOrders.find(o => o.id == id);
    if (!order) return;
    
    selectedOrderId = id;
    document.getElementById("m-order-id").textContent = order.id;
    document.getElementById("m-patient").textContent = order.patient;
    document.getElementById("m-doctor").textContent = order.doctor;

    const listEl = document.getElementById("m-med-list");
    
    listEl.innerHTML = order.items.map(item => {
        const qty = Number(item.quantity);
        const isFinished = !Number.isFinite(qty) || qty <= 0;

        
        return `
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex flex-col gap-3">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-bold text-blue-900 text-lg">${item.drug}</p>
                    <p class="text-sm text-blue-700">${item.dosage} | ${item.freq}</p>
                    <p class="text-xs text-gray-500 italic mt-1">${item.instructions || "Use as directed"}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs font-bold uppercase tracking-wide text-gray-500">Remaining</span>
                    <div class="text-2xl font-bold ${isFinished ? 'text-gray-400' : 'text-blue-600'}">
                        ${item.quantity}
                    </div>
                </div>
            </div>
            
            ${!isFinished ? `
            <div class="flex items-center gap-4 border-t border-blue-200 pt-3 mt-1">
                <label class="text-sm font-medium text-gray-700">Dispense Qty:</label>
                <input type="number" 
                       id="input-dispense-${item.drug_id}" 
                       data-drug-id="${item.drug_id}"
                       data-max="${item.quantity}"
                       class="dispense-input w-24 px-3 py-1.5 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none"
                       value="${item.quantity}" 
                       min="0" 
                       max="${item.quantity}">
            </div>
            ` : `<div class="text-sm text-green-600 font-bold border-t border-blue-200 pt-2">✓ Fully Dispensed</div>`}
        </div>
    `}).join("");

    modal.classList.remove("hidden");
    modal.classList.add("flex");
};

function hideModal() {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    selectedOrderId = null;
}

async function confirmFillOrder() {
    if(!selectedOrderId) return;
    
    const inputs = document.querySelectorAll('.dispense-input');
    const dispenseItems = [];
    let hasError = false;

    inputs.forEach(input => {
        const val = parseInt(input.value);
        const max = parseInt(input.dataset.max);
        const drugId = input.dataset.drugId;

        if (val < 0 || val > max) {
            alert(`Invalid quantity for one of the items. Max available: ${max}`);
            hasError = true;
            return;
        }

        if (val > 0) {
            dispenseItems.push({
                drug_id: drugId,
                amount: val
            });
        }
    });

    if (hasError) return;
    if (dispenseItems.length === 0) {
        alert("Please set a quantity to dispense for at least one item.");
        return;
    }

    try {
        const res = await api.put(`prescriptionRoutes.php?action=dispense`, { 
            prescription_id: selectedOrderId,
            items: dispenseItems
        });

        if(res.success) {
            hideModal();
            loadData(); // Force immediate reload to see updated quantities or status change
        } else {
            alert("Error: " + (res.error || "Failed to dispense"));
        }
    } catch(e) { console.error(e); }
}

window.markDispensed = async (id) => {
    const order = groupedOrders.find(o => o.id == id);
    if (!order) return;

    //partial presc
    if (order.status === 'partial') {
        const msg = `Hand over available items to patient?\n\nThis order has remaining items. It will be moved back to the INTAKE QUEUE so you can fill the rest later.`;
        
        if(!confirm(msg)) return;

        try {
            const res = await api.put(`prescriptionRoutes.php?action=update&prescription_id=${id}`, { 
                status: "partial-pending" 
            });

            if(res.success) {
                loadData(); 
            } else {
                alert("Error moving to queue: " + (res.error || "Unknown error"));
            }
        } catch(e) { console.error(e); }
    } 
    else {
        if(!confirm("Complete order and hand to patient? This will close the ticket.")) return;
        try {
            const res = await api.put(`prescriptionRoutes.php?action=update&prescription_id=${id}`, { status: "active" });
            if(res.success) loadData(); 
        } catch(e) { console.error(e); }
    }
}

function formatDate(d) {
    if (!d) return "-";
    return new Date(d).toLocaleDateString("en-US", { month: 'short', day: 'numeric' });
}