const BASE_URL = (window.APP_URL || "http://localhost/heater-monitoring-system/public").replace(/\/$/, "");
const API_URL = `${BASE_URL}/api/v1/heaters`;
const CHART_URL = `${BASE_URL}/api/v1/heaters/chart-data`;
const ALERTS_URL = `${BASE_URL}/api/v1/heaters/alerts`;

let heaterData = [];
let currentChart = null;
let statusChart = null;

// Pagination state
let currentPage = 1;
const ITEMS_PER_PAGE = 5;

async function loadDashboard() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();
        heaterData = result.data;

        if (document.getElementById("total-heater")) updateSummary(heaterData);
        if (document.getElementById("heaterTable")) renderTablePage(currentPage);
        if (statusChart) updateStatusChart(heaterData);
        if (document.getElementById("monitored-machine")) updateFactoryFloorMap(heaterData);

        if (currentChart) fetchChartData();
        fetchAlertsData();

    } catch (err) {
        console.error(err);
    }
}

function updateSummary(data) {
    const total = data.length;
    let active = 0;
    let inactive = 0;
    let normal = 0;
    let warning = 0;
    let danger = 0;
    let lastUpdateTime = "-";
    let lastUpdateDate = "-";

    data.forEach(h => {
        if (h.is_active) active++;
        else inactive++;

        if (h.latest_log) {
            if (h.latest_log.status === "NORMAL") normal++;
            else if (h.latest_log.status === "WARNING") warning++;
            else if (h.latest_log.status === "DANGER") danger++;

            if (lastUpdateTime === "-") {
                const dateObj = new Date(h.latest_log.received_at);
                const optionsDate = { day: '2-digit', month: 'long', year: 'numeric' };
                lastUpdateDate = dateObj.toLocaleDateString('id-ID', optionsDate);
                lastUpdateTime = dateObj.toTimeString().split(' ')[0];
            }
        }
    });

    const elTotal = document.getElementById("total-heater");
    if (elTotal) elTotal.innerText = total;

    const elActive = document.getElementById("heater-active");
    if (elActive) elActive.innerText = active;

    const elInactive = document.getElementById("heater-inactive");
    if (elInactive) elInactive.innerText = inactive;

    const elNormal = document.getElementById("normal-count");
    if (elNormal) elNormal.innerText = normal;

    const elNormalPct = document.getElementById("normal-pct");
    if (elNormalPct) elNormalPct.innerText = total > 0 ? ((normal / total) * 100).toFixed(2) + "%" : "0%";

    const elWarning = document.getElementById("warning-count");
    if (elWarning) elWarning.innerText = warning;

    const elWarningPct = document.getElementById("warning-pct");
    if (elWarningPct) elWarningPct.innerText = total > 0 ? ((warning / total) * 100).toFixed(2) + "%" : "0%";

    const elDanger = document.getElementById("danger-count");
    if (elDanger) elDanger.innerText = danger;

    const elDangerPct = document.getElementById("danger-pct");
    if (elDangerPct) elDangerPct.innerText = total > 0 ? ((danger / total) * 100).toFixed(2) + "%" : "0%";

    const elTime = document.getElementById("last-update-time");
    if (elTime) elTime.innerText = lastUpdateTime;

    const elDate = document.getElementById("last-update-date");
    if (elDate) elDate.innerText = lastUpdateDate;
}

// =====================
// PAGINATION FUNCTIONS
// =====================

