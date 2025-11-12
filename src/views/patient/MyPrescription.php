<?php
session_start();
$patient_id = $_SESSION['patient_id'] ?? null;
$prescription_id = $_GET['prescription_id'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>My Prescriptions</title>
<link rel="stylesheet" href="../../../public/assets/css/patient.css" />
</head>
<body>
<div class="app">
  <?php
  $activePage = 'prescriptions';
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
      <div class="card prescription-list">
        <h3>Prescription Items</h3>
        <table id="itemsTable">
          <thead>
            <tr>
              <th>Drug</th>
              <th>Dosage</th>
              <th>Frequency</th>
              <th>Duration</th>
              <th>Refills</th>
              <th>Instructions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </section>
  </div>
</div>

<script src="../../../public/assets/js/patient/prescription.js"></script>
</body>
</html>