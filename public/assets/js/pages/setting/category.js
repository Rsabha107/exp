$(".js-select-event-assign-multiple, .js-select-venue-assign-multiple").select2(
    {
        closeOnSelect: false,
        placeholder: "Select ...",
    }
);

$(document).ready(function () {
    // **************************************************
    // CREATE CATEGORY
    $("#createCategoryForm").on("submit", function (e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr("action");
        let formData = form.serialize();

        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            success: function (response) {
                $("#create_categories_modal").modal("hide"); // close modal
                $("#category_table").bootstrapTable("refresh"); // refresh table
                toastr.success("Category created successfully");
                $("#createCategoryForm")[0].reset();
                $(
                    ".js-select-event-assign-multiple, .js-select-venue-assign-multiple"
                )
                    .val(null)
                    .trigger("change");
            },
            error: function (xhr) {
                toastr.error("Something went wrong while creating category");
            },
        });
    });

    // EDIT CATEGORY
    $("body").on("click", ".edit-category", function () {
        console.log("Edit category clicked");

        let id = $(this).data("id");
        let title = $(this).data("title");
        let event_ids = $(this).data("event"); // should be array of IDs
        let venue_ids = $(this).data("venue"); // should be array of IDs

        // Fill the form fields
        $("#edit_category_id").val(id);
        $("#edit_category_title").val(title);

        // Populate multi-select dropdowns
        $("#edit_category_event").val(event_ids).trigger("change");
        $("#edit_category_venue").val(venue_ids).trigger("change");

        // Set form action for update
        $("#editCategoryForm").attr("action", "/setting/categories/" + id);

        // Show modal
        $("#edit_categories_modal").modal("show");
    });

    // =========================
    // DELETE CATEGORY
    // =========================
    $("body").on("click", ".delete-category", function (e) {
        e.preventDefault();

        let id = $(this).data("id");
        console.log("Delete button clicked. Category ID:", id);

        Swal.fire({
            title: "Are you sure?",
            text: "Delete This Category?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                console.log("User confirmed deletion for ID:", id);

                $.ajax({
                    url: "/setting/categories/" + id,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    dataType: "json",
                    success: function (result) {
                        console.log("AJAX success response:", result);

                        if (!result.error) {
                            toastr.success(
                                result.message ||
                                    "Category deleted successfully"
                            );

                            // Refresh table using its fixed ID
                            console.log("Refreshing table: #category_table");
                            $("#category_table").bootstrapTable("refresh");
                        } else {
                            toastr.error(result.message || "Delete failed");
                            console.log(
                                "Delete failed with message:",
                                result.message
                            );
                        }
                    },
                    error: function (xhr, status, error) {
                        toastr.error(
                            "Something went wrong while deleting category"
                        );
                        console.log(
                            "AJAX error:",
                            status,
                            error,
                            xhr.responseText
                        );
                    },
                });
            } else {
                console.log("User cancelled deletion.");
            }
        });
    });
});

// =========================
// BOOTSTRAP TABLE HELPERS
// =========================
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
    return '<i class="bx bx-loader-alt bx-spin bx-flip-vertical"></i>';
}

// Actions column formatter
function actionsFormatter(value, row, index) {
    return `
        <a href="javascript:void(0);" class="edit-category" 
            data-id="${row.id}" 
            data-title="${row.title}" 
            data-event='${JSON.stringify(row.event_ids)}' 
            data-venue='${JSON.stringify(row.venue_ids)}'
            title="${label_update}">
            <i class="bx bx-edit mx-1"></i>
        </a>
         <button type="button" class="btn delete-category" 
            data-id="${row.id}" 
            title="${label_delete}">
            <i class="bx bx-trash text-danger mx-1"></i>
        </button>
    `;
}
