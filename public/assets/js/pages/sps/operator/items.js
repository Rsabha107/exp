$(document).ready(function () {
    // console.log("all tasksJS file");

    // ************************************************** task venues
    $("body").on("click", "#addItem", function () {
        // console.log('inside edit_events')
        var id = $(this).data("id");
        var table = $(this).data("table");

        $("#add_profile_id").val(id);
        $("#add_table").val(table);

        $("#add_stored_item_modal").modal("show");
    });

    // $("body").on("click", "#ItemDetails", function () {
    //     console.log("inside ItemDetails");
    //     var id = $(this).data("id");
    //     var table = $(this).data("table");

    //     $("#add_profile_id").val(id);
    //     $("#add_table").val(table);

    //     $("#stored_item_detail_modal").modal("show");
    // });

    $("#offcanvas-add-stored-item-modalxxx").on(
        "hidden.bs.offcanvas",
        function (e) {
            $(this)
                .find("input,textarea,select")
                .val("")
                .end()
                .find("input[type=checkbox], input[type=radio]")
                .prop("checked", "")
                .end();

            // $(".js-select-project-assign-multiple").val(null).trigger("change");
            // $(".js-select-project-tags-multiple").val(null).trigger("change");
        }
    );

    $("#offcanvas-add-stored-item-modal").on(
        "hidden.bs.offcanvas",
        function () {
            $("#add_stored_item_form")
                .find("input, textarea, select")
                .each(function () {
                    if ($(this).attr("type") !== "hidden") {
                        if (this.tagName === "SELECT") {
                            this.selectedIndex = 0;
                        } else if (
                            this.type === "checkbox" ||
                            this.type === "radio"
                        ) {
                            this.checked = false;
                        } else {
                            $(this).val("");
                        }
                    }
                });
        }
    );

    $("body").on("click", "#clear_filter_btn", function () {
        console.log("inside #clear_filter_btn");
        $("#filter_schedule_form")[0].reset();
        $("#mds_date_range_filter").val("");
        $("#schedules_table").bootstrapTable("refresh");
    });

    $("body").on("click", "#offcanvas-add-stored-item", function () {
        console.log("inside #offcanvas-add-project");
        // $("#add_edit_form").get(0).reset()
        // console.log(window.choices.removeActiveItems())
        $("#cover-spin").show();
        $("#offcanvas-add-stored-item-modal").offcanvas("show");
        $("#cover-spin").hide();
    });

    // delete project
    $("body").on("click", "#deleteVisitorInformatione", function (e) {
        var id = $(this).data("id");
        var tableID = $(this).data("table");
        e.preventDefault();
        // alert("tableID: "+tableID);
        var link = $(this).attr("href");
        Swal.fire({
            title: "Are you sure?",
            text: "Delete This Data?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/sps/admin/visitor/delete/" + id,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ), // Replace with your method of getting the CSRF token
                    },
                    dataType: "json",
                    success: function (result) {
                        if (!result["error"]) {
                            toastr.success(result["message"]);
                            // divToRemove.remove();
                            // $("#fileCount").html("File ("+result["count"]+")");
                            // console.log('before table refrest for #'+tableID);
                            $("#" + tableID).bootstrapTable("refresh");
                            // Swal.fire(
                            //     'Deleted!',
                            //     'Your file has been deleted.',
                            //     'success'
                            //   )
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status);
                        console.log(thrownError);
                        // $("#cover-spin").hide();
                        toastr.error(thrownError);
                    },
                });
            }
        });
    });

    // $("body").on("click", "#edit_stored_item_offcanv", function () {
    //     console.log("inside #edit_stored_item_offcanv");
    //     $("#cover-spin").show();
    //     var id = $(this).data("id");
    //     var table = $(this).data("table");
    //     console.log("id", id);
    //     console.log("table", table);
    //     $.ajax({
    //         url: "/mds/setting/schedule/mv/get/" + id,
    //         method: "GET",
    //         async: true,
    //         success: function (response) {
    //             g_response = response.view;
    //             $("#global-edit-stored-item-content")
    //                 .empty("")
    //                 .append(g_response);
    //             $("#edit_schedule_table").val(table);
    //             $("#offcanvas-edit-stored-item-modal").offcanvas("show");
    //             $("#cover-spin").hide();
    //         },
    //         error: function (xhr, ajaxOptions, thrownError) {
    //             console.log(xhr.status);
    //             console.log(thrownError);
    //             $("#cover-spin").hide();
    //         },
    //     });
    // });

    $("body").on("click", "#ItemDetails", function () {
        $("#cover-spin").show();
        console.log("inside #edit_employee");
        // console.log("source: " + x_source);
        // console.log($("#edit_employee").data("id"));
        // reset all values

        // $("#taskTabNotes").empty("").append(refreshEmpEdit(taskID));
        id = $(this).data("id");
        console.log("employee_id: " + id);

        $.ajax({
            url: "/sps/admin/item/mv/edit/" + id,
            method: "GET",
            async: true,
            success: function (response) {
                g_response = response.view;
                $("#global-stored-item-content").empty("").append(g_response);
                $("#stored_item_detail_modal").modal("show");
                $("#cover-spin").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
                $("#cover-spin").hide();
            },
        });
    });

    // load data for items modal
    $("body").on("click", "#editStatus", function (event) {
        console.log("inside sec click edit: items.js");
        // event.preventDefault();
        var id = $(this).data("id");
        var table = $(this).data("table");
        // var route = $(this).data("route");
        // console.log("id: " + id);
        // console.log("table: " + table);

        $.get("/sps/admin/item/status/edit/" + id, function (data) {
            //  console.log('event name: ' + data);
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
});
