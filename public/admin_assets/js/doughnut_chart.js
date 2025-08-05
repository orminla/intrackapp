document.addEventListener("DOMContentLoaded", function () {
    fetch("/admin/chart/distribution")
        .then((res) => res.json())
        .then((data) => {
            const dataSeries = data.series;
            const labels = data.labels;
            const totalPetugas = dataSeries.reduce((a, b) => a + b, 0);

            const chart = new ApexCharts(
                document.querySelector("#distributionPieChart"),
                {
                    chart: {
                        type: "donut",
                        height: 280,
                        fontFamily: "inherit",
                        foreColor: "#6c757d",
                        toolbar: { show: false },
                        events: {
                            dataPointSelection: function (event) {
                                event.stopPropagation();
                            },
                        },
                    },

                    series: dataSeries,
                    labels: labels,
                    colors: ["#1e4db7", "#fc4b6c", "#fb8c00"],
                    legend: {
                        position: "bottom",
                        fontSize: "13px",
                        labels: { colors: "#6c757d" },
                        markers: {
                            width: 10,
                            height: 10,
                            radius: 50,
                        },
                    },

                    dataLabels: {
                        enabled: true,
                        formatter: (val) => `${Math.round(val)}%`,
                        dropShadow: { enabled: false },
                        style: {
                            fontSize: "12px",
                            fontWeight: "bold",
                            position: "center",
                        },
                    },

                    tooltip: {
                        y: {
                            formatter: (val) => `${val} Petugas`,
                        },
                    },

                    plotOptions: {
                        pie: {
                            donut: {
                                size: "65%",
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: "30px",
                                        fontWeight: 700,
                                        color: "#4a4a4a",
                                        offsetY: 5,
                                        formatter: function () {
                                            return totalPetugas.toString();
                                        },
                                    },
                                    value: {
                                        show: true,
                                        fontSize: "16px",
                                        fontWeight: "thin",
                                        color: "#6c757d",
                                        offsetY: 5,
                                        formatter: () => "Petugas",
                                    },
                                    total: {
                                        show: true,
                                        showAlways: true,
                                        label: "Petugas",
                                        fontSize: "40px",
                                        color: "#6c757d",
                                        fontWeight: "bold",
                                        formatter: () => "Petugas",
                                    },
                                },
                            },
                        },
                    },
                    responsive: [
                        {
                            breakpoint: 480,
                            options: {
                                chart: { height: 250 },
                                legend: { position: "bottom" },
                            },
                        },
                    ],
                },
            );

            chart.render();
        });
});
