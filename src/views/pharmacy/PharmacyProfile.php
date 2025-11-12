<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Pharmacy Profile</title>
  <link rel="stylesheet" href="..\..\..\public\assets\css\pharmacy.css" />
</head>
<body>
  <div class="app">
    <?php
    $activePage = 'profile';
    include '../components/PharmacySidebar.php';
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
          <div class="profile-right">
            <h2 id="profile-name">—</h2>
            <div id="profile-email">—</div>
            <div id="profile-contact">Contact: —</div>
            <div id="profile-address">Address: —</div>
            <div id="profile-hours">Operating Hours: —</div>

            <form id="profile-form" class="profile-form">
              <div class="two-col">
                <input id="pharmacy_name" placeholder="Pharmacy Name" />
                <input id="email" placeholder="Email" />
              </div>
              <div class="two-col">
                <input id="contact_number" placeholder="Contact Number" />
                <input id="address" placeholder="Address" />
              </div>
              <div class="two-col">
                <input id="open_time" type="time" placeholder="Opening Time" />
                <input id="close_time" type="time" placeholder="Closing Time" />
              </div>
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

  <script src="..\..\..\public\assets\js\pharmacy\profile.js"></script>
</body>
</html>
