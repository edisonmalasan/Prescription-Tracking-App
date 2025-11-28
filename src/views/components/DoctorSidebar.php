<?php
$activePage = $activePage ?? 'dashboard';
?>
<aside class="w-64 bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 text-white min-h-screen fixed left-0 top-0 shadow-2xl z-10">
  <div class="p-6">
    <div class="flex items-center gap-3 mb-8 pb-6 border-b border-blue-700">
      <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
      <div>
        <div class="text-xl font-bold">Doctor Portal</div>
        <div class="text-xs text-blue-200">Medical Dashboard</div>
      </div>
    </div>
    <nav class="space-y-1">
      <a href="DoctorDashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $activePage === 'dashboard' ? 'bg-white bg-opacity-20 text-white shadow-lg' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        <span class="font-medium">Dashboard</span>
      </a>
      <a href="PatientManagement.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $activePage === 'patients' ? 'bg-white bg-opacity-20 text-white shadow-lg' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <span class="font-medium">Patients</span>
      </a>
      <a href="PrescriptionManagement.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $activePage === 'prescriptions' ? 'bg-white bg-opacity-20 text-white shadow-lg' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
        </svg>
        <span class="font-medium">Prescriptions</span>
      </a>
      <a href="DoctorProfile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $activePage === 'profile' ? 'bg-white bg-opacity-20 text-white shadow-lg' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span class="font-medium">Profile</span>
      </a>
    </nav>
  </div>
  <div class="absolute bottom-0 w-full p-6 border-t border-blue-700">
    <a href="/" class="flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-center transition-all duration-200 shadow-lg hover:shadow-xl font-medium">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
      </svg>
      <span>Logout</span>
    </a>
  </div>
</aside>
