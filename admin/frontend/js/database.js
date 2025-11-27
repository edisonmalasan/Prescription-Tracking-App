const API_BASE = "http://localhost:4000/api/admin";

// auth checker that supports both admin frontend login and PHP login
let admin = JSON.parse(sessionStorage.getItem("admin") || "null");
if (!admin) {
  const loggedInUser = JSON.parse(
    sessionStorage.getItem("loggedInUser") || "null"
  );
  if (loggedInUser && loggedInUser.role === "ADMIN") {
    admin = loggedInUser;
    sessionStorage.setItem("admin", JSON.stringify(admin));
  } else {
    window.location.href = "/";
  }
}

let currentTable = null;

async function loadTables() {
  const loadingState = document.getElementById("loadingState");
  const tablesContainer = document.getElementById("tablesContainer");

  try {
    const response = await fetch(`${API_BASE}/database/metadata`);
    const data = await response.json();

    if (data.success) {
      displayTables(data.tables || []);
      loadingState.classList.add("hidden");
      tablesContainer.classList.remove("hidden");
    } else {
      throw new Error(data.message || "Failed to load tables");
    }
  } catch (error) {
    loadingState.classList.add("hidden");
    showMessage(`Error: ${error.message}`, "error");
  }
}

function displayTables(tables) {
  const container = document.getElementById("tablesContainer");
  container.innerHTML = tables
    .map(
      (table) => `
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition cursor-pointer" onclick="viewTableRecords('${
          table.table_name
        }')">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">${
                  table.table_name
                }</h3>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
            <p class="text-sm text-gray-600 mb-2">${
              table.column_count || 0
            } columns</p>
            <p class="text-xs text-gray-500">Click to view records</p>
        </div>
    `
    )
    .join("");
}

async function viewTableRecords(tableName) {
  currentTable = tableName;
  const recordsContainer = document.getElementById("recordsContainer");
  const tablesContainer = document.getElementById("tablesContainer");

  tablesContainer.classList.add("hidden");
  recordsContainer.classList.remove("hidden");
  document.getElementById(
    "tableNameHeader"
  ).textContent = `${tableName} Records`;
  document.getElementById("recordsTableBody").innerHTML =
    '<tr><td colspan="100" class="px-6 py-4 text-center">Loading...</td></tr>';

  try {
    const response = await fetch(
      `${API_BASE}/database/records/${tableName}?limit=100`
    );
    const data = await response.json();

    if (data.success) {
      displayRecords(data.records || [], tableName);
      document.getElementById("recordCount").textContent = `${
        data.records?.length || 0
      } records displayed`;
    } else {
      throw new Error(data.message || "Failed to load records");
    }
  } catch (error) {
    document.getElementById(
      "recordsTableBody"
    ).innerHTML = `<tr><td colspan="100" class="px-6 py-4 text-center text-red-600">Error: ${error.message}</td></tr>`;
  }
}

function displayRecords(records, tableName) {
  if (records.length === 0) {
    document.getElementById("recordsTableBody").innerHTML =
      '<tr><td colspan="100" class="px-6 py-4 text-center text-gray-500">No records found</td></tr>';
    return;
  }
  const columns = Object.keys(records[0]);

  const thead = document.getElementById("recordsTableHead");
  thead.innerHTML = `<tr>${columns
    .map(
      (col) =>
        `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${col}</th>`
    )
    .join("")}</tr>`;

  const tbody = document.getElementById("recordsTableBody");
  tbody.innerHTML = records
    .map(
      (record) => `
        <tr class="hover:bg-gray-50">
            ${columns
              .map((col) => {
                const value = record[col];
                return `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${
                  value !== null && value !== undefined ? String(value) : "NULL"
                }</td>`;
              })
              .join("")}
        </tr>
    `
    )
    .join("");
}

function closeRecordsView() {
  document.getElementById("recordsContainer").classList.add("hidden");
  document.getElementById("tablesContainer").classList.remove("hidden");
  currentTable = null;
}

function showMessage(message, type) {
  const messageDiv = document.getElementById("messageDiv");
  messageDiv.textContent = message;
  messageDiv.className = `mb-6 px-4 py-3 rounded-lg ${
    type === "success"
      ? "bg-green-50 border border-green-200 text-green-700"
      : "bg-red-50 border border-red-200 text-red-700"
  }`;
  messageDiv.classList.remove("hidden");
  setTimeout(() => messageDiv.classList.add("hidden"), 5000);
}

function logout() {
  sessionStorage.removeItem("admin");
  sessionStorage.removeItem("loggedInUser");
  window.location.href = "/";
}

loadTables();
