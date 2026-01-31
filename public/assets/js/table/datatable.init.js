/*
Template Name: Fabkin - Admin & Dashboard Template
Author: Pixeleyez
Website: https://pixeleyez.com/
Contact: pixeleyez@gmail.com
File: DataTable init js
*/
"use strict";

// Default DataTable configuration for Bootstrap 5
var defaults = {
    language: {
        info: "Showing _START_ to _END_ of _TOTAL_ records",
        infoEmpty: "Showing no records",
        lengthMenu: "_MENU_",
        paginate: {
            first: '<i class="ri-arrow-left-double-line"></i>',
            last: '<i class="ri-arrow-right-double-line"></i>',
            next: '<i class="ri-arrow-right-s-line"></i>',
            previous: '<i class="ri-arrow-left-s-line"></i>',
        },
    },
};

// Extend jQuery DataTable defaults
$.extend(true, $.fn.dataTable.defaults, defaults);

// DataTable initialization
(function (factory) {
    if (typeof define === "function" && define.amd) {
        define(["jquery", "datatables.net"], factory);
    } else {
        factory(jQuery);
    }
})(function ($) {
    var DataTable = $.fn.dataTable;

    // Set DataTable defaults
    $.extend(true, DataTable.defaults, {
        pagingType: "simple_numbers",
        dom: "<'table-responsive'tr><'d-flex gap-3 justify-content-center justify-content-md-between flex-wrap'l p>",
        renderer: "bootstrap",
    });

    // Default classes for Bootstrap 5
    $.extend(DataTable.ext.classes, {
        sWrapper: "dataTables_wrapper dt-bootstrap5",
        sFilterInput: "form-control",
        sLengthSelect: "form-select",
        sPageButton: "paginate_button page-item",
    });

    return DataTable;
});

// Add Bootstrap classes to filter input
$(document).on("init.dt", function (e, settings) {
    $(settings.nTableWrapper)
        .find(".dt-buttons button.dt-button")
        .addClass("btn btn-primary"); // Style input
    $(settings.nTableWrapper)
        .find(".dataTables_filter input")
        .addClass("form-control");
});

// ------------------------------------------------

$(document).ready(function () {
    $("#default_datatable").DataTable({
        searching: true, // Aktifkan fitur search
    });
});

$(function () {
    if (
        $("#scroll-vertical").length &&
        !$.fn.dataTable.isDataTable("#scroll-vertical")
    ) {
        $("#scroll-vertical").DataTable({
            scrollY: "210px",
            scrollCollapse: true,
            paging: false,
        });
    }

    if (
        $("#scroll-horizontal").length &&
        !$.fn.dataTable.isDataTable("#scroll-horizontal")
    ) {
        $("#scroll-horizontal").DataTable({
            scrollX: true,
        });
    }

    if (
        $("#alternative-pagination").length &&
        !$.fn.dataTable.isDataTable("#alternative-pagination")
    ) {
        // Some pages use a custom server-side DataTables init.
        // Opt-out by adding data-dt-server="1" on the table element.
        if ($("#alternative-pagination").attr("data-dt-server") === "1") {
            return;
        }
        $("#alternative-pagination").DataTable({
            pagingType: "full_numbers",
            searching: true,
            dom:
                "<'row'<'col-sm-12 col-md-6 mb-3'l><'col-sm-12 col-md-6 mt-5'f>>" +
                "<'table-responsive'tr>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                search: "Search : ",
                searchPlaceholder: "Type to filter...",
            },
        });
    }
});

//buttons exmples
document.addEventListener("DOMContentLoaded", function () {
    if (
        $("#buttons-datatables").length &&
        !$.fn.dataTable.isDataTable("#buttons-datatables")
    ) {
        $("#buttons-datatables").DataTable({
            dom: "Bfrtip",
            buttons: ["copy", "csv", "excel", "print", "pdf"],
        });
    }
});
