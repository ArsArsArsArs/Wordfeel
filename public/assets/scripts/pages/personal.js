document.addEventListener("DOMContentLoaded", () => {
    setupSearch();
    setupLO();
    setupTO();
    setupDelete();
});

function setupSearch() {
    const languageSearch = document.getElementById("languagesSearch");
    if (!languageSearch) {
        return;
    }
    const allLangsContainer = document.querySelector("#allLangsContainer");
    const allLangs = allLangsContainer.querySelectorAll("div.language-choice");
    
    const languageData = Array.from(allLangs).map(div => ({
        element: div,
        lowerName: div.querySelector("a").innerText.toLowerCase()
    }));
    
    const noResultsMsg = document.createElement("p");
    noResultsMsg.className = "no-results-message";
    noResultsMsg.textContent = "Языки не найдены";
    noResultsMsg.style.padding = "10px";
    
    function debounce(func, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    }
    
    const filterLanguages = (searchTerm) => {
        searchTerm = searchTerm.toLowerCase().trim();
        let visibleCount = 0;
        
        const existingMsg = allLangsContainer.querySelector(".no-results-message");
        if (existingMsg) {
            allLangsContainer.removeChild(existingMsg);
        }
        
        languageData.forEach(({ element, lowerName }) => {
            if (searchTerm === "" || lowerName.includes(searchTerm)) {
                element.classList.remove("invisible");
                visibleCount++;
            } else {
                element.classList.add("invisible");
            }
        });
        
        if (visibleCount === 0 && searchTerm !== "") {
            allLangsContainer.appendChild(noResultsMsg);
        }
    };
    
    const debouncedFilter = debounce(filterLanguages, 300);
    
    languageSearch.addEventListener("input", (e) => {
        debouncedFilter(e.target.value);
    });
}

function setupLO() {
    const langsSelect = document.getElementById("lo_langselect");
    if (!langsSelect) return;

    langsSelect.addEventListener("change", (e) => {
        if (e.target.dataset.for) {
            window.location.assign(`${window.location.origin}/personal/?langdict=${e.target.value}&for=${e.target.dataset.for}`);
        } else {
            window.location.assign(`${window.location.origin}/personal/?langdict=${e.target.value}`);
        }
    });
}

function setupTO() {
    const langsSelect = document.getElementById("lo_langselect");
    if (!langsSelect) return;

    const tagsSelect = document.getElementById("lo_tagselect");
    if (!tagsSelect) return;

    tagsSelect.addEventListener("change", (e) => {
        if (e.target.dataset.for) {
            window.location.assign(`${window.location.origin}/personal/?langdict=${langsSelect.value}&tag=${e.target.value}&for=${e.target.dataset.for}`);
        } else {
            window.location.assign(`${window.location.origin}/personal/?langdict=${langsSelect.value}&tag=${e.target.value}`);
        }
    });
}

function setupDelete() {
    const deleteButtons = document.querySelectorAll(".delete-word-button");
    if (deleteButtons.length === 0) return;

    deleteButtons.forEach(deleteButton => {
        deleteButton.addEventListener("click", (e) => {
            const ok = confirm(`Удалить слово ${deleteButton.dataset.word}?`);
            if (!ok) return;

            deleteButton.classList.add("button-activated");

            fetch("/personal/delete", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `user_for=${deleteButton.dataset.username}&wordID=${deleteButton.dataset.wordid}`
            }).then((res) => {
                if (res.status != 200) {
                    deleteButton.classList.remove("button-activated");
                    return;
                }

                const trWord = document.getElementById(`word${deleteButton.dataset.wordid}`);
                if (!trWord) return;

                trWord.classList.add("invisible");
            });
        });
    });
}
