<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Create Prescription</title>
  <link rel="stylesheet" href="..\..\..\public\assets\css\doctor.css" />
</head>
<body>
  <div class="app">
    <?php
    $activePage = 'prescriptions';
    include '../components/DoctorSidebar.php';
    ?>

    <div class="main">
      <header class="topbar">
        <div class="welcome">Create Prescription</div>
        <div></div>
      </header>

      <section class="content">
        <div class="two-col">
          <div>
            <div class="card">
              <h4>Patient Selection</h4>
              <input id="presc-search-patient" placeholder="Search Patient" />
              <div id="patient-search-results" class="search-results"></div>
              <div id="selected-patient" class="card selected-box">No patient selected</div>
            </div>

            <div class="card">
              <h4>Prescription Details</h4>
              <label>Medication Name</label>
              <input id="medication-name" placeholder="Start typing..."/>
              <div id="drug-suggestions" class="search-results"></div>

              <div class="form-row">
                <div class="form-group">
                  <label>Dosage</label>
                  <input id="dosage" />
                </div>
                <div class="form-group">
                  <label>Frequency</label>
                  <input id="frequency" />
                </div>
                <div class="form-group">
                  <label>Duration (days)</label>
                  <input id="duration" type="number" />
                </div>
              </div>

              <label>Refills</label>
              <input id="refills" type="number" value="0" />
              <label>Special Instructions</label>
              <textarea id="instructions"></textarea>

              <div class="row">
                <button id="cancel-presc" class="btn small secondary">Cancel</button>
                <button id="create-presc" class="btn small">Create Prescription</button>
              </div>
            </div>
          </div>

          <aside class="card narrow">
            <h4>Patient Allergies</h4>
            <div id="patient-allergies">—</div>

            <h4>Current Medications</h4>
            <div id="current-medications">—</div>
          </aside>
        </div>
      </section>
    </div>
  </div>

  <script src="../../../public/assets/js/doctor/prescription.js"></script>
</body>
</html>
