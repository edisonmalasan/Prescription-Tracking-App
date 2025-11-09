<?php
$activePage = $activePage ?? 'dashboard';
?>
<aside class="sidebar">
  <div class="brand">Doctor</div>
  <nav>
    <a href="DoctorDashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
    <a href="PatientManagement.php" class="<?= $activePage === 'patients' ? 'active' : '' ?>">Patient Management</a>
    <a href="PrescriptionManagement.php" class="<?= $activePage === 'prescriptions' ? 'active' : '' ?>">Prescription</a>
    <a href="DoctorProfile.php" class="<?= $activePage === 'profile' ? 'active' : '' ?>">Profile</a>
  </nav>
  <div class="sidebar-footer">
    <a href="../../../public/login.html" class="logout-btn">Logout</a>
  </div>
</aside>

