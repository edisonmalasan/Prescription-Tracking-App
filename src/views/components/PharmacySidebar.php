<?php
$activePage = $activePage ?? 'dashboard';
?>
<aside class="sidebar">
  <div class="brand">Pharmacy</div>
  <nav>
    <a href="PharmacyDashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
    <a href="PharmacyProfile.php" class="<?= $activePage === 'profile' ? 'active' : '' ?>">Profile</a>
  </nav>
  <div class="sidebar-footer">
    <a href="../../../public/login.html" class="logout-btn">Logout</a>
  </div>
</aside>
