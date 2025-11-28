<?php
session_start();
$patient_id = $_SESSION['patient_id'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Patient Dashboard</title>
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
  include '../components/PatientSidebar.php';
  ?>

  <div class="ml-64 flex-1 min-h-screen">
    <header class="bg-white shadow-lg border-b border-gray-200 p-6">
      <div class="flex justify-between items-center">
        <div>
          <div id="patient-name" class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">Welcome, —</div>
          <div id="patient-id" class="text-sm text-gray-500 mt-1 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
            </svg>
            Patient ID: <span id="patient-id-value">—</span>
          </div>
        </div>
      </div>
    </header>

    <section class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-8 text-white transform hover:scale-105 transition-all duration-200">
          <div class="flex items-center justify-between mb-4">
            <div class="bg-white bg-opacity-20 rounded-xl p-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
          </div>
          <h4 class="text-blue-100 text-sm font-medium mb-2">Total Prescriptions</h4>
          <div id="stat-total-prescriptions" class="text-5xl font-bold">0</div>
        </div>
        
        <div class="bg-white border-2 border-blue-200 rounded-2xl shadow-xl p-8 transform hover:scale-105 transition-all duration-200">
          <div class="flex items-center justify-between mb-4">
            <div class="bg-blue-100 rounded-xl p-3">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <h4 class="text-gray-600 text-sm font-medium mb-2">Active Prescriptions</h4>
          <div id="stat-active-prescriptions" class="text-5xl font-bold text-blue-600">0</div>
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
                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Prescription ID</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Doctor</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Date</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr class="hover:bg-blue-50 transition-colors duration-150">
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
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

<script src="../../../public/assets/js/patient/dashboard.js"></script>
</body>
</html>
