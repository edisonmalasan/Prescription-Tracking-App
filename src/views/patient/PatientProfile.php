<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../../../public/assets/css/patient.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2 class="sidebar-header">Patient</h2>
            <ul class="nav-list">
                <li class="nav-item active" data-page="PatientDashboard.php">Dashboard</li>
                <li class="nav-item" data-page="MyPrescriptions.php">My Prescriptions</li>
                <li class="nav-item" data-page="PatientProfile.php">Profile</li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="header">
                My Profile
            </header>
            
            <div class="profile-section">
                <div class="profile-header">
                    <div class="profile-photo"></div>
                    <div class="profile-name">
                        <?php 
                        // Will be updated by JavaScript with actual user data
                        echo "Loading..."; 
                        ?>
                    </div>
                </div>
                
                <form class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dateOfBirth">Date of Birth</label>
                            <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($dateOfBirth ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="contactNumber">Contact Number</label>
                            <input type="tel" id="contactNumber" name="contactNumber" value="<?php echo htmlspecialchars($contactNumber ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="province">Province</label>
                            <input type="text" id="province" name="province" value="<?php echo htmlspecialchars($province ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
            
            <div class="medical-section">
                <div class="medical-header">Medical Information</div>
                <div class="medical-content">
                    <label for="allergies">Known Allergies</label>
                    <input type="text" id="allergies" name="allergies" value="<?php echo htmlspecialchars($allergies ?? ''); ?>">
                </div>
            </div>
        </main>
    </div>

    <script src="../../../public/assets/js/patient/profile.js"></script>
</body>
</html>
