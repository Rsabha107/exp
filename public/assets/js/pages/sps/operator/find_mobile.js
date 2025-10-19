$(document).ready(function () {

    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: "toast-top-center",
        preventDuplicates: false,
        hideDuration: "300",
    };

    $(".select2-status").select2({
        templateResult: formatStatusOption,
        templateSelection: formatStatusOption,
        minimumResultsForSearch: -1, // hide search box
        width: "100%",
    });

    $(".select2-profile-status").select2({
        templateResult: formatProfileStatusOption,
        templateSelection: formatProfileStatusOption,
        minimumResultsForSearch: -1, // hide search box
        width: "100%",
        containerCssClass: "select2-profile-status-container",
        dropdownCssClass: "select2-profile-status-dropdown",
    });

    function formatProfileStatusOption(option) {
        // console.log(option);
        const statusColorMap = {
            2: "warning",
            3: "primary",
            4: "success",
            5: "secondary",
            6: "danger",
        };
        if (!option.id) return option.text;

        const status = option.id;
        const label = option.text;
        const badgeClass =
            "badge badge-phoenix badge-phoenix-" +
            (statusColorMap[status] || "dark");

        return $(`<span class="${badgeClass}">${label}</span>`);
    }

    function formatStatusOption(option) {
        // console.log(option);
        const statusColorMap = {
            2: "warning",
            3: "primary",
            4: "success",
        };
        if (!option.id) return option.text;

        const status = option.id;
        const label = option.text;
        const badgeClass =
            "badge badge-phoenix badge-phoenix-" +
            (statusColorMap[status] || "dark");

        return $(`<span class="${badgeClass}">${label}</span>`);
    }

    $(".select2-status").on("change", function () {
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
        // console.log("Selected status:", status, "for profile ID:", profileId);
        // console.log("Request URL:", url);

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
                if (response.error) {
                    console.error("Error updating status:", response.message);
                    toastr.error(response.message || "Failed to update status");
                    return;
                }
                // console.log("Status updated successfully:", response);
                // console.log("Status color:", statusColorMap[status]);
                profileStatusHtml = `<span
                                    class="badge-display badge-phoenix fs--2 align-middle white-space-wrap ms-1 badge-phoenix-${
                                        response.profile_status_color
                                    }">
                                    <span class="badge-label">
                                        ${response.profile_status_title}
                                    </span>
                                </span>`;
                $(".profile-status-cell").empty("").append(profileStatusHtml);

                toastr.success(
                    response.message + " " + (response.profile_status_id || ""),
                    "Status updated!"
                );
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

    $(".editable").on("focus", function () {
        $(this).data("initialText", $(this).text().trim());
    });

    $(".editable").on("blur", function () {
        let td = $(this);
        let value = td.text().trim();
        let id = td.data("id");
        let field = td.data("field");
        let originalValue = td.data("initialText");
        if (value === originalValue) {
            return; // No change, do nothing
        }
        // console.log('Updating field:', field, 'with value:', value, 'for item ID:', id);
        td.css("background-color", "#fff3cd"); // yellow loading background
        td.append(
            '<span class="spinner-border spinner-border-sm float-end" role="status" aria-hidden="true"></span>'
        );
        // td.prop('contenteditable', false); // Disable editing while saving
        // td.off('blur'); // Remove the blur event handler to prevent multiple submissions
        $.ajax({
            url: "/sps/operator/item/update-field/" + id,
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"), // Replace with your method of getting the CSRF token
            },
            data: {
                field: field,
                value: value,
            },
            success: function (response) {
                td.css("background-color", "#d4edda"); // light green
                setTimeout(() => td.css("background-color", ""), 5000);
                toastr.success(response["message"]);
            },
            error: function () {
                td.css("background-color", "#f8d7da"); // red error
                td.text(originalValue); // revert on error
                toastr.error(response["message"]);
            },
            complete: function () {
                td.find(".spinner-border").remove(); // remove spinner
                setTimeout(() => td.css("background-color", ""), 1000);
            },
        });
    });
});

