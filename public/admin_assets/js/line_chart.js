document.addEventListener('DOMContentLoaded', function () {
    const options = {
        chart: {
            type: 'line',
            height: 300,
            toolbar: { show: false },
            fontFamily: 'inherit',
            foreColor: '#6c757d',
        },
        series: [
            {
                name: 'BITU', // Singkatan untuk di tooltip
                data: [50, 47, 49, 58, 70, 55, 60, 65, 75, 80, 90, 95],
            },
            {
                name: 'BIP',
                data: [30, 35, 38, 40, 43, 50, 55, 60, 65, 70, 75, 80],
            },
        ],
        xaxis: {
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'Mei',
                'Jun',
                'Jul',
                'Agu',
                'Sep',
                'Okt',
                'Nov',
                'Des',
            ],
            labels: { style: { fontSize: '13px' } },
            axisBorder: { color: '#e0e6ed' },
            axisTicks: { color: '#e0e6ed' },
        },
        yaxis: {
            min: 0,
            max: 100,
            tickAmount: 4,
            labels: { style: { fontSize: '13px' } },
        },
        grid: {
            borderColor: '#e0e6ed',
            strokeDashArray: 4,
        },
        colors: ['#1e4db7', '#0bb2fb'],
        stroke: {
            curve: 'smooth',
            width: 2,
        },
        markers: {
            size: 4,
            strokeWidth: 0,
            hover: { size: 6 },
        },
        tooltip: {
            theme: 'light',
        },
        legend: {
            show: false, // disembunyikan, karena kita pakai legend manual di HTML
        },
    };

    const chart = new ApexCharts(document.querySelector('#inspectionChart'), options);
    chart.render();

    // Download PNG
    document.getElementById('download-png').addEventListener('click', function () {
        chart.dataURI().then(({ imgURI }) => {
            const a = document.createElement('a');
            a.href = imgURI;
            a.download = 'statistik-inspeksi.png';
            a.click();
        });
    });
});
