document.addEventListener("DOMContentLoaded", () => {
    // setupCaptchaChecker();
    setupCopying();
});

function setupCaptchaChecker() {
    document.getElementById("createForm").addEventListener("submit", (e) => {
        let hcaptchaResponse = document.querySelector('[name=h-captcha-response]').value;
        
        if (hcaptchaResponse === "") {
            e.preventDefault();
            alert("Пожалуйста, пройдите проверку (каптчу). Если её не видно, стоит перезагрузить страницу");
        }
    });
}

function setupCopying() {
    document.getElementById("copyMuc").addEventListener("click", async (e) => {
        let link = e.target.dataset.link;

        await window.navigator.clipboard.writeText(link);
        alert("Ссылка скопирована. Отправьте её ученику");
    });
}