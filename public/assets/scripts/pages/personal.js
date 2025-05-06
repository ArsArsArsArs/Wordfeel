document.addEventListener("DOMContentLoaded", () => {
    setupSearch();
    setupLO();
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
    if (!langsSelect) {
        return;
    }

    langsSelect.addEventListener("change", (e) => {
        window.location.assign(`${window.location.origin}/personal/?langdict=${e.target.value}`);
    });
}
