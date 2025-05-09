document.addEventListener("DOMContentLoaded", () => {
    setupTS();
});

function setupTS() {
    const tagsSelect = document.getElementById("tagselect");
    if (!tagsSelect) return;

    const urlParams = new URLSearchParams(window.location.search);

    tagsSelect.addEventListener("change", (e) => {
        window.location.assign(`${window.location.origin}/personal/train?langdict=${urlParams.get("langdict")}&tag=${e.target.value}`);
    });
}