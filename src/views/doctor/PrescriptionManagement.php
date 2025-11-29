<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Create Prescription</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <div class="flex">
    <?php
    $activePage = 'prescriptions';
    include '../components/DoctorSidebar.php';
    ?>
    

    <div class="ml-64 flex-1 min-h-screen">
      <header class="bg-white shadow-md p-6">
        <div class="text-2xl font-bold text-gray-800">Create Prescription</div>
      </header>

      <section class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-6">
            <!-- Patient Selection -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <h4 class="text-lg font-semibold text-gray-800 mb-4">Patient Selection</h4>
              <input id="presc-search-patient" placeholder="Search Patient" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none mb-4" />
              <div id="patient-search-results" class="border border-gray-200 rounded-lg max-h-48 overflow-y-auto"></div>
              <div id="selected-patient" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                No patient selected
              </div>
            </div>

            <!-- Prescription Details -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <h4 class="text-lg font-semibold text-gray-800 mb-4">Prescription Details</h4>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Medication Name</label>
                <input id="medication-name" placeholder="Start typing..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                <div id="drug-suggestions" class="border border-gray-200 rounded-lg mt-2 max-h-48 overflow-y-auto"></div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Dosage</label>
                  <input id="dosage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                  <input id="frequency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Duration (days)</label>
                  <input id="duration" type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                </div>
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Refills</label>
                <input id="refills" type="number" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                <textarea id="instructions" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"></textarea>
              </div>

              <div class="flex gap-4">
                <button id="cancel-presc" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                  Cancel
                </button>
                <button id="create-presc" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                  Create Prescription
                </button>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <aside class="bg-white rounded-lg shadow-md p-6 h-fit">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Patient Allergies</h4>
            <div id="patient-allergies" class="mb-6 text-gray-600">—</div>

            <h4 class="text-lg font-semibold text-gray-800 mb-4">Current Medications</h4>
            <div id="current-medications" class="text-gray-600">—</div>
          </aside>
        </div>
      </section>
    </div>
  </div>

  <script src="../../../public/assets/js/doctor/prescription.js"></script>
</body>
</html>
