<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Pharmacy Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
    #prescriptions-table tbody tr:not(:first-child):hover {
      background-color: #eff6ff;
      transition: background-color 0.15s ease;
    }
    #prescriptions-table tbody td {
      padding: 1rem 1.5rem;
      font-size: 0.875rem;
      color: #374151;
      font-weight: 500;
    }
    #prescriptions-table tbody tr:not(:first-child) {
      border-bottom: 1px solid #e5e7eb;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
  <div class="flex">
    <?php
    $activePage = 'dashboard';
    include '../components/PharmacySidebar.php';
    ?>

    <div class="ml-64 flex-1 min-h-screen">
      <header class="bg-white shadow-lg border-b border-gray-200 p-6">
        <div class="flex justify-between items-center">
          <div>
            <div id="pharmacy-name" class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Welcome, —</div>
            <div id="pharmacy-address" class="text-sm text-gray-500 mt-1 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              Address: <span id="pharmacy-address-value">—</span>
            </div>
          </div>
        </div>
      </header>

      <section class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-8 text-white transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
            </div>
            <h4 class="text-blue-100 text-sm font-medium mb-2">Total Prescriptions</h4>
            <div id="stat-total-prescriptions" class="text-4xl font-bold">0</div>
          </div>
          
          <div class="bg-white border-2 border-blue-200 rounded-2xl shadow-xl p-8 transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-blue-100 rounded-xl p-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <h4 class="text-gray-600 text-sm font-medium mb-2">Pending Prescriptions</h4>
            <div id="stat-pending-prescriptions" class="text-4xl font-bold text-blue-600">0</div>
          </div>
          
          <div class="bg-gradient-to-br from-blue-400 to-blue-500 rounded-2xl shadow-xl p-8 text-white transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
              <div class="bg-white bg-opacity-20 rounded-xl p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <h4 class="text-blue-100 text-sm font-medium mb-2">Filled Prescriptions</h4>
            <div id="stat-filled-prescriptions" class="text-4xl font-bold">0</div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
              </svg>
              All Prescriptions
            </h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <input type="text" id="patient-name-filter" placeholder="Filter by Patient Name..." class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <input type="text" id="drug-name-filter" placeholder="Filter by Drug Name..." class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>
          </div>
          
          <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
            <table id="prescriptions-table" class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gradient-to-r from-blue-600 to-blue-700">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Patient Name</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Doctor Name</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Drug Name</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Dosage</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Duration</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Date Prescribed</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Notes</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Action</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100">
                <tr class="hover:bg-blue-50 transition-colors duration-150">
                  <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                      <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
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

  <script src="../../../public/assets/js/pharmacy/dashboard.js"></script>
</body>
</html>