function renderTablePage(page) {
    const tbody = document.getElementById("heaterTable");
    if (!tbody) return;

    const data = heaterData;
    const total = data.length;
    const totalPages = Math.ceil(total / ITEMS_PER_PAGE);

    if (page < 1) page = 1;
    if (page > totalPages && totalPages > 0) page = totalPages;
    currentPage = page;

    const start = (currentPage - 1) * ITEMS_PER_PAGE;
    const end = Math.min(start + ITEMS_PER_PAGE, total);
    const pageData = data.slice(start, end);

    tbody.innerHTML = "";

    if (total === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="py-5 text-muted">Tidak ada data heater.</td></tr>`;
    } else {
        pageData.forEach((item, idx) => {
            const globalIndex = start + idx;
            const log = item.latest_log;
            let badge = "secondary";
            let timeStr = "-";

            if (log) {
                if (log.status === "NORMAL") badge = "success";
                else if (log.status === "WARNING") badge = "warning";
                else if (log.status === "DANGER") badge = "danger";

                const dateObj = new Date(log.received_at);
                const d = ("0" + dateObj.getDate()).slice(-2);
                const m = ("0" + (dateObj.getMonth() + 1)).slice(-2);
                const y = dateObj.getFullYear();
                const time = dateObj.toTimeString().split(' ')[0];
                timeStr = `${d}-${m}-${y} ${time}`;
            }

            tbody.innerHTML += `
            <tr>
                <td class="py-2">${globalIndex + 1}</td>
                <td class="py-2 font-weight-bold">${item.heater_code}</td>
                <td class="py-2">${item.heater_name}</td>
                <td class="py-2">${item.zone || "-"}</td>
                <td class="py-2">${log ? log.current : "-"}</td>
                <td class="py-2">
                    <span class="badge badge-${badge}">${log ? log.status : "-"}</span>
                </td>
                <td class="py-2">${timeStr}</td>
            </tr>`;
        });
    }

    const infoEl = document.getElementById("pagination-info");
    if (infoEl) {
        infoEl.innerText = total > 0
            ? `Menampilkan ${start + 1} - ${end} dari ${total} data`
            : "Tidak ada data";
    }

    renderPagination(currentPage, totalPages);
}

function renderPagination(page, totalPages) {
    const container = document.getElementById("pagination-container");
    if (!container) return;

    container.innerHTML = "";

    const prevLi = document.createElement("li");
    prevLi.className = `page-item ${page === 1 ? "disabled" : ""}`;
    prevLi.innerHTML = `<a class="page-link rounded-circle mx-1" href="#" id="prev-page" style="cursor:pointer"><i class="fas fa-angle-left"></i></a>`;
    container.appendChild(prevLi);

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement("li");
        li.className = `page-item ${i === page ? "active" : ""}`;
        li.innerHTML = `<a class="page-link rounded-circle mx-1" href="#" data-page="${i}" style="cursor:pointer">${i}</a>`;
        container.appendChild(li);
    }

    const nextLi = document.createElement("li");
    nextLi.className = `page-item ${page === totalPages || totalPages === 0 ? "disabled" : ""}`;
    nextLi.innerHTML = `<a class="page-link rounded-circle mx-1" href="#" id="next-page" style="cursor:pointer"><i class="fas fa-angle-right"></i></a>`;
    container.appendChild(nextLi);

    container.querySelectorAll("[data-page]").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            renderTablePage(parseInt(this.getAttribute("data-page")));
        });
    });

    const prevBtn = container.querySelector("#prev-page");
    if (prevBtn) {
        prevBtn.addEventListener("click", function(e) {
            e.preventDefault();
            if (currentPage > 1) renderTablePage(currentPage - 1);
        });
    }

    const nextBtn = container.querySelector("#next-page");
    if (nextBtn) {
        nextBtn.addEventListener("click", function(e) {
            e.preventDefault();
            if (currentPage < totalPages) renderTablePage(currentPage + 1);
        });
    }
}

// =====================
// CHARTS
// =====================

function initCharts() {
    const elCtx1 = document.getElementById('currentChart');
    if (elCtx1) {
        const ctx1 = elCtx1.getContext('2d');
        currentChart = new Chart(ctx1, {
            type: 'line',
            data: { labels: [], datasets: [] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                elements: { 
                    point: { radius: 2, hoverRadius: 5 },
                    line: { tension: 0.3 }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: 12,
                        title: { display: true, text: 'Arus Listrik (Ampere)' }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 15, font: { family: 'Poppins', size: 12 } }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    }

    const elCtx2 = document.getElementById('statusChart');
    if (elCtx2) {
        const ctx2 = elCtx2.getContext('2d');
        statusChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Normal', 'Warning', 'Danger'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: { legend: { display: false } }
            }
        });
    }
}

let currentRange = 'realtime';
let currentShift = 'shift1';
let currentMonth = new Date().getMonth() + 1;

async function fetchChartData() {
    if (!currentChart) return;
    try {
        const url = `${CHART_URL}?range=${currentRange}&shift=${currentShift}&month=${currentMonth}`;
        const response = await fetch(url);
        const result = await response.json();

        const colors = [
            '#0284c7', '#16a34a', '#eab308', '#9333ea', '#dc2626',
            '#0891b2', '#f97316', '#db2777', '#0d9488', '#4b5563',
            '#1e293b', '#2563eb'
        ];

        const datasets = result.data.map((h, i) => {
            const hexColor = colors[i % colors.length];
            return {
                label: h.label,
                data: h.data ? h.data.map(d => d.current) : [],
                borderColor: hexColor,
                backgroundColor: hexColor,
                borderWidth: 2.5,
                pointRadius: 3,
                pointHoverRadius: 6,
                fill: false,
                spanGaps: true
            };
        });

        let labels = [];
        if (result.data && result.data.length > 0) {
            const firstWithData = result.data.find(h => h.data && h.data.length > 0);
            if (firstWithData) {
                labels = firstWithData.data.map(d => d.time);
            }
        }

        currentChart.data.labels = labels;
        currentChart.data.datasets = datasets;
        currentChart.update();
    } catch (err) {
        console.error(err);
    }
}

function updateStatusChart(data) {
    if (!statusChart) return;
    let normal = 0, warning = 0, danger = 0;
    const total = data.length;

    data.forEach(h => {
        if (h.latest_log) {
            if (h.latest_log.status === "NORMAL") normal++;
            else if (h.latest_log.status === "WARNING") warning++;
            else if (h.latest_log.status === "DANGER") danger++;
        }
    });

    statusChart.data.datasets[0].data = [normal, warning, danger];
    statusChart.update();

    const nPct = total > 0 ? ((normal / total) * 100).toFixed(2) : 0;
    const wPct = total > 0 ? ((warning / total) * 100).toFixed(2) : 0;
    const dPct = total > 0 ? ((danger / total) * 100).toFixed(2) : 0;

    const elLegend = document.getElementById("statusLegend");
    if (elLegend) {
        elLegend.innerHTML = `
            <div class="d-flex justify-content-between mb-2">
                <div><i class="fas fa-square text-success mr-1"></i> <strong>NORMAL</strong></div>
                <div class="text-muted">${normal} (${nPct}%)</div>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <div><i class="fas fa-square text-warning mr-1"></i> <strong>WARNING</strong></div>
                <div class="text-muted">${warning} (${wPct}%)</div>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <div><i class="fas fa-square text-danger mr-1"></i> <strong>DANGER</strong></div>
                <div class="text-muted">${danger} (${dPct}%)</div>
            </div>
        `;
    }
}

async function fetchAlertsData() {
    try {
        const response = await fetch(ALERTS_URL);
        const result = await response.json();

        if (typeof updateNavbarNotificationBadge === 'function') {
            updateNavbarNotificationBadge();
        }

        const list = document.getElementById("recentAlertsList");
        if (!list) return;
        
        list.innerHTML = "";

        if (!result.data || result.data.length === 0) {
            list.innerHTML = `<li class="list-group-item text-center text-muted py-4 border-0">Tidak ada alert saat ini</li>`;
            return;
        }

        result.data.forEach(alert => {
            const isDanger = alert.status === "DANGER";
            const iconColor = isDanger ? "text-danger" : "text-warning";
            const iconBg = isDanger ? "rgba(220,53,69,0.1)" : "rgba(255,193,7,0.1)";

            list.innerHTML += `
            <li class="list-group-item px-3 py-3 border-0 d-flex align-items-start">
                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 mt-1" style="width: 32px; height: 32px; background-color: ${iconBg}; flex-shrink:0;">
                    <i class="fas fa-exclamation ${iconColor}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong class="mb-1" style="font-size: 13px;">${alert.heater_code} - <span class="${iconColor}">${alert.status}</span></strong>
                        <small class="text-muted ml-2" style="white-space:nowrap;">${alert.time}</small>
                    </div>
                    <div class="text-muted" style="font-size: 12px;">
                        Arus: ${alert.current} A
                    </div>
                </div>
            </li>`;
        });
    } catch (err) {
        console.error(err);
    }
}

// =====================
// INIT
// =====================

document.addEventListener("DOMContentLoaded", () => {
    initCharts();
    loadDashboard();

    const btnRefresh = document.getElementById("btn-refresh");
    if (btnRefresh) {
        btnRefresh.addEventListener("click", () => {
            loadDashboard();
        });
    }

    // Interactive Popover for Monitored Machine
    const monitoredMachine = document.getElementById("monitored-machine");
    const popover = document.getElementById("machine-details-popover");

    if (monitoredMachine && popover) {
        monitoredMachine.addEventListener("mouseenter", (e) => {
            popover.classList.add("show");
            positionPopover(e);
        });

        monitoredMachine.addEventListener("mousemove", (e) => {
            positionPopover(e);
        });

        monitoredMachine.addEventListener("mouseleave", () => {
            popover.classList.remove("show");
        });

        monitoredMachine.addEventListener("click", (e) => {
            e.stopPropagation();
            popover.classList.toggle("show");
            positionPopover(e);
        });

        document.addEventListener("click", () => {
            popover.classList.remove("show");
        });
    }

    function positionPopover(e) {
        const container = document.querySelector(".factory-floor-container");
        if (!container) return;

        const containerRect = container.getBoundingClientRect();
        
        let x = e.clientX - containerRect.left + container.scrollLeft;
        let y = e.clientY - containerRect.top + container.scrollTop;

        x += 15;
        y += 15;

        const popoverWidth = popover.offsetWidth || 290;
        const popoverHeight = popover.offsetHeight || 200;

        if (x + popoverWidth > container.scrollWidth) {
            x = e.clientX - containerRect.left + container.scrollLeft - popoverWidth - 15;
        }

        if (y + popoverHeight > container.scrollHeight) {
            y = e.clientY - containerRect.top + container.scrollTop - popoverHeight - 15;
        }

        popover.style.left = `${x}px`;
        popover.style.top = `${y}px`;
    }

    // Filter controls
    const mainFilterEl = document.getElementById('mainFilter');
    const shiftSubFilterEl = document.getElementById('shiftSubFilter');
    const monthSubFilterEl = document.getElementById('monthSubFilter');

    if (monthSubFilterEl) {
        monthSubFilterEl.value = currentMonth;
    }

    if (mainFilterEl) {
        mainFilterEl.addEventListener('change', function() {
            currentRange = this.value;
            if (currentRange === 'shift') {
                if (shiftSubFilterEl) shiftSubFilterEl.classList.remove('d-none');
                if (monthSubFilterEl) monthSubFilterEl.classList.add('d-none');
            } else if (currentRange === 'monthly') {
                if (shiftSubFilterEl) shiftSubFilterEl.classList.add('d-none');
                if (monthSubFilterEl) monthSubFilterEl.classList.remove('d-none');
            } else {
                if (shiftSubFilterEl) shiftSubFilterEl.classList.add('d-none');
                if (monthSubFilterEl) monthSubFilterEl.classList.add('d-none');
            }
            fetchChartData();
        });
    }

    if (shiftSubFilterEl) {
        shiftSubFilterEl.addEventListener('change', function() {
            currentShift = this.value;
            fetchChartData();
        });
    }

    if (monthSubFilterEl) {
        monthSubFilterEl.addEventListener('change', function() {
            currentMonth = this.value;
            fetchChartData();
        });
    }

    setInterval(loadDashboard, 5000);
});

// =================================================================
// FACTORY FLOOR LAYOUT MAP LOGIC
// =================================================================
function updateFactoryFloorMap(data) {
    const machineNode = document.getElementById("monitored-machine");
    const statusText = document.getElementById("mach-status-text");
    const popoverOverall = document.getElementById("popover-overall-status");
    const sensorContainer = document.getElementById("popover-sensor-container");
    const popoverLastUpdate = document.getElementById("popover-last-update");

    if (!machineNode) return;

    let overallStatus = "OFFLINE";
    let sensorsHtml = "";
    let dangerCount = 0;
    let warningCount = 0;
    let offlineCount = 0;
    let normalCount = 0;
    let latestTime = "-";

    data.forEach(h => {
        const log = h.latest_log;
        const status = log ? log.status : "OFFLINE";
        const current = log ? parseFloat(log.current) : 0.00;
        
        if (status === "DANGER") dangerCount++;
        else if (status === "WARNING") warningCount++;
        else if (status === "OFFLINE") offlineCount++;
        else if (status === "NORMAL") normalCount++;

        if (log && log.received_at) {
            const dateObj = new Date(log.received_at);
            latestTime = dateObj.toTimeString().split(' ')[0];
        }

        let itemClass = "";
        let badgeClass = "badge-secondary";
        if (status === "NORMAL") {
            badgeClass = "badge-success";
        } else if (status === "WARNING") {
            itemClass = "warning";
            badgeClass = "badge-warning text-dark";
        } else if (status === "DANGER") {
            itemClass = "danger";
            badgeClass = "badge-danger";
        } else if (status === "OFFLINE") {
            itemClass = "offline";
            badgeClass = "badge-secondary";
        }

        sensorsHtml += `
        <div class="popover-sensor-item ${itemClass}">
            <span class="font-weight-bold">${h.heater_code}</span>
            <span class="badge ${badgeClass}" style="font-size: 9px;">${current.toFixed(2)} A</span>
        </div>`;
    });

    if (dangerCount > 0) {
        overallStatus = "DANGER";
    } else if (warningCount > 0) {
        overallStatus = "WARNING";
    } else if (normalCount > 0) {
        overallStatus = "NORMAL";
    } else {
        overallStatus = "OFFLINE";
    }

    // Reset glow classes
    machineNode.classList.remove("glow-pulsing-green", "glow-pulsing-yellow", "glow-pulsing-red", "glow-pulsing-grey");

    let popoverBadgeClass = "badge-secondary";
    if (overallStatus === "NORMAL") {
        machineNode.classList.add("glow-pulsing-green");
        popoverBadgeClass = "badge-success";
    } else if (overallStatus === "WARNING") {
        machineNode.classList.add("glow-pulsing-yellow");
        popoverBadgeClass = "badge-warning text-dark";
    } else if (overallStatus === "DANGER") {
        machineNode.classList.add("glow-pulsing-red");
        popoverBadgeClass = "badge-danger";
    } else {
        machineNode.classList.add("glow-pulsing-grey");
    }

    if (statusText) statusText.innerText = overallStatus;
    if (popoverOverall) {
        popoverOverall.innerText = overallStatus;
        popoverOverall.className = `badge ${popoverBadgeClass}`;
    }
    if (sensorContainer) sensorContainer.innerHTML = sensorsHtml;
    if (popoverLastUpdate) popoverLastUpdate.innerText = latestTime;
}