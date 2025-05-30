document.addEventListener("DOMContentLoaded", () => {
    setupCaptchaChecker();
});

function setupCaptchaChecker() {
  ['loginForm','signupForm'].forEach(id => {
    const form = document.getElementById(id)
    form.addEventListener('submit', e => {
      const tokenField = form.querySelector('textarea[name="h-captcha-response"], input[name="h-captcha-response"]')
      const val = tokenField ? tokenField.value.trim() : ''
      if (!val) {
        e.preventDefault()
        alert("Пожалуйста, пройдите проверку (каптчу)…")
      }
    })
  })
}