document.addEventListener("DOMContentLoaded", () => {
  const registrationForm = document.getElementById("registrationForm");
  if (registrationForm) {
    registrationForm.addEventListener("submit", handleRegistration);

    setupFieldErrorClearing();
  }

  function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + "Error");

    if (field && errorDiv) {
      field.classList.remove("border-gray-300");
      field.classList.add(
        "border-red-500",
        "focus:border-red-500",
        "focus:ring-red-500"
      );
      errorDiv.textContent = message;
      errorDiv.classList.remove("hidden");
    }
  }

  function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + "Error");

    if (field && errorDiv) {
      field.classList.remove(
        "border-red-500",
        "focus:border-red-500",
        "focus:ring-red-500"
      );
      field.classList.add("border-gray-300");
      errorDiv.classList.add("hidden");
      errorDiv.textContent = "";
    }
  }

  function clearAllErrors() {
    const errorFields = [
      "firstName",
      "lastName",
      "regEmail",
      "phoneNumber",
      "regPassword",
      "confirmPassword",
      "regRole",
      "patientBirthDate",
      "doctorBirthDate",
      "doctorSpecialization",
      "doctorLicense",
      "clinicName",
      "pharmacyName",
      "address",
      "operatingHours",
    ];

    errorFields.forEach((fieldId) => clearFieldError(fieldId));

    const agreeTermsError = document.getElementById("agreeTermsError");
    if (agreeTermsError) {
      agreeTermsError.classList.add("hidden");
      agreeTermsError.textContent = "";
    }
  }

  function setupFieldErrorClearing() {
    const fields = [
      "firstName",
      "lastName",
      "regEmail",
      "phoneNumber",
      "regPassword",
      "confirmPassword",
      "patientBirthDate",
      "doctorBirthDate",
      "doctorSpecialization",
      "doctorLicense",
      "clinicName",
      "pharmacyName",
      "address",
      "operatingHours",
    ];

    fields.forEach((fieldId) => {
      const field = document.getElementById(fieldId);
      if (field) {
        field.addEventListener("input", () => clearFieldError(fieldId));
        field.addEventListener("change", () => clearFieldError(fieldId));
      }
    });

    const agreeTerms = document.getElementById("agreeTerms");
    if (agreeTerms) {
      agreeTerms.addEventListener("change", () =>
        clearFieldError("agreeTerms")
      );
    }
  }

  async function handleRegistration(event) {
    event.preventDefault();
    clearAllErrors();

    const email = document.getElementById("regEmail").value.trim();
    const password = document.getElementById("regPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const firstName = document.getElementById("firstName").value.trim();
    const lastName = document.getElementById("lastName").value.trim();
    const phoneNumber = document.getElementById("phoneNumber").value.trim();
    const role = document.getElementById("regRole").value;
    const agreeTerms = document.getElementById("agreeTerms").checked;

    let hasErrors = false;

    if (!firstName) {
      showFieldError("firstName", "First name is required");
      hasErrors = true;
    }

    if (!lastName) {
      showFieldError("lastName", "Last name is required");
      hasErrors = true;
    }

    if (!email) {
      showFieldError("regEmail", "Email is required");
      hasErrors = true;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showFieldError("regEmail", "Please enter a valid email address");
      hasErrors = true;
    }

    if (!password) {
      showFieldError("regPassword", "Password is required");
      hasErrors = true;
    } else if (password.length < 6) {
      showFieldError("regPassword", "Password must be at least 6 characters");
      hasErrors = true;
    }

    if (!confirmPassword) {
      showFieldError("confirmPassword", "Please confirm your password");
      hasErrors = true;
    } else if (password !== confirmPassword) {
      showFieldError("confirmPassword", "Passwords do not match");
      hasErrors = true;
    }

    if (!agreeTerms) {
      const agreeTermsError = document.getElementById("agreeTermsError");
      if (agreeTermsError) {
        agreeTermsError.textContent =
          "You must agree to the Terms of Service and Privacy Policy";
        agreeTermsError.classList.remove("hidden");
      }
      hasErrors = true;
    }

    if (role === "PATIENT") {
      const birthDate = document.getElementById("patientBirthDate").value;
      if (!birthDate) {
        showFieldError("patientBirthDate", "Birth date is required");
        hasErrors = true;
      }
    } else if (role === "DOCTOR") {
      const birthDate = document.getElementById("doctorBirthDate").value;
      const prcLicense = document.getElementById("doctorLicense").value.trim();

      if (!birthDate) {
        showFieldError("doctorBirthDate", "Birth date is required");
        hasErrors = true;
      }
      if (!prcLicense) {
        showFieldError("doctorLicense", "PRC License is required");
        hasErrors = true;
      }
    } else if (role === "PHARMACY") {
      const pharmacyName = document.getElementById("pharmacyName").value.trim();
      if (!pharmacyName) {
        showFieldError("pharmacyName", "Pharmacy name is required");
        hasErrors = true;
      }
    }

    if (hasErrors) {
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
          setTimeout(() => {
            showVerificationModal(true);
          }, 500);
          return;
        }

        if (role === "PATIENT") {
          const user = {
            email: formData.email,
            role: formData.role,
            user_id: result.user_id,
          };
          sessionStorage.setItem("loggedInUser", JSON.stringify(user));

          setTimeout(() => {
            window.location.href = "../src/views/patient/PatientDashboard.php";
          }, 500);
        }
      } else {
        const errorMessage = result.error || "Registration failed!";

        if (errorMessage.toLowerCase().includes("email")) {
          showFieldError("regEmail", errorMessage);
        } else if (errorMessage.toLowerCase().includes("password")) {
          showFieldError("regPassword", errorMessage);
        } else if (
          errorMessage.toLowerCase().includes("first name") ||
          errorMessage.toLowerCase().includes("first_name")
        ) {
          showFieldError("firstName", errorMessage);
        } else if (
          errorMessage.toLowerCase().includes("last name") ||
          errorMessage.toLowerCase().includes("last_name")
        ) {
          showFieldError("lastName", errorMessage);
        } else {
          showFieldError("regEmail", errorMessage);
        }
      }
    } catch (error) {
      console.error("Registration error:", error);
      showFieldError(
        "regEmail",
        "An error occurred during registration. Please try again."
      );
    }
  }
});
