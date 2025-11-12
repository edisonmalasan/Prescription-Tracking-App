<?php
session_start();
$patient_id = $_SESSION['patient_id'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Patient Dashboard</title>
<link rel="stylesheet" href="../../../public/assets/css/patient.css" />
</head>
<body>
<div class="app">
  <?php
  $activePage = 'dashboard';
  include '../components/PatientSidebar.php';
  ?>

  <div class="main">
    <header class="topbar">
      <div class="welcome">
        <div id="patient-name">Welcome, —</div>
        <div id="patient-id" class="sub">Patient ID: —</div>
      </div>
    </header>

    <section class="content">
      <div class="cards">
        <div class="card stat">
          <h4>Total Prescriptions</h4>
          <div class="stat-value" id="stat-total-prescriptions">0</div>
        </div>
        <div class="card stat">
          <h4>Active Prescriptions</h4>
          <div class="stat-value" id="stat-active-prescriptions">0</div>
        </div>
      </div>

      <div class="card prescription-list">
        <h3>Recent Prescriptions</h3>
        <table id="recent-prescriptions">
          <thead>
            <tr>
              <th>Prescription ID</th>
              <th>Doctor</th>
              <th>Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </section>
  </div>
</div>

<script src="../../../public/assets/js/patient/patientdashboard.js"></script>
</body>
</html>
