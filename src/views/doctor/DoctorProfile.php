<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Doctor Profile</title>
  <link rel="stylesheet" href="..\..\..\public\assets\css\doctor.css" />
</head>
<body>
  <div class="app">
    <aside class="sidebar">
      <div class="brand">Doctor</div>
      <nav>
        <a href="DoctorDashboard.php">Dashboard</a>
        <a href="PatientManagement.php">Patient Management</a>
        <a href="PrescriptionManagement.php">Prescription</a>
        <a class="active" href="DoctorProfile.php">Profile</a>
      </nav>
    </aside>

    <div class="main">
      <header class="topbar">
        <div class="welcome">My Profile</div>
        <div>
          <button id="edit-profile-btn" class="btn">Edit Profile</button>
        </div>
      </header>

      <section class="content">
        <div class="card profile-card">
          <div class="profile-left">
            <div id="profile-photo" class="placeholder-photo">Photo</div>
          </div>
          <div class="profile-right">
            <h2 id="profile-name">Dr. —</h2>
            <div id="profile-special">—</div>
            <div id="profile-prc">PRC License: —</div>

            <form id="profile-form" class="profile-form">
              <div class="two-col">
                <input id="first_name" placeholder="First Name" />
                <input id="last_name" placeholder="Last Name" />
              </div>
              <div class="two-col">
                <input id="contactno" placeholder="Contact Number" />
                <input id="email" placeholder="Email" />
              </div>
              <div class="two-col">
                <input id="prc_license" placeholder="PRC License Number" />
                <input id="specialization" placeholder="Specialization" />
              </div>
              <input id="clinic_name" placeholder="Hospital/Clinic" />
              <input id="address" placeholder="Hospital/Clinic Address" />
              <div class="row">
                <button type="button" id="cancel-profile" class="btn small secondary">Cancel</button>
                <button type="submit" id="save-profile" class="btn small">Save</button>
              </div>
            </form>
          </div>
        </div>
      </section>

    </div>
  </div>

  <script src="./js/doctorProfile.js"></script>
</body>
</html>
