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

async function loadDashboard() {
  const loadingState = document.getElementById("loadingState");
  const errorState = document.getElementById("errorState");
  const statsContainer = document.getElementById("statsContainer");
  const roleBreakdownContainer = document.getElementById(
    "roleBreakdownContainer"
  );

  try {
    const response = await fetch(`${API_BASE}/dashboard/summary`);
    const data = await response.json();

    if (data.success) {
      displayStats(data.summary);
      displayRoleBreakdown(data.summary.roleBreakdown);
      loadingState.classList.add("hidden");
      statsContainer.classList.remove("hidden");
      roleBreakdownContainer.classList.remove("hidden");
    } else {
      throw new Error(data.message || "Failed to load dashboard");
    }
  } catch (error) {
    loadingState.classList.add("hidden");
    errorState.textContent = `Error: ${error.message}`;
    errorState.classList.remove("hidden");
  }
}

function displayStats(summary) {
  const statsContainer = document.getElementById("statsContainer");
  const counts = summary.counts || {};

  const stats = [
    { label: "Total Users", value: counts.users || 0, color: "blue" },
    { label: "Doctors", value: counts.doctors || 0, color: "green" },
    { label: "Patients", value: counts.patients || 0, color: "purple" },
    {
      label: "Pharmacies",
      value: counts.pharmacies || 0,
      color: "orange",
    },
  ];

  statsContainer.innerHTML = stats
    .map(
      (stat) => `
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">${stat.label}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">${stat.value}</p>
                        </div>
                        <div class="bg-${stat.color}-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-${stat.color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            `
    )
    .join("");
}

function displayRoleBreakdown(roleBreakdown) {
  const roleBreakdownDiv = document.getElementById("roleBreakdown");
  if (!roleBreakdown || Object.keys(roleBreakdown).length === 0) {
    roleBreakdownDiv.innerHTML =
      '<p class="text-gray-600">No role data available</p>';
    return;
  }

  const roles = [
    { key: "ADMIN", label: "Admins", color: "red" },
    { key: "DOCTOR", label: "Doctors", color: "green" },
    { key: "PATIENT", label: "Patients", color: "blue" },
    { key: "PHARMACY", label: "Pharmacies", color: "orange" },
  ];

  roleBreakdownDiv.innerHTML = roles
    .map((role) => {
      const count = roleBreakdown[role.key] || 0;
      return `
                    <div class="bg-${role.color}-50 border border-${role.color}-200 rounded-lg p-4">
                        <p class="text-${role.color}-600 text-sm font-medium">${role.label}</p>
                        <p class="text-2xl font-bold text-${role.color}-800 mt-2">${count}</p>
                    </div>
                `;
    })
    .join("");
}

function logout() {
  sessionStorage.removeItem("admin");
  sessionStorage.removeItem("loggedInUser");
  window.location.href = "/";
}

loadDashboard();
