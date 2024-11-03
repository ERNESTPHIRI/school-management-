// script.js
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    const errorDiv = document.querySelector(".error");

    form.addEventListener("submit", function(event) {
        const password = document.getElementById("Password").value;
        
        if (password.length < 6) {
            // Show the error under the password input
            errorDiv.textContent = "Password must be at least 6 characters long.";
            event.preventDefault();
        }
    });
});
