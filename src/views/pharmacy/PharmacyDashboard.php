<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Pharmacy Dashboard</title>
  <link rel="stylesheet" href="..\..\..\public\assets\css\pharmacy.css" />
</head>
<body>
  <div class="app">
    <aside class="sidebar">
      <div class="brand">Pharmacy</div>
      <nav>
        <a class="active" href="PharmacyDashboard.php">Dashboard</a>
        <a href="PharmacyProfile.php">Profile</a>
      </nav>
    </aside>

    <div class="main">
      <header class="topbar">
        <div class="welcome">
          <div id="pharmacy-name">Welcome, —</div>
          <div id="pharmacy-address" class="sub">Address: —</div>
        </div>
      </header>

      <section class="content">
        <div class="cards">
          <div class="card stat">
            <h4>Total Prescriptions</h4>
            <div class="stat-value" id="stat-total-prescriptions">0</div>
          </div>

          <div class="card stat">
            <h4>Pending Prescriptions</h4>
            <div class="stat-value" id="stat-pending-prescriptions">0</div>
          </div>

          <div class="card stat">
            <h4>Filled Prescriptions</h4>
            <div class="stat-value" id="stat-filled-prescriptions">0</div>
          </div>
        </div>

        <div class="card prescription-list">
          <h3>All Prescriptions</h3>
          <div class="filter-container">
            <input type="text" id="patient-name-filter" placeholder="Filter by Patient Name...">
            <input type="text" id="drug-name-filter" placeholder="Filter by Drug Name...">
          </div>
          <table id="prescriptions-table">
            <thead>
              <tr>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Drug Name</th>
                <th>Dosage</th>
                <th>Duration</th>
                <th>Date Prescribed</th>
                <th>Notes</th>
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

  <script src="..\..\..\public\assets\js\pharmacy\dashboard.js"></script>
</body>
</html>
