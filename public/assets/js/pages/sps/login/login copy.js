// $(document).ready(function () {

$("#loginForm").on("submit", function (e) {
    e.preventDefault();

    let form = $(this);
    let btn = $("#login-btn");
    let spinner = $("#login-spinner");
    let text = $("#login-text");
    let errorBox = $("#login-error");

    // Reset error
    errorBox.text("");

    // Disable button & show spinner
    btn.prop("disabled", true);
    spinner.removeClass("d-none");
    text.text("Logging in...");

    $.ajax({
        url: form.attr("action"),
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"), // Replace with your method of getting the CSRF token
        },
        data: form.serialize(),
        success: function (response) {
            console.log("Login response:", response);
            if (response.success) {
                window.location.href = response.redirect_url ;
                // window.location.href = response.redirect_url || "/dashboard";
            } else {
                errorBox.text(response.message || "Invalid credentials.");
            }
        },
        error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
            if (errors.login) {
                // alert(errors.login[0]);            
                errorBox.text(errors.login[0]);

            }
            // errorBox.text("Something went wrong. Please try again.");
        },
        complete: function () {
            // Re-enable button & hide spinner
            btn.prop("disabled", false);
            spinner.addClass("d-none");
            text.text("Login");
        },
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
