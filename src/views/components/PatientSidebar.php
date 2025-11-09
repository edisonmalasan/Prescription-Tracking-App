<?php
$activePage = $activePage ?? 'dashboard';
?>
<aside class="sidebar">
  <div class="brand">Patient</div>
  <nav>
    <a href="PatientDashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
    <a href="MyPrescription.php" class="<?= $activePage === 'prescriptions' ? 'active' : '' ?>">My Prescriptions</a>
    <a href="PatientProfile.php" class="<?= $activePage === 'profile' ? 'active' : '' ?>">Profile</a>
  </nav>
  <div class="sidebar-footer">
    <a href="../../../public/login.html" class="logout-btn">Logout</a>
  </div>
</aside>

