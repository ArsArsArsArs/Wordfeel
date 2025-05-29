document.addEventListener("DOMContentLoaded", () => {
    setupAddButton();
    setupCopying();
});

function setupAddButton() {
    const packageWords = document.getElementById("packageWords");
    const template = document.getElementById("wordTemplate").content;

    document.getElementById("addWord").addEventListener("click", () => {
        const clone = document.importNode(template, true);
        packageWords.appendChild(clone);
        refreshLegends();
    });

    packageWords.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-word-button")) {
            e.target.closest(".package-word").remove();
            refreshLegends();
        }
    });

    function refreshLegends() {
        packageWords.querySelectorAll(".package-word legend").forEach((lg, i) => lg.textContent = `№ ${i + 1}`);
    }
}

function setupCopying() {
    document.getElementById("copyWp").addEventListener("click", async (e) => {
        let link = e.target.dataset.link;

        await window.navigator.clipboard.writeText(link);
        alert("Ссылка скопирована");
    });
}