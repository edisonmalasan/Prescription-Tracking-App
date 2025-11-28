<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Pharmacy Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <div class="flex">
    <?php
    $activePage = 'profile';
    include '../components/PharmacySidebar.php';
    ?>

    <div class="ml-64 flex-1 min-h-screen">
      <header class="bg-white shadow-md p-6">
        <div class="flex justify-between items-center">
          <div class="text-2xl font-bold text-gray-800">My Profile</div>
          <div>
            <button id="edit-profile-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
              Edit Profile
            </button>
          </div>
        </div>
      </header>

      <section class="p-6">
        <div class="bg-white rounded-lg shadow-md p-8 max-w-3xl">
          <div id="profile-view">
            <h2 id="profile-name" class="text-3xl font-bold text-gray-800 mb-4">—</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Email</div>
                <div id="profile-email" class="text-lg text-gray-900">—</div>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Contact</div>
                <div id="profile-contact" class="text-lg text-gray-900">—</div>
              </div>
              <div class="md:col-span-2">
                <div class="text-sm font-medium text-gray-500 mb-1">Address</div>
                <div id="profile-address" class="text-lg text-gray-900">—</div>
              </div>
              <div class="md:col-span-2">
                <div class="text-sm font-medium text-gray-500 mb-1">Operating Hours</div>
                <div id="profile-hours" class="text-lg text-gray-900">—</div>
              </div>
            </div>
          </div>

          <form id="profile-form" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <input id="pharmacy_name" placeholder="Pharmacy Name" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
              <input id="email" placeholder="Email" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
              <input id="contact_number" placeholder="Contact Number" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
              <input id="address" placeholder="Address" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
              <input id="open_time" type="time" placeholder="Opening Time" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
              <input id="close_time" type="time" placeholder="Closing Time" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
            </div>
            <div class="flex gap-4">
              <button type="button" id="cancel-profile" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                Cancel
              </button>
              <button type="submit" id="save-profile" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                Save
              </button>
            </div>
          </form>
        </div>
      </section>
    </div>
  </div>

  <script src="../../../public/assets/js/pharmacy/profile.js"></script>
</body>
</html>
