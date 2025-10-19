$(function() {

    // Handle toggle
    $('#tasks-list').on('change', '.task-toggle', function() {
        var taskId = $(this).data('id');
        var url = '/chl/admin/tasks/' + taskId + '/toggle';

        $.ajax({
            url: url,
            type: 'PATCH',
            success: function(html) {
                $('#tasks-list').html(html);
                toastr.success('Task status updated successfully!');
            },
            error: function(xhr) {
                toastr.error('Failed to update task status!');
                console.error(xhr);
            }
        });
    });

    // Generate tasks from categories
    $('.copy-task-form').submit(function(e) {
        e.preventDefault();
        var form = $(this);

        // Get selected event & venue
        var eventId = $('#event_dropdown').val();
        var venueId = $('#venue_dropdown').val();

        if (!eventId || !venueId) {
            toastr.error('Please select Event and Venue first.');
            return;
        }

        // Append them into the data
        var data = form.serialize() + '&event_id=' + eventId + '&venue_id=' + venueId;

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: data,
            success: function(response) {
                console.log(response);
                if (response.success) {

                    // Hide the modal after success
                    $('#generateTasksModal').modal(
                        'hide'); 

                    // Optionally, refresh tasks list if html returned
                    if (response.html) {
                        $('#tasks-list').html(response.html);
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Something went wrong');
                console.error(xhr.responseText);
            }
        });
    });

    // Delete task
    $('#tasks-list').on('click', '.task-delete', function() {
        var taskId = $(this).data('id');
        var url = '/chl/admin/tasks/' + taskId;

        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        if (response.success && response.html) {
                            $('#tasks-list').html(response.html);
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Task deleted successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Failed to delete task.'
                        });
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });

    document.querySelector('#event_dropdown').addEventListener('change', function() {
        document.querySelector('#event_id').value = this.value;
    });

    document.querySelector('#venue_dropdown').addEventListener('change', function() {
        document.querySelector('#venue_id').value = this.value;
    });
});
