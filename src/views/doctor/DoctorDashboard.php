<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Doctor Dashboard</title>
  <link rel="stylesheet" href="..\..\..\public\assets\css\doctor.css" />
</head>
<body>
  <div class="app">
    <?php
    $activePage = 'dashboard';
    include '../components/DoctorSidebar.php';
    ?>

    <div class="main">
      <header class="topbar">
        <div class="welcome">
          <div id="doctor-name">Welcome, Dr. —</div>
          <div id="doctor-prc" class="sub">PRC License: —</div>
        </div>
        <div>
          <button id="new-prescription-btn" class="btn">New Prescription</button>
        </div>
      </header>

      <section class="content">
        <div class="cards">
          <div class="card stat">
            <h4>Total Patients</h4>
            <div class="stat-value" id="stat-total-patients">0</div>
          </div>

          <div class="card stat">
            <h4>Active Prescriptions</h4>
            <div class="stat-value" id="stat-active-prescriptions">0</div>
          </div>

          <div class="card stat">
            <h4>This Week</h4>
            <div class="stat-value" id="stat-this-week">0</div>
          </div>

          <div class="card stat">
            <h4>This Month</h4>
            <div class="stat-value" id="stat-this-month">0</div>
          </div>
        </div>

        <div class="card prescription-list">
          <h3>Recent Prescriptions</h3>
          <table id="recent-prescriptions">
            <thead>
              <tr>
                <th>Patient</th>
                <th>Medication</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </section>
    </div>
  </div>

  <script src="..\..\public\assets\js\doctor\doctorDashboard.js"></script>
</body>
</html>
