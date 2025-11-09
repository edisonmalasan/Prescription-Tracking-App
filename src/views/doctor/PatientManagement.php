<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Patient Management</title>
  <link rel="stylesheet" href="..\..\..\public\assets\css\doctor.css" />
</head>
<body>
  <div class="app">
    <?php
    $activePage = 'patients';
    include '../components/DoctorSidebar.php';
    ?>

    <div class="main">
      <header class="topbar">
        <div class="welcome">My Patients</div>
        <div>
          <button id="add-patient-btn" class="btn">+ Add Patient</button>
        </div>
      </header>

      <section class="content">
        <div class="card">
          <div class="row">
            <input id="search-patient" placeholder="Search Patients" />
            <button id="search-btn" class="btn small">Search</button>
          </div>

          <table id="patients-table">
            <thead>
              <tr><th>Name</th><th>Age</th><th>Contact</th><th>Last Visit</th><th>Action</th></tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </section>
    </div>
  </div>

  <!-- patient detail modal -->
  <div id="patient-modal" class="modal hidden">
    <div class="modal-inner">
      <button class="close-modal">&times;</button>
      <div id="patient-modal-content"></div>
    </div>
  </div>

  <script src="..\..\public\assets\js\doctor\patientManagement.js"></script>
</body>
</html>
