$(document).ready(function () {
    // console.log("all tasksJS file");

    // ************************************************** task venues
    $(".js-select-event-assign-multiple-venue_id").select2({
        closeOnSelect: false,
        placeholder: "Select ...",
    });

    $(".js-select-event-assign-multiple-edit_venue_id").select2({
        closeOnSelect: false,
        placeholder: "Select ...",
    });

    $("body").on("click", "#editPermVenueEvents", function () {
        console.log("inside edit_events");

        var id = $(this).data("id");
        var table = $(this).data("table");
        // console.log('edit venues in venues.js');
        // console.log('id: '+id);
        // console.log('table: '+table);
        // var target = document.getElementById("edit_venues_modal");
        // var spinner = new Spinner().spin(target);
        // $("#edit_event_table").modal("show");
        $.ajax({
            url: "/setting/per_venue_event/get/" + id,
            type: "get",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"), // Replace with your method of getting the CSRF token
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                let imageUrl = "";
                // Get the Dropify instance
                let drEvent = $("#edit_file_name").dropify();

                // Reset and destroy the previous instance
                drEvent = drEvent.data("dropify");
                drEvent.resetPreview();
                drEvent.clearElement();
                imageUrl = response.op.event_logo ?? "default.png";
                drEvent.settings.defaultFile = `/storage/event/logo/${imageUrl}`;
                drEvent.destroy();
                drEvent.init();

                var eventVenues = response.venues.map((venue) => venue.id);
                console.log(eventVenues);

                $("#perm_venue_edit_event_id").val(response.op.id);
                $("#perm_venue_edit_event_name").val(response.op.name);
                $("#perm_venue_edit_venue_id").val(eventVenues);
                $("#perm_venue_edit_venue_id").trigger("change");
                $("#editActiveFlag").val(response.op.active_flag);
                $("#perm_venue_edit_event_table").val(table);
                // $("#edit_event_modal").modal("show");
            },
        }).done(function () {
            $("#perm_venue_edit_event_modal").modal("show");
        });
    });
});

$("body").on("click", "#deleteEvent", function (e) {
    var id = $(this).data("id");
    var tableID = $(this).data("table");
    e.preventDefault();
    // alert('in deleteStatus '+tableID);
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
                url: "/setting/per_venue_event/delete/" + id,
                type: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"), // Replace with your method of getting the CSRF token
                },
                dataType: "json",
                success: function (result) {
                    if (!result["error"]) {
                        toastr.success(result["message"]);
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
                },
            });
        }
    });
});

("use strict");
function queryParams(p) {
    return {
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
    };
}

window.icons = {
    refresh: "bx-refresh",
    toggleOn: "bx-toggle-right",
    toggleOff: "bx-toggle-left",
    fullscreen: "bx-fullscreen",
    columns: "bx-list-ul",
    export_data: "bx-list-ul",
};

function loadingTemplate(message) {
    return '<i class="bx bx-loader-alt bx-spin bx-flip-vertical" ></i>';
}

function imageFormatter(value, row, index) {
    if (!value)
        return `<div class="avatar avatar-m">
                                <a  href="#" role="button" id="editPermVenueEvents" data-id=' . $op->id . ' data-table="event_table">
                                    <img class="rounded-circle pull-up" src="/storage/event/logo/noimage.png" alt="" />
                                </a>
                            </div>`;
    return `<div class="avatar avatar-m">
                                <a  href="#" role="button" id="editPermVenueEvents" data-id=' . $op->id . ' data-table="event_table">
                                    <img class="rounded-circle pull-up" src="/storage/event/logo/${value}" alt="" />
                                </a>
                            </div>`;
}
