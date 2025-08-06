document.addEventListener("DOMContentLoaded", function () {
    fetch("/admin/chart/inspection")
        .then((res) => res.json())
        .then((data) => {
            const options = {
                chart: {
                    type: "line",
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: "inherit",
                    foreColor: "#6c757d",
                },
                series: [
                    {
                        name: "BITU",
                        data: data.bitu,
                    },
                    {
                        name: "BIP",
                        data: data.bip,
                    },
                ],
                xaxis: {
                    categories: data.labels.map((tgl) => {
                        const date = new Date(tgl);
                        const formatter = new Intl.DateTimeFormat("id-ID", {
                            day: "2-digit",
                            month: "short",
                        });
                        return formatter.format(date); // hasil: "06 Agt"
                    }),
                    labels: { style: { fontSize: "13px" } },
                    axisBorder: { color: "#e0e6ed" },
                    axisTicks: { color: "#e0e6ed" },
                },
                yaxis: {
                    min: 0,
                    max: 100,
                    tickAmount: 4,
                    labels: { style: { fontSize: "13px" } },
                },
                grid: {
                    borderColor: "#e0e6ed",
                    strokeDashArray: 4,
                },
                colors: ["#1e4db7", "#0bb2fb"],
                stroke: {
                    curve: "smooth",
                    width: 2,
                },
                markers: {
                    size: 4,
                    strokeWidth: 0,
                    hover: { size: 6 },
                },
                tooltip: {
                    theme: "light",
                },
                legend: {
                    show: false,
                },
            };

            const chart = new ApexCharts(
                document.querySelector("#inspectionChart"),
                options,
            );
            chart.render();

            document
                .getElementById("download-png")
                .addEventListener("click", function () {
                    chart.dataURI().then(({ imgURI }) => {
                        const a = document.createElement("a");
                        a.href = imgURI;
                        a.download = "statistik-inspeksi.png";
                        a.click();
                    });
                });
        });
});
