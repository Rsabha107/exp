$(document).ready(function () {
    $("#find_ref_number").focus();

    $(".select2-statusxx").select2({
        templateResult: formatStatusOption,
        templateSelection: formatStatusOption,
        minimumResultsForSearch: -1, // hide search box
        width: "100%",
    });

    function formatStatusOption(option) {
        // console.log(option);
        const statusColorMap = {
            2: "warning",
            3: "primary",
            4: "success",
            5: "secondary",
        };
        if (!option.id) return option.text;

        const status = option.id;
        const label = option.text;
        const badgeClass =
            "badge badge-phoenix badge-phoenix-" +
            (statusColorMap[status] || "dark");

        return $(`<span class="${badgeClass}">${label}</span>`);
    }

    $(".select2-statusxx").on("change", function () {
        const select = $(this);
        const status = select.val();
        const profileId = select.data("id");
        const url = select.data("url");
        const $bookingDetails = $("#booking-details");

        const statusColorMap = {
            2: "warning",
            3: "primary",
            4: "success",
            5: "secondary",
        };
        console.log("Selected status:", status, "for profile ID:", profileId);
        console.log("Request URL:", url);

        $.ajax({
            url: url,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Replace with your method of getting the CSRF token
            },
            data: {
                profile_id: profileId,
                status: status,
            },
            success: function (response) {
                console.log("Status updated successfully:", response);
                console.log("Status color:", statusColorMap[status]);
                toastr.success(response.message || "Status updated!");
                $bookingDetails.removeClass(
                    "table-secondary table-warning table-success table-danger"
                );
                $bookingDetails.addClass("table-" + statusColorMap[status]);
            },
            error: function (xhr) {
                console.error("Error updating status:", xhr);
                toastr.error("Failed to update status");
            },
        });
    });

    $(".editable").on("blur", function () {
        let td = $(this);
        let value = td.text().trim();
        let id = td.data("id");
        let field = td.data("field");
        console.log("ID: " + id + ", Field: " + field + ", Value: " + value);
    });
});

$("#visitor_form").on("submit", function (event) {
    console.log("Form submitted");
    event.preventDefault();

    var refNumber = $("#find_ref_number").val();
    $("#cover-spin").show();
    console.log("inside #visitor_form: " + refNumber);
    var btn = $("#submitBtn");
    btn.prop("disabled", true);
    $.ajax({
        url: "/sps/venue-admin/visitor/mv/get/" + refNumber,
        method: "GET",
        async: true,
        success: function (response) {
            console.log("inside success");
            console.log(response.error);
            if (!response.error) {
                g_response = response.view;
                $("#find_ref_number").val("");
                $("#find_ref_number").focus();
                $("#visitor-stored-item-content").empty("").append(g_response);
                // $("#stored_item_detail_modal").modal("show");
                $("#cover-spin").hide();
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-center",
                    timeOut: "3000",
                };
                toastr.success(response.message);
                btn.prop("disabled", false);
            } else {
                console.log("inside else");
                $("#visitor-stored-item-content").empty("");
                $("#find_ref_number").focus();
                $("#find_ref_number").select();
                $("#cover-spin").hide();
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-center",
                    timeOut: "3000",
                };
                toastr.error(response.message);
                btn.prop("disabled", false);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
            $("#cover-spin").hide();
            btn.prop("disabled", false);
        },
    });
});

// load data for items modal
$("body").on("click", "#editStatus", function (event) {
    console.log("inside sec click edit: find.js");
    // event.preventDefault();
    var id = $(this).data("id");
    var table = $(this).data("table");
    // var route = $(this).data("route");
    // console.log("id: " + id);
    // console.log("table: " + table);

    $.get("/sps/venue-admin/item/status/edit/" + id, function (data) {
        // $.each(data, function (index, value) {
        console.log(data);
        $("#editId").val(data.id);
        $("#add_status_id").val(data.item_status_id);
        $("#statusTable").val(table);
        $("#statusModal").modal("show");
        // });

        // $('#staticBackdropLabel').html("Edit category");
        // $('#submit').val("Edit category");
    });
});
