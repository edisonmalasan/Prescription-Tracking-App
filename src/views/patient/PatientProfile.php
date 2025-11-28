<?php
session_start();
$patient_id = $_SESSION['patient_id'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Patient Profile</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="flex">
  <?php
  $activePage = 'profile';
  include '../components/PatientSidebar.php';
  ?>

  <div class="ml-64 flex-1 min-h-screen">
    <header class="bg-white shadow-md p-6">
      <div class="flex justify-between items-center">
        <div>
          <div id="patient-name" class="text-2xl font-bold text-gray-800">Welcome, —</div>
          <div id="patient-id" class="text-sm text-gray-600 mt-1">Patient ID: —</div>
        </div>
      </div>
    </header>

    <section class="p-6">
      <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Profile Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <div class="text-sm font-medium text-gray-500 mb-1">First Name</div>
            <div id="first-name" class="text-lg text-gray-900">—</div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500 mb-1">Last Name</div>
            <div id="last-name" class="text-lg text-gray-900">—</div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500 mb-1">Email</div>
            <div id="email" class="text-lg text-gray-900">—</div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500 mb-1">Contact Number</div>
            <div id="contactno" class="text-lg text-gray-900">—</div>
          </div>
          <div class="md:col-span-2">
            <div class="text-sm font-medium text-gray-500 mb-1">Address</div>
            <div id="address" class="text-lg text-gray-900">—</div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500 mb-1">Birth Date</div>
            <div id="birth-date" class="text-lg text-gray-900">—</div>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-500 mb-1">Age</div>
            <div id="age" class="text-lg text-gray-900">—</div>
          </div>
          <div class="md:col-span-2">
            <div class="text-sm font-medium text-gray-500 mb-1">Allergies</div>
            <div id="allergies" class="text-lg text-gray-900">—</div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<script src="../../../public/assets/js/patient/profile.js"></script>
</body>
</html>
