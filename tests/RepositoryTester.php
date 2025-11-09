<?php

// Change working directory so relative requires inside repository files resolve
chdir(__DIR__ . '/../src/repositories');
require_once 'UserRepository.php';
require_once 'DoctorRepository.php';
require_once 'PatientRepository.php';
require_once 'PharmacyRepository.php';
require_once 'AdminRepository.php';
require_once 'DrugRepository.php';
require_once 'MedicalRecordRepository.php';
require_once 'PrescriptionRepository.php';

date_default_timezone_set('UTC');

try {
	// Simple check helpers for fetch/search functions
	function checkExists($label, $val) {
		if ($val === null || $val === false) {
			echo "[FAIL] $label returned null/false\n";
			return false;
		}
		if (is_array($val) && count($val) === 0) {
			echo "[FAIL] $label returned empty array\n";
			return false;
		}
		echo "[PASS] $label returned result\n";
		return true;
	}

	function checkArray($label, $arr) {
		if (!is_array($arr)) {
			echo "[FAIL] $label did not return an array\n";
			return false;
		}
		if (count($arr) === 0) {
			echo "[FAIL] $label returned empty array\n";
			return false;
		}
		echo "[PASS] $label returned " . count($arr) . " rows\n";
		return true;
	}

	$userRepo = new UserRepository();

	$now = date('Y-m-d H:i:s');

	// User data for Marco Polozki
	$userData = [
		'last_name' => 'Polozki',
		'first_name' => 'Marco',
		'role' => 'doctor',
		'email' => 'marco222@gmail.com',
		'contactno' => '999',
		'pass_hash' => 'pass500',
		'address' => 'pizza tower',
		'created_at' => $now,
		'updated_at' => $now
	];

	echo "Checking for existing user...\n";
	$existing = $userRepo->findByEmail($userData['email']);
	checkExists('UserRepository::findByEmail', $existing);
	if ($existing) {
		$userId = $existing['user_id'];
		echo "User already exists with ID: $userId\n";
	} else {
		echo "Creating user...\n";
		$userId = $userRepo->create($userData);
		echo "User created with ID: $userId\n";
	}

	// Prepare doctor data compatible with DoctorRepository::create
	$doctor = new stdClass();
	$doctor->user_id = $userId;
	$doctor->prc_license = 'prc999';
	$doctor->specialization = 'Cardiologist';
	$doctor->verified = 1; // repository accepts 'verified' or 'isVerified'
	$doctor->isVerified = 1;
	$doctor->birth_date = '1987-01-20';
	$doctor->clinic_name = 'StayAlive';

	$doctorRepo = new DoctorRepository();
	// Check if doctor record already exists for this user
	$existingDoctor = $doctorRepo->findByUserId($userId);
	if ($existingDoctor) {
		echo "Doctor record already exists for user_id $userId\n";
		$doctorId = $userId;
	} else {
		echo "Creating doctor record...\n";
		$doctorId = $doctorRepo->create($doctor);
		echo "Doctor record created with ID (user_id): $doctorId\n";
	}

	// Fetch back to verify
	$fetchedUser = $userRepo->findById($userId);
	checkExists('UserRepository::findById', $fetchedUser);
	$fetchedDoctor = $doctorRepo->findByUserId($userId);
	checkExists('DoctorRepository::findByUserId', $fetchedDoctor);

	echo "\nInserted user row:\n";
	print_r($fetchedUser);

	echo "\nInserted doctor row:\n";
	print_r($fetchedDoctor);

	// --- Create a sample patient: Sam P. Le ---
	echo "\n--- Creating sample patient Sam P. Le ---\n";
	$patientRepo = new PatientRepository();

	$patientData = new stdClass();
	$patientData->first_name = 'Sam P.';
	$patientData->last_name = 'Le';
	$patientData->role = 'patient';
	$patientData->email = 'sam.le@example.com';
	$patientData->contactno = '0900000000';
	$patientData->pass_hash = 'pass123';
	$patientData->address = '123 Test Lane';
	$patientData->birth_date = '2000-01-01';

	// check existing
	$existingPatientUser = $userRepo->findByEmail($patientData->email);
	checkExists('UserRepository::findByEmail (patient)', $existingPatientUser);
	if ($existingPatientUser) {
		$patientUserId = $existingPatientUser['user_id'];
		echo "Patient user already exists with ID: $patientUserId\n";
	} else {
		echo "Creating patient...\n";
		$patientUserId = $patientRepo->create($patientData);
		if ($patientUserId) {
			echo "Patient user created with ID: $patientUserId\n";
		} else {
			echo "Failed to create patient.\n";
		}
	}

	$fetchedPatientUser = $userRepo->findById($patientUserId);
	checkExists('UserRepository::findById (patient)', $fetchedPatientUser);
	$fetchedPatient = $patientRepo->findByUserId($patientUserId);
	checkExists('PatientRepository::findByUserId', $fetchedPatient);

	echo "\nInserted patient user row:\n";
	print_r($fetchedPatientUser);

	echo "\nInserted patient row:\n";
	print_r($fetchedPatient);

	echo "\nDone.\n";


	// --- Create a sample drug ---
	echo "\n--- Creating sample drug ---\n";
	$drugRepo = new DrugRepository();
	$drugName = 'TestDrug-' . rand(1000,9999);
	$existingDrug = $drugRepo->findByGenericName($drugName);
	checkExists('DrugRepository::findByGenericName', $existingDrug);
	if ($existingDrug) {
		echo "Drug already exists: " . $existingDrug['drug_id'] . "\n";
		$drugId = $existingDrug['drug_id'];
	} else {
		$drug = [
			'generic_name' => $drugName,
			'brand' => 'TestBrand',
			'chemical_name' => 'TestChem',
			'category' => 'OTC',
			'expiry_date' => null,
			'isControlled' => 0
		];
		$drugId = $drugRepo->create($drug);
		echo "Created drug id: $drugId\n";
	}

	// --- Create a sample pharmacy (will create user if needed) ---
	echo "\n--- Creating sample pharmacy ---\n";
	$pharmacyRepo = new PharmacyRepository();
	$pharmacyData = [
		'first_name' => 'Pharm',
		'last_name' => 'Test',
		'email' => 'pharm.test+' . rand(1000,9999) . '@example.com',
		'pharmacy_name' => 'Test Pharmacy',
		'phar_license' => 'LIC' . rand(1000,9999),
		'open_time' => '08:00',
		'close_time' => '18:00',
		'dates_open' => 'Mon-Fri'
	];
	$pharmUserId = $pharmacyRepo->create($pharmacyData);
	if ($pharmUserId) {
		echo "Pharmacy created for user id: $pharmUserId\n";
		$pharm = $pharmacyRepo->findByUserId($pharmUserId);
		checkExists('PharmacyRepository::findByUserId', $pharm);
	} else {
		echo "Failed to create pharmacy\n";
	}

	// --- Create medical record for patient ---
	echo "\n--- Creating medical record for patient ---\n";
	$mrRepo = new MedicalRecordRepository();
	$mrData = [
		'user_id' => $patientUserId,
		'height' => '175',
		'weight' => '70',
		'allergies' => 'None'
	];
	$recordId = $mrRepo->create($mrData);
	if ($recordId) {
		echo "Medical record created with id: $recordId\n";
		$mrFetched = $mrRepo->findByUserId($patientUserId);
		checkExists('MedicalRecordRepository::findByUserId', $mrFetched);
	} else {
		echo "Failed to create medical record\n";
	}

	// --- Create a prescription for the patient by the doctor ---
	echo "\n--- Creating prescription and detail ---\n";
	$presRepo = new PrescriptionRepository();
	$presData = [
		'prescribing_doctor' => $userId,
		'record_id' => $recordId,
		'prescription_date' => date('Y-m-d'),
		'status' => 'pending',
		'details' => [
			[
				'drug_id' => $drugId,
				'duration' => '7 days',
				'dosage' => '1 pill',
				'frequency' => 'Twice a day',
				'refills' => 0,
				'special_instructions' => 'After meals',
				'description' => 'Treat fever and pain'
			]
		]
	];

	$prescriptionId = $presRepo->create($presData);
	if ($prescriptionId) {
		echo "Prescription created id: $prescriptionId\n";
		checkExists('PrescriptionRepository::findById (created)', $presRepo->findById($prescriptionId));
		$details = $presRepo->getPrescriptionDetails($prescriptionId);
		checkArray('PrescriptionRepository::getPrescriptionDetails', $details);
		// Print details to verify description field
		echo "\nInserted prescription details:\n";
		print_r($details);
	} else {
		echo "Failed to create prescription\n";
	}

} catch (Exception $e) {
	echo "Error: " . $e->getMessage() . "\n";
	exit(1);
}

?>

