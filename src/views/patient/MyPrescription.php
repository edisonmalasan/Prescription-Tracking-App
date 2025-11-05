<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions</title>
    <link rel="stylesheet" href="../../../public/assets/css/patient.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <h1>Prescription Tracking System</h1>
            <ul class="nav-links">
                <li><a href="PatientDashboard.php">Dashboard</a></li>
                <li><a href="MyPrescription.php" class="active">My Prescriptions</a></li>
                <li><a href="PatientProfile.php">Profile</a></li>
                <li><a href="../../../public/login.html">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="dashboard">
        <section class="prescription-management">
            <h2>My Prescriptions</h2>
            
            <div class="action-buttons">
                <div class="search-container">
                    <input type="text" 
                            class="search-prescriptions" 
                            placeholder="Search prescriptions..."
                            aria-label="Search prescriptions">
                </div>
                <select class="filter-by-date" aria-label="Filter by date">
                    <option value="all">All Time</option>
                    <option value="month">Last Month</option>
                    <option value="3months">Last 3 Months</option>
                    <option value="6months">Last 6 Months</option>
                    <option value="year">Last Year</option>
                </select>
            </div>

            <div class="tabs-container">
                <div class="tab-buttons">
                    <button class="tab active" data-tab="active">
                        Active (<?php echo $activePrescriptionCount ?? '0'; ?>)
                    </button>
                    <button class="tab" data-tab="completed">
                        Completed (<?php echo $completedPrescriptionCount ?? '0'; ?>)
                    </button>
                </div>

                <div class="patient-table-container">
                    <table class="patient-table" id="active-prescriptions">
                        <thead>
                            <tr>
                                <th>Prescription ID</th>
                                <th>Medicine</th>
                                <th>Doctor</th>
                                <th>Date Prescribed</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($prescriptions)): ?>
                            <tr>
                                <td colspan="6" class="no-data">No prescriptions found</td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="loading">Loading prescriptions...</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

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
