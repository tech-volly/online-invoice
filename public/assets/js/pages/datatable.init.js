/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************************!*\
  !*** ./resources/js/pages/datatable.init.js ***!
  \**********************************************/
// Datatable
// if ($('#clientDataTable').length > 0) {
//   $('#clientDataTable').DataTable({
//     "bFilter": false,
//     "aoColumnDefs": [
//         { "bSortable": false, "aTargets": [ 8 ] }
//     ],
//     "bFilter": true,
//     "buttons": [
//       'excel',
//     ]
//   });
// }

// if ($('#usersDataTable').length > 0) {
//   $('#usersDataTable').DataTable({
//     "bFilter": false,
//     "aoColumnDefs": [
//         { "bSortable": false, "aTargets": [ 5 ] }
//     ],
//     "bFilter": true,
//   });
// }

if ($('#serviceDataTable').length > 0) {
  $('#serviceDataTable').DataTable({
    "bFilter": false,
    "aoColumnDefs": [
        { "bSortable": false, "aTargets": [ 5 ] }
    ],
    "bFilter": true,
  });
}

// if($('#categoryDataTable').length > 0) {
//   $('#categoryDataTable').DataTable({
//     "bFilter": false,
//     "aoColumnDefs": [
//         { "bSortable": false, "aTargets": [ 3 ] }
//     ],
//     "bFilter": true,
//   });
// }


/******/ })()
;