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
<link rel="stylesheet" href="../../../public/assets/css/patient.css" />
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">Patient</div>
    <nav>
      <a href="PatientDashboard.php">Dashboard</a>
      <a href="MyPrescription.php">My Prescriptions</a>
      <a class="active" href="PatientProfile.php">Profile</a>
    </nav>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="welcome">
        <div id="patient-name">Welcome, —</div>
        <div id="patient-id" class="sub">Patient ID: —</div>
      </div>
    </header>

    <section class="content">
      <div class="card profile-card">
        <div class="profile-right">
          <div class="small">First Name</div>
          <div id="first-name">—</div>

          <div class="small">Last Name</div>
          <div id="last-name">—</div>

          <div class="small">Email</div>
          <div id="email">—</div>

          <div class="small">Contact Number</div>
          <div id="contactno">—</div>

          <div class="small">Address</div>
          <div id="address">—</div>

          <div class="small">Birth Date</div>
          <div id="birth-date">—</div>

          <div class="small">Age</div>
          <div id="age">—</div>

          <div class="small">Allergies</div>
          <div id="allergies">—</div>
        </div>
      </div>
    </section>
  </div>
</div>

<script>
const USER_ID = <?= json_encode($patient_id) ?>;
</script>
<script src="../../../js/patientProfile.js"></script>
</body>
</html>
