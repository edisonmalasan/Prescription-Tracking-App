<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../../../public/assets/css/patient.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <h1>Prescription Tracking System</h1>
            <ul class="nav-links">
                <li><a href="PatientDashboard.php">Dashboard</a></li>
                <li><a href="MyPrescription.php">My Prescriptions</a></li>
                <li><a href="PatientProfile.php" class="active">Profile</a></li>
                <li><a href="../../../public/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="dashboard">
        <section class="profile-management">
            <h2>My Profile</h2>

            <div class="profile-container">
                <div class="profile-photo">
                    <img src="../../../public/assets/images/default-avatar.png" alt="Profile Photo" id="profile-image">
                </div>
                <div class="profile-info">
                    <h3 id="profile-name"><?php echo "Loading..."; ?></h3>
                    <p id="profile-email"></p>
                </div>
            </div>

            <div class="form-section">
                <h3>Personal Information</h3>
                <form class="profile-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="dateOfBirth">Date of Birth</label>
                            <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($dateOfBirth ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="contactNumber">Contact Number</label>
                            <input type="tel" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($contactNumber ?? ''); ?>">
                        </div>
                    </div>

                    <h3>Address Information</h3>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="address">Street Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="province">Province</label>
                            <input type="text" id="province" name="province" value="<?php echo htmlspecialchars($province ?? ''); ?>">
                        </div>
                    </div>

                    <h3>Medical Information</h3>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="allergies">Known Allergies</label>
                            <input type="text" id="allergies" name="allergies" value="<?php echo htmlspecialchars($allergies ?? ''); ?>">
                        </div>
                    </div>

                </form>
            </div>
        </section>
    </main>

    <script src="../../../public/assets/js/patient/profile.js"></script>
</body>
</html>
