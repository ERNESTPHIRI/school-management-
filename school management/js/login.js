// script.js (Optional)

document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    const loginErrorDiv = document.getElementById("loginError");

    form.addEventListener("submit", function(event) {
        const loginName = document.getElementById("LoginName").value;
        const password = document.getElementById("Password").value;

        if (loginName.trim() === "" || password.trim() === "") {
            loginErrorDiv.textContent = "Please fill in all fields.";
            event.preventDefault();
        } else {
            loginErrorDiv.textContent = ""; // Clear previous error messages
        }
    });
});
