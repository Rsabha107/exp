@extends('chl.admin.layout.template')

@section('main')
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->

    <div class="d-flex justify-content-between m-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">{{ get_label('home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ get_label('audit_logs', 'Audit Logs') }}
                    </li>
                </ol>
            </nav>
        </div>

        <div>
            <button class="btn px-3 btn-phoenix-secondary" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#bookingFilterOffcanvas" aria-haspopup="true" aria-expanded="false">
                <svg class="svg-inline--fa fa-filter text-primary" data-fa-transform="down-3" aria-hidden="true"
                    focusable="false" data-prefix="fas" data-icon="filter" role="img" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 512 512" data-fa-i2svg="" style="transform-origin: 0.5em 0.6875em;">
                    <g transform="translate(256 256)">
                        <g transform="translate(0, 96)  scale(1, 1)  rotate(0 0 0)">
                            <path fill="currentColor"
                                d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"
                                transform="translate(-256 -256)"></path>
                        </g>
                    </g>
                </svg>
            </button>
        </div>
    </div>

    <!-- Card wrapper -->
    <div class="card mt-4 mb-5">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="audit_table" data-toggle="table" data-url="{{ route('sec.audit.list') }}"
                    data-side-pagination="server" data-pagination="true" data-search="true" data-sort-name="id"
                    data-sort-order="desc" data-total-field="total" data-data-field="rows" data-page-size="10"
                    data-page-list="[5, 10, 20, 50, 100, 200]" data-show-refresh="true" data-show-toggle="true"
                    data-show-fullscreen="true" data-show-columns="true" data-show-export="true"
                    class="table table-hover fs-9 mb-0 border-top border-translucent">
                    <thead>
                        <tr>
                            <th data-field="id" data-visible="false">{{ get_label('id', 'ID') }}</th>
                            <th data-field="user_type" data-sortable="true">{{ get_label('user_type', 'User Type') }}</th>
                            <th data-field="user">{{ get_label('user', 'User') }}</th>
                            <th data-field="event" data-sortable="true">{{ get_label('event', 'Event') }}</th>
                            <th data-sortable="true" data-field="event_id">{{ get_label('event_id', 'Event ID') }}</th>
                            <th data-sortable="true" data-field="venue_id">{{ get_label('venue_id', 'Venue ID') }}</th>
                            <th data-field="auditable_type">{{ get_label('auditable_type', 'Auditable Type') }}</th>
                            <th data-field="auditable_id">{{ get_label('auditable_id', 'Auditable ID') }}</th>
                            <th data-field="old_values">{{ get_label('old_values', 'Old Values') }}</th>
                            <th data-field="new_values">{{ get_label('new_values', 'New Values') }}</th>
                            <th data-field="url">{{ get_label('url', 'URL') }}</th>
                            <th data-field="ip_address">{{ get_label('ip_address', 'IP Address') }}</th>
                            <th data-field="user_agent">{{ get_label('user_agent', 'User Agent') }}</th>
                            <th data-field="tags">{{ get_label('tags', 'Tags') }}</th>
                            <th data-field="created_at" data-sortable="true">{{ get_label('created_at', 'Created At') }}
                            </th>
                            <th data-field="updated_at" data-sortable="true">{{ get_label('updated_at', 'Updated At') }}
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Offcanvas Element -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="bookingFilterOffcanvas">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">Filter Audit Logs</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <form id="auditFilterForm">
                        <div class="mb-3">
                            <label for="search_event" class="form-label">Event</label>
                            <input type="text" class="form-control" id="search_event" name="event"
                                placeholder="Search by event">
                        </div>
                        <div class="mb-3">
                            <label for="search_venue" class="form-label">Venue</label>
                            <input type="text" class="form-control" id="search_venue" name="venue"
                                placeholder="Search by venue">
                        </div>
                        <div class="mb-3">
                            <label for="search_user" class="form-label">User</label>
                            <input type="text" class="form-control" id="search_user" name="user"
                                placeholder="Search by user">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="timepicker2">Date</label>
                            <input class="form-control datetimepicker flatpickr-input" id="timepicker2" type="text"
                                placeholder="YYYY-MM-DD to YYYY-MM-DD"
                                data-options='{"mode":"range","dateFormat":"Y-m-d","disableMobile":true}'
                                readonly="readonly">
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-secondary" id="filterResetBtn">Reset</button>
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="offcanvas">Cancel</button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- Audit Details Modal -->
            <div class="modal fade" id="auditModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white">Audit Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <p><strong>ID:</strong> <span id="audit_id"></span></p>
                                    <p><strong>User Type:</strong> <span id="audit_user_type"></span></p>
                                    <p><strong>User ID:</strong> <span id="audit_user_id"></span></p>
                                    <p><strong>User Name:</strong> <span id="audit_user"></span></p>
                                    <p><strong>Event:</strong> <span id="audit_event"></span></p>
                                    <p><strong>Auditable Type:</strong> <span id="audit_auditable_type"></span></p>
                                    <p><strong>Auditable ID:</strong> <span id="audit_auditable_id"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>URL:</strong> <span id="audit_url"></span></p>
                                    <p><strong>IP Address:</strong> <span id="audit_ip_address"></span></p>
                                    <p><strong>User Agent:</strong> <span id="audit_user_agent"></span></p>
                                    <p><strong>Tags:</strong> <span id="audit_tags"></span></p>
                                    <p><strong>Created At:</strong> <span id="audit_created_at"></span></p>
                                    <p><strong>Updated At:</strong> <span id="audit_updated_at"></span></p>
                                </div>
                            </div>

                            <h6 class="mt-3">Old Values</h6>
                            <table class="table table-bordered table-sm" id="audit_old_values">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <h6 class="mt-3">New Values</h6>
                            <table class="table table-bordered table-sm" id="audit_new_values">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            console.log("Audit Logs table initialized");

            // Initialize flatpickr for date range
            flatpickr("#timepicker2", {
                mode: "range",
                dateFormat: "Y-m-d", // match DB format
                disableMobile: true,
                onClose: function(selectedDates, dateStr) {
                    // Refresh table instantly when date changes
                    $('#audit_table').bootstrapTable('refresh');
                }
            });

            // Modify query params sent to server
            window.queryParams = function(params) {
                return {
                    ...params,
                    mds_date_range_filter: $('#timepicker2').val().trim(), // ✅ comma added
                    mds_schedule_event_filter: $('#search_event').val().trim(),
                    mds_schedule_venue_filter: $('#search_venue').val().trim(),
                    mds_schedule_rsp_filter: $('#search_user').val().trim()
                };
            };

            $('#filterResetBtn').on('click', function() {
                // Clear all filters
                $('#search_event').val('');
                $('#search_venue').val('');
                $('#search_user').val('');
                $('#timepicker2').flatpickr().clear();

                // Refresh table without filters
                $('#audit_table').bootstrapTable('refresh');
            });
            $('#auditFilterForm').on('submit', function(e) {
                e.preventDefault();
                $('#audit_table').bootstrapTable('refresh');
                var offcanvasElement = document.getElementById('bookingFilterOffcanvas');
                var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                offcanvas.hide();
            });

            // Table row click to show modal
            $('#audit_table').on('click-row.bs.table', function(e, row) {
                $('#audit_id').text(row.id);
                $('#audit_user_type').text(row.user_type);
                $('#audit_user_id').text(row.user_id);
                $('#audit_user').text(row.user);
                $('#audit_event').text(row.event);
                $('#audit_auditable_type').text(row.auditable_type);
                $('#audit_auditable_id').text(row.auditable_id);
                $('#audit_url').text(row.url);
                $('#audit_ip_address').text(row.ip_address);
                $('#audit_user_agent').text(row.user_agent);
                $('#audit_tags').text(row.tags ?? '-');
                $('#audit_created_at').text(row.created_at);
                $('#audit_updated_at').text(row.updated_at);

                let oldValues = JSON.parse(row.old_values || '{}');
                let oldRows = '';
                for (let key in oldValues) {
                    oldRows += `<tr><td>${key}</td><td>${oldValues[key]}</td></tr>`;
                }
                $('#audit_old_values tbody').html(oldRows || '<tr><td colspan="2">No Data</td></tr>');

                let newValues = JSON.parse(row.new_values || '{}');
                let newRows = '';
                for (let key in newValues) {
                    newRows += `<tr><td>${key}</td><td>${newValues[key]}</td></tr>`;
                }
                $('#audit_new_values tbody').html(newRows || '<tr><td colspan="2">No Data</td></tr>');

                $('#auditModal').modal('show');
            });

            // Instant filter for event, venue, user
            $('#search_event, #search_venue, #search_user').on('keyup', function() {
                $('#audit_table').bootstrapTable('refresh');
            });
        });
    </script>
@endpush
