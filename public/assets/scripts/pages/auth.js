document.addEventListener("DOMContentLoaded", () => {
    setupCaptchaChecker();
});

function setupCaptchaChecker() {
    document.getElementById("loginForm").addEventListener("submit", (e) => {
        var hcaptchaResponse = document.querySelector('[name=h-captcha-response]').value;
        
        if (hcaptchaResponse === "") {
            e.preventDefault();
            alert("Пожалуйста, пройдите проверку (каптчу). Если её не видно, стоит перезагрузить страницу");
        }
    });

    document.getElementById("signupForm").addEventListener("submit", (e) => {
        var hcaptchaResponse = document.querySelector('[name=h-captcha-response]').value;
        
        if (hcaptchaResponse === "") {
            e.preventDefault();
            alert("Пожалуйста, пройдите проверку (каптчу). Если её не видно, стоит перезагрузить страницу");
        }
    });
}