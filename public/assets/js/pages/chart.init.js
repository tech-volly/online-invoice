/******/ (() => { // webpackBootstrap
    var __webpack_exports__ = {};
    /*!******************************************!*\
     !*** ./resources/js/pages/chart.init.js ***!
     \******************************************/
    $(document).ready(function () {
        // Bar Chart
        Morris.Bar({
            element: 'bar-charts',
            data: [{
                    y: 'Jul',
                    a: 100,
                    b: 90
                }, {
                    y: 'Aug',
                    a: 75,
                    b: 65
                }, {
                    y: 'Sep',
                    a: 50,
                    b: 40
                }, {
                    y: 'Oct',
                    a: 75,
                    b: 65
                }, {
                    y: 'Nov',
                    a: 50,
                    b: 40
                }, {
                    y: 'Dec',
                    a: 75,
                    b: 65
                }, {
                    y: 'Jan',
                    a: 75,
                    b: 65
                }, {
                    y: 'Feb',
                    a: 75,
                    b: 65
                }, {
                    y: 'Mar',
                    a: 75,
                    b: 65
                }, {
                    y: 'Apr',
                    a: 75,
                    b: 65
                }, {
                    y: 'May',
                    a: 75,
                    b: 65
                }, {
                    y: 'Jun',
                    a: 100,
                    b: 90
                }],
            xkey: 'y',
            ykeys: ['a', 'b'],
            labels: ['Total Income', 'Total Outcome'],
            lineColors: ['#667eea', '#764ba2'],
            lineWidth: '3px',
            barColors: ['#667eea', '#764ba2'],
            resize: true,
            redraw: true
        }); // Line Chart
    });
})();