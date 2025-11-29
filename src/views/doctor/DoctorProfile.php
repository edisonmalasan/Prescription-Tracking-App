<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Doctor Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
  <div class="flex">
    <?php
    $activePage = 'profile';
    include '../components/DoctorSidebar.php';
    ?>

    <div class="ml-64 flex-1 min-h-screen">
      <header class="bg-white shadow-lg border-b border-gray-200 p-6">
        <div class="flex justify-between items-center">
          <div class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent flex items-center gap-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            My Profile
          </div>
          <div>
            <button id="edit-profile-btn" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              Edit Profile
            </button>
          </div>
        </div>
      </header>

      <section class="p-6">
        <div class="bg-white rounded-2xl shadow-xl p-8 max-w-3xl border border-gray-100">
          <!-- VIEW MODE -->
          <div id="profile-view">
            <h2 id="profile-name" class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-6">Dr. —</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                  </svg>
                  Specialization
                </div>
                <div id="profile-special" class="text-lg font-semibold text-gray-900">—</div>
              </div>
              <div class="bg-white border-2 border-blue-200 rounded-xl p-4">
                <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                  PRC License
                </div>
                <div id="profile-prc" class="text-lg font-semibold text-gray-900">—</div>
              </div>
              <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                  </svg>
                  Contact
                </div>
                <div id="profile-contact" class="text-lg font-semibold text-gray-900">—</div>
              </div>
              <div class="bg-white border-2 border-blue-200 rounded-xl p-4">
                <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                  Email
                </div>
                <div id="profile-email" class="text-lg font-semibold text-gray-900">—</div>
              </div>
              <div class="md:col-span-2 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                <div class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  Clinic
                </div>
                <div id="profile-clinic" class="text-lg font-semibold text-gray-900">—</div>
              </div>
            </div>
          </div>

          <!-- EDIT FORM -->
          <form id="profile-form" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <input id="first_name" placeholder="First Name" disabled class="px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
              <input id="last_name" placeholder="Last Name" disabled class="px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
              <input id="contactno" placeholder="Contact Number" disabled class="px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
              <input id="email" placeholder="Email" disabled class="px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
              <input id="prc_license" placeholder="PRC License Number" disabled class="px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
              <input id="specialization" placeholder="Specialization" disabled class="px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
              <input id="clinic_name" placeholder="Hospital/Clinic" disabled class="px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
            </div>
            <div class="flex gap-4">
              <button type="button" id="cancel-profile" class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-semibold transition-all duration-200">
                Cancel
              </button>
              <button type="submit" id="save-profile" class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </section>
    </div>
  </div>

  <script src="../../../public/assets/js/doctor/profile.js"></script>
</body>
</html>
