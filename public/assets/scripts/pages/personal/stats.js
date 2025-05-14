document.addEventListener("DOMContentLoaded", () => {
    setupDO();

    const chartCanvas = document.getElementById("statsCanvas");
    const dataJson = JSON.parse(chartCanvas.dataset.statsinfo);
    console.log(dataJson);
    
    new Chart(chartCanvas, {
        type: "bar",
        data: {
            labels: dataJson.map((i) => i.date.split(" ")[0]),
            datasets: [
                {
                    label: "Повторения",
                    tension: 0.1,
                    backgroundColor: "#6600cc",
                    data: dataJson.map((i) => i.wordsDone)
                },
                {
                    label: "Заработанные проценты",
                    tension: 0.1,
                    backgroundColor: "#fb7efd",
                    data: dataJson.map((i) => i.percentGained)
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: {
                        autoSkip: false
                    }
                }
            }
        }
    });
});

function setupDO() {
    const startDate = document.getElementById("startDateInput");
    const endDate = document.getElementById("endDateInput");
    
    const searchParams = new URLSearchParams(window.location.search);

    const startParam = searchParams.get("start");
    const endParam = searchParams.get("end");

    if (!isNaN(Date.parse(startParam))) {
        startDate.value = startParam;
    }
    if (!isNaN(Date.parse(endParam))) {
        endDate.value = endParam;
    }

    startDate.addEventListener("change", (e) => {
        endDate.setAttribute("min", e.target.value);
    });
    endDate.addEventListener("change", (e) => {
        startDate.setAttribute("max", e.target.value);
    });
}