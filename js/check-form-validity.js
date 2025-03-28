document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("resetForm");
    const emailInput = document.getElementById("email");
    const backupEmailInput = document.getElementById("backup_email");
    const submitButton = document.getElementById("submitButton");

    function validateEmail(input) {
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        const isValid = emailPattern.test(input.value);
        const errorText = input.nextElementSibling;

        if (isValid) {
            input.classList.remove("error");
            errorText.style.display = "none";
        } else {
            input.classList.add("error");
            errorText.style.display = "block";
        }

        return isValid;
    }

    function checkFormValidity() {
        const emailValid = validateEmail(emailInput);
        const backupEmailValid = validateEmail(backupEmailInput);

        submitButton.disabled = !(emailValid && backupEmailValid);
    }

    emailInput.addEventListener("input", checkFormValidity);
    backupEmailInput.addEventListener("input", checkFormValidity);
    document.addEventListener("change", checkFormValidity);
});