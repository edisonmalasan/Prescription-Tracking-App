<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Doctor Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
    #recent-prescriptions tbody tr:not(:first-child):hover {
      background-color: #eff6ff;
      transition: background-color 0.15s ease;
    }
    #recent-prescriptions tbody td {
      padding: 1rem 1.5rem;
      font-size: 0.875rem;
      color: #374151;
      font-weight: 500;
    }
    #recent-prescriptions tbody tr:not(:first-child) {
      border-bottom: 1px solid #e5e7eb;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
  <div class="flex">
    <?php
    $activePage = 'dashboard';
    include '../components/DoctorSidebar.php';
    ?>

    <div class="ml-64 flex-1 min-h-screen">
      <header class="bg-white shadow-lg border-b border-gray-200 p-6">
        <div class="flex justify-between items-center">
          <div>
            <div id="doctor-name" class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Welcome, Dr. —</div>
            <div id="doctor-prc" class="text-sm text-gray-500 mt-1 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
              PRC License: <span id="doctor-prc-value">—</span>
            </div>
          </div>
          <div>
            <button id="new-prescription-btn" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              New Prescription
            </button>
          </div>
        </div>
      </header>

      <section class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
            </div>
            <h4 class="text-blue-100 text-sm font-medium mb-2">Total Patients</h4>
            <div id="stat-total-patients" class="text-4xl font-bold">0</div>
          </div>
          
          <div class="bg-white border-2 border-blue-200 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-blue-100 rounded-xl p-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <h4 class="text-gray-600 text-sm font-medium mb-2">Active Prescriptions</h4>
            <div id="stat-active-prescriptions" class="text-4xl font-bold text-blue-600">0</div>
          </div>
          
          <div class="bg-gradient-to-br from-blue-400 to-blue-500 rounded-2xl shadow-xl p-6 text-white transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            </div>
            <h4 class="text-blue-100 text-sm font-medium mb-2">This Week</h4>
            <div id="stat-this-week" class="text-4xl font-bold">0</div>
          </div>
          
          <div class="bg-white border-2 border-blue-300 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-blue-200 rounded-xl p-3">
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            </div>
            <h4 class="text-gray-600 text-sm font-medium mb-2">This Month</h4>
            <div id="stat-this-month" class="text-4xl font-bold text-blue-600">0</div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
              </svg>
              Recent Prescriptions
            </h3>
          </div>
          <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
            <table id="recent-prescriptions" class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gradient-to-r from-blue-600 to-blue-700">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Patient</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Medication</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Date</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100">
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                  <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                      <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                      </svg>
                      <p class="text-sm">No prescriptions found</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </div>

  <script src="../../../public/assets/js/doctor/dashboard.js"></script>
</body>
</html>
