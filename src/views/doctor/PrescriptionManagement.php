<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Create Prescription</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    /* Custom Scrollbar for search results */
    .scroller::-webkit-scrollbar { width: 6px; }
    .scroller::-webkit-scrollbar-track { background: transparent; }
    .scroller::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">
  <div class="flex min-h-screen">
    <?php
    $activePage = 'prescriptions';
    include '../components/DoctorSidebar.php';
    ?>
    
    <div class="ml-64 flex-1 flex flex-col h-screen overflow-hidden">
      
      <header class="bg-white border-b border-gray-200 px-8 py-5 flex items-center justify-between shrink-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Prescription Order</h1>
            <p class="text-sm text-gray-500 mt-1">Create digital prescriptions for pharmacy fulfillment</p>
        </div>
        <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium border border-blue-100">
            Dr. Session Active
        </div>
      </header>

      <section class="flex-1 overflow-y-auto p-8 scroller">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          <div class="lg:col-span-8 space-y-6">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
              <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                <div class="bg-blue-600 text-white rounded-full p-1 w-6 h-6 flex items-center justify-center text-xs font-bold">1</div>
                <h4 class="font-semibold text-gray-800">Identify Patient</h4>
              </div>
              
              <div class="p-6 relative"> <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Search Database</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input id="presc-search-patient" placeholder="Type patient name..." 
                           class="pl-10 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
                </div>
                
                <div id="patient-search-results" class="absolute left-6 right-6 z-20 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto scroller empty:hidden"></div>

                <div id="selected-patient" class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-lg text-blue-900 text-sm">
                  <span class="text-blue-400 italic">No patient selected yet.</span>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative"> <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                 <div class="bg-blue-600 text-white rounded-full p-1 w-6 h-6 flex items-center justify-center text-xs font-bold">2</div>
                <h4 class="font-semibold text-gray-800">Prescription Details</h4>
              </div>

              <div class="p-6 space-y-5">
                <div class="relative">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Medication</label>
                    <input id="medication-name" placeholder="Search drug name (e.g. Amoxicillin)..." 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-shadow" />
                    <div id="drug-suggestions" class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg mt-1 shadow-lg max-h-48 overflow-y-auto scroller empty:hidden"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                  <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Dosage</label>
                    <input id="dosage" placeholder="e.g. 500mg" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Frequency</label>
                    <input id="frequency" placeholder="e.g. 3x a day" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Duration</label>
                    <input id="duration" placeholder="e.g. 7 days" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                     <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Refills</label>
                        <input id="refills" type="number" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Special Instructions</label>
                        <input id="instructions" placeholder="e.g. Take with food..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" />
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button id="cancel-presc" class="mr-3 px-6 py-2.5 text-gray-500 hover:text-gray-700 font-medium transition-colors text-sm">
                        Reset Form
                    </button>
                    </div>
              </div>
            </div>
          </div>

          <aside class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-0">
                <div class="flex items-center gap-2 mb-6 text-gray-800">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h4 class="font-bold text-lg">Clinical Context</h4>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Known Allergies</p>
                        <div id="patient-allergies" class="p-3 bg-red-50 border border-red-100 rounded-lg text-red-800 text-sm font-medium">
                            —
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Current Medications</p>
                        <div id="current-medications" class="p-3 bg-gray-50 border border-gray-100 rounded-lg text-gray-600 text-sm">
                            —
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Please verify all contraindications before prescribing.</p>
                    </div>
                </div>
            </div>
          </aside>

        </div>
      </section>
    </div>
  </div>

  <script src="../../../public/assets/js/doctor/prescription.js"></script>
</body>
</html>