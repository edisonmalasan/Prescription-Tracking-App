<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions</title>
    <link rel="stylesheet" href="../../../public/assets/css/patient.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <h2>Patient Portal</h2>
        </div>
        <ul class="nav-list">
            <li class="nav-item<?php if(basename($_SERVER['PHP_SELF']) === 'PatientDashboard.php') echo ' active'; ?>">
                <a href="PatientDashboard.php">Dashboard</a>
            </li>
            <li class="nav-item<?php if(basename($_SERVER['PHP_SELF']) === 'MyPrescription.php') echo ' active'; ?>">
                <a href="MyPrescription.php">My Prescriptions</a>
            </li>
            <li class="nav-item<?php if(basename($_SERVER['PHP_SELF']) === 'PatientProfile.php') echo ' active'; ?>">
                <a href="PatientProfile.php">Profile</a>
            </li>
        </ul>
    </nav>
        
        <main class="main-content">
            <header class="header">
                My Prescriptions
            </header>
            
            <div class="prescription-controls">
                <div class="tabs" role="tablist">
                    <button class="tab active" role="tab" aria-selected="true" aria-controls="active-prescriptions">
                        Active (<?php echo $activePrescriptionCount ?? '0'; ?>)
                    </button>
                    <button class="tab" role="tab" aria-selected="false" aria-controls="completed-prescriptions">
                        Completed (<?php echo $completedPrescriptionCount ?? '0'; ?>)
                    </button>
                </div>

                <div class="prescription-filters">
                    <input type="text" 
                            class="search-prescriptions" 
                            placeholder="Search prescriptions..."
                            aria-label="Search prescriptions">
                    <select class="filter-by-date" aria-label="Filter by date">
                        <option value="all">All Time</option>
                        <option value="month">Last Month</option>
                        <option value="3months">Last 3 Months</option>
                        <option value="6months">Last 6 Months</option>
                        <option value="year">Last Year</option>
                    </select>
                </div>
            </div>
            
            <div class="prescriptions-list" role="tabpanel" id="active-prescriptions">
                <?php if (empty($prescriptions)): ?>
                <div class="no-prescriptions">
                    No prescriptions found
                </div>
                <?php else: ?>
                    <!-- Prescriptions will be loaded by JavaScript -->
                    <div class="loading-prescriptions">
                        Loading prescriptions...
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Prescription Detail Modal Template -->
    <template id="prescription-modal">
        <div class="modal prescription-detail-modal" role="dialog" aria-labelledby="modal-title">
            <div class="modal-content">
                <header class="modal-header">
                    <h2 id="modal-title">Prescription Details</h2>
                    <button class="close-modal" aria-label="Close">&times;</button>
                </header>
                <div class="modal-body">
                    <!-- Content will be dynamically populated -->
                </div>
                <footer class="modal-footer">
                    <button class="btn btn-secondary close-modal">Close</button>
                    <button class="btn btn-primary print-prescription">Print</button>
                </footer>
            </div>
        </div>
    </template>

    <script src="../../../public/assets/js/patient/prescriptions.js"></script>
</body>
</html>


