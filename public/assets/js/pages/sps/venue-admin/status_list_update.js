$(document).ready(function () {
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
            7: "info",
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
            6: "danger",
            7: "info",
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
                if (response.error) {
                    console.error("Error updating status:", response.message);
                    toastr.error(response.message || "Failed to update status");
                    return;
                }
                console.log("Status updated successfully:", response);
                console.log("Status color:", statusColorMap[status]);
                profileStatusHtml = `<span
                                    class="badge-display badge-phoenix fs--2 align-middle white-space-wrap ms-1 badge-phoenix-${
                                        response.profile_status_color
                                    }">
                                    <span class="badge-label">
                                        ${response.profile_status_title}
                                    </span>
                                </span>`;
                console.log("Profile status HTML:", profileStatusHtml);
                // profileStatusHtml = `<span class="badge badge-phoenix badge-phoenix-${response.profile_status_color}">${response.profile_status_title}</span>`;
                // $("#profileStatus")
                //     .val(response.profile_status_id)
                //     .trigger("change");
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
});
