document.addEventListener("DOMContentLoaded", () => {
  const registrationForm = document.getElementById("registrationForm");
  if (registrationForm) {
    registrationForm.addEventListener("submit", handleRegistration);
  }

  async function handleRegistration(event) {
    event.preventDefault();

    const email = document.getElementById("regEmail").value;
    const password = document.getElementById("regPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const phoneNumber = document.getElementById("phoneNumber").value;
    const role = document.getElementById("regRole").value;
    const agreeTerms = document.getElementById("agreeTerms").checked;

    if (password !== confirmPassword) {
      showMessage("registrationMessage", "Passwords do not match!", true);
      return;
    }

    if (!agreeTerms) {
      showMessage(
        "registrationMessage",
        "You must agree to the Terms of Service and Privacy Policy.",
        true
      );
      return;
    }

    const formData = {
      first_name: firstName,
      last_name: lastName,
      email: email,
      password: password,
      role: role,
      contactno: phoneNumber,
    };

    if (role === "PATIENT") {
      formData.birth_date = document.getElementById("patientBirthDate").value;
    } else if (role === "DOCTOR") {
      formData.birth_date = document.getElementById("doctorBirthDate").value;
      formData.specialization = document.getElementById(
        "doctorSpecialization"
      ).value;
      formData.prc_license = document.getElementById("doctorLicense").value;
      formData.clinic_name = document.getElementById("clinicName").value;
    } else if (role === "PHARMACY") {
      formData.pharmacy_name = document.getElementById("pharmacyName").value;
      formData.address = document.getElementById("address").value;
      formData.operating_hours =
        document.getElementById("operatingHours").value;
    }

    console.log("Sending registration data:", formData);

    try {
      const response = await fetch(
        "../src/api/authRoutes.php?action=register",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(formData),
        }
      );

      console.log("Response status:", response.status);
      console.log("Response headers:", [...response.headers.entries()]);

      const result = await response.json();
      console.log("Response data:", result);

      if (response.ok && result.success) {
        const role = formData.role;

        if (role === "DOCTOR" || role === "PHARMACY") {
          showMessage("registrationMessage", "Registration successful!", false);
          setTimeout(() => {
            showVerificationModal(true);
          }, 500);
          return;
        }

        if (role === "PATIENT") {
          showMessage(
            "registrationMessage",
            "Registration successful! Redirecting to your dashboard...",
            false
          );

          const user = {
            email: formData.email,
            role: formData.role,
            user_id: result.user_id,
          };
          sessionStorage.setItem("loggedInUser", JSON.stringify(user));

          setTimeout(() => {
            window.location.href = "../src/views/patient/PatientDashboard.php";
          }, 2000);
        }
      } else {
        showMessage(
          "registrationMessage",
          result.error || "Registration failed!",
          true
        );
      }
    } catch (error) {
      console.error("Registration error:", error);
      showMessage(
        "registrationMessage",
        "An error occurred during registration: " + error.message,
        true
      );
    }
  }
});
