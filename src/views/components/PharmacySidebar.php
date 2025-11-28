<?php
$activePage = $activePage ?? 'dashboard';
?>
<aside class="w-64 bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 text-white min-h-screen fixed left-0 top-0 shadow-2xl z-10">
  <div class="p-6">
    <div class="flex items-center gap-3 mb-8 pb-6 border-b border-blue-700">
      <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
        </svg>
      </div>
      <div>
        <div class="text-xl font-bold">Pharmacy Portal</div>
        <div class="text-xs text-blue-200">Dispensing Hub</div>
      </div>
    </div>
    <nav class="space-y-1">
      <a href="PharmacyDashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $activePage === 'dashboard' ? 'bg-white bg-opacity-20 text-white shadow-lg' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' ?>">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        <span class="font-medium">Dashboard</span>
      </a>
      <a href="PharmacyProfile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?= $activePage === 'profile' ? 'bg-white bg-opacity-20 text-white shadow-lg' : 'text-blue-100 hover:bg-white hover:bg-opacity-10' ?>">
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
