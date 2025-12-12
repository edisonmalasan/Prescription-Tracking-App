<?php
session_start();
$patient_id = $_SESSION['patient_id'] ?? null;
$prescription_id = $_GET['prescription_id'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>My Prescriptions</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
  body { font-family: 'Inter', sans-serif; }
  #itemsTable tbody tr:not(:first-child):hover {
    background-color: #eff6ff;
    transition: background-color 0.15s ease;
  }
  #itemsTable tbody td {
    padding: 1rem 1.5rem;
    font-size: 0.875rem;
    color: #374151;
  }
</style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
<div class="flex">
  <?php
  $activePage = 'prescriptions';
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
      <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
          </svg>
          Prescription Items
        </h3>
        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
          <div id="prescription-container" class="mt-6"></div>
        </div>
      </div>
    </section>
  </div>
</div>

<script>
    const PRESCRIPTION_ID = <?= json_encode($prescription_id) ?>;
</script>
<script src="../../../public/assets/js/patient/prescription.js"></script>
</body>
</html>
