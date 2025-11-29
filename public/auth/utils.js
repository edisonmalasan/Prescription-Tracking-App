// for modal verif message
function showMessage(elementId, message, isError = false) {
  const messageElement = document.getElementById(elementId);
  if (messageElement) {
    messageElement.textContent = message;
    messageElement.className = `px-4 py-3 rounded-lg text-sm font-medium ${
      isError
        ? "bg-red-50 border border-red-200 text-red-700"
        : "bg-green-50 border border-green-200 text-green-700"
    }`;
    messageElement.classList.remove("hidden");
  }
}

let isRegistrationModal = false;

function showVerificationModal(isRegistration = false) {
  isRegistrationModal = isRegistration;
  const modal = document.getElementById("verificationModal");
  if (modal) {
    modal.classList.remove("hidden");
  }
}

function closeVerificationModal() {
  const modal = document.getElementById("verificationModal");
  if (modal) {
    modal.classList.add("hidden");
    if (isRegistrationModal) {
      setTimeout(() => {
        window.location.href = "/login.html";
      }, 300);
    }
  }
}

window.showMessage = showMessage;
window.showVerificationModal = showVerificationModal;
window.closeVerificationModal = closeVerificationModal;
