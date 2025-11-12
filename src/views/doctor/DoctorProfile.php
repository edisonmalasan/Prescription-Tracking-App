<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Doctor Profile</title>
  <link rel="stylesheet" href="../../../public/assets/css/doctor.css" />
</head>
<body>
  <div class="app">
    <?php
    $activePage = 'profile';
    include '../components/DoctorSidebar.php';
    ?>

    <div class="main">
      <header class="topbar">
        <div class="welcome">My Profile</div>
        <div>
          <button id="edit-profile-btn" class="btn">Edit Profile</button>
        </div>
      </header>

      <section class="content">
        <div class="card profile-card">
          <!-- VIEW MODE -->
          <div id="profile-view">
            <h2 id="profile-name">Dr. —</h2>
            <div id="profile-special">Specialization: —</div>
            <div id="profile-prc">PRC License: —</div>
            <div id="profile-contact">Contact: —</div>
            <div id="profile-email">Email: —</div>
            <div id="profile-clinic">Clinic: —</div>
          </div>

          <!-- EDIT FORM -->
          <form id="profile-form">
            <div class="two-col">
              <input id="first_name" placeholder="First Name" disabled />
              <input id="last_name" placeholder="Last Name" disabled />
            </div>
            <div class="two-col">
              <input id="contactno" placeholder="Contact Number" disabled />
              <input id="email" placeholder="Email" disabled />
            </div>
            <div class="two-col">
              <input id="prc_license" placeholder="PRC License Number" disabled />
              <input id="specialization" placeholder="Specialization" disabled />
            </div>
            <input id="clinic_name" placeholder="Hospital/Clinic" disabled />
            <input id="address" placeholder="Hospital/Clinic Address" disabled />

            <div class="row">
              <button type="button" id="cancel-profile" class="btn small secondary">Cancel</button>
              <button type="submit" id="save-profile" class="btn small">Save</button>
            </div>
          </form>
        </div>
      </section>

    </div>
  </div>

  <script src="../../../public/assets/js/doctor/profile.js"></script>
</body>
</html>
