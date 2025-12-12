<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Pharmacy Workflow Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .scroller::-webkit-scrollbar { width: 6px; height: 6px; }
    .scroller::-webkit-scrollbar-track { background: #f1f1f1; }
    .scroller::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">
  <div class="flex h-screen overflow-hidden">
    <?php
    $activePage = 'dashboard';
    include '../components/PharmacySidebar.php';
    ?>

    <div class="ml-64 flex-1 flex flex-col h-screen overflow-hidden">
      
      <header class="bg-white border-b border-gray-200 px-8 py-5 flex justify-between items-center shrink-0 z-10">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Pharmacy Operations</h1>
          <div class="text-sm text-gray-500 mt-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span id="pharmacy-name">Loading Pharmacy...</span>
          </div>
        </div>
      </header>

      <main class="flex-1 p-8 overflow-y-auto scroller flex flex-col">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 shrink-0">
            
            <div class="bg-white border border-blue-100 rounded-2xl shadow-sm p-6 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute right-0 top-0 h-full w-1 bg-blue-500"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-50 rounded-lg p-3 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-blue-500 uppercase tracking-wider bg-blue-50 px-2 py-1 rounded">Intake</span>
                </div>
                <h4 class="text-gray-500 text-sm font-medium">Orders to Fill</h4>
                <div id="stat-pending-orders" class="text-3xl font-bold text-gray-800 mt-1">0</div>
            </div>
            
            <div id="card-ready-orders" class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg shadow-blue-200 p-6 text-white transform transition-all hover:-translate-y-1 hidden">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 rounded-lg p-3 backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <span class="text-xs font-bold text-white/80 uppercase tracking-wider bg-white/20 px-2 py-1 rounded">Action Required</span>
                </div>
                <h4 class="text-blue-100 text-sm font-medium">Ready for Pickup</h4>
                <div id="stat-ready-orders" class="text-3xl font-bold mt-1">0</div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 group hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gray-100 rounded-lg p-3 text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                </div>
                <h4 class="text-gray-500 text-sm font-medium">Total History</h4>
                <div id="stat-total-prescriptions" class="text-3xl font-bold text-gray-800 mt-1">0</div>
            </div>
        </div>
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 shrink-0">
            <div class="bg-gray-100 p-1.5 rounded-xl flex shadow-inner w-full md:w-auto">
                <button class="tab-btn flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-medium transition-all shadow-sm bg-white text-blue-700" data-tab="pending">
                    1. Intake Queue
                </button>
                <button class="tab-btn flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-700 hover:bg-gray-200/50" data-tab="filled">
                    2. Ready for Pickup
                </button>
                <button class="tab-btn flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-medium transition-all text-gray-500 hover:text-gray-700 hover:bg-gray-200/50" data-tab="history">
                    History
                </button>
            </div>

            <div class="relative w-full md:w-72">
                <input type="text" id="global-filter" placeholder="Search patient name..." 
                       class="pl-4 px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm w-full shadow-sm">
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex-1 overflow-hidden flex flex-col min-h-[400px]">
            <div class="overflow-x-auto scroller flex-1">
                <table id="prescriptions-table" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-10 text-xs uppercase font-semibold text-gray-500">
                        <tr>
                            <th class="px-6 py-4 text-left tracking-wider">Order ID / Date</th>
                            <th class="px-6 py-4 text-left tracking-wider">Patient Info</th>
                            <th class="px-6 py-4 text-left tracking-wider">Medications List</th>
                            <th class="px-6 py-4 text-right tracking-wider">Workflow Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100 text-sm" id="table-body">
                        </tbody>
                </table>
            </div>
        </div>
      </main>
    </div>
  </div>

  <div id="verify-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all scale-100">
          <div class="bg-gray-900 px-8 py-5 flex justify-between items-center">
              <div><h3 class="text-white font-bold text-lg">Order Verification</h3><p class="text-gray-400 text-xs mt-1">Order #<span id="m-order-id"></span></p></div>
              <button id="close-modal" class="text-white opacity-60 hover:opacity-100 text-2xl">&times;</button>
          </div>
          <div class="p-8 max-h-[60vh] overflow-y-auto scroller">
              <div class="flex justify-between items-start mb-6 pb-6 border-b border-gray-100">
                  <div><p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Patient</p><p id="m-patient" class="font-bold text-gray-800 text-lg">-</p></div>
                  <div class="text-right"><p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Prescribing Doctor</p><p id="m-doctor" class="text-gray-700">-</p></div>
              </div>
              <h4 class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-3">Medications to Fill</h4>
              <div id="m-med-list" class="space-y-3"></div>
          </div>
          <div class="bg-gray-50 px-8 py-5 flex justify-between items-center border-t border-gray-200">
              <span class="text-xs text-gray-500">Ensure label matches dosage instructions.</span>
              <div class="flex gap-3">
                <button id="btn-cancel-modal" class="px-5 py-2.5 text-gray-600 font-medium hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                <button id="btn-confirm-fill" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5">Confirm Order Filled</button>
              </div>
          </div>
      </div>
  </div>
  <script src="../../../public/assets/js/pharmacy/dashboard.js"></script>
</body>
</html>