<!-- meetings -->

<div class="card mt-4 mb-5">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            {{$slot}}
            <div class="mx-2 mb-2">
                <table  id="audit_table"
                        data-toggle="table"
                        data-classes="table table-hover  fs-9 mb-0 border-top border-translucent"
                        data-loading-template="loadingTemplate"
                        data-url="{{ route('sec.log.list') }}"
                        data-icons-prefix="bx"
                        data-icons="icons"
                        data-show-export="true"
                        data-show-columns-toggle-all="true"
                        data-show-toggle="true"
                        data-show-fullscreen="true"
                        data-show-refresh="true"
                        data-total-field="total"
                        data-trim-on-search="false"
                        data-data-field="rows"
                        data-page-size="10"
                        data-page-list="[5, 10, 20, 50, 100, 200]"
                        data-search="true"
                        data-side-pagination="server"
                        data-show-columns="true"
                        data-pagination="true"
                        data-sort-name="id"
                        data-sort-order="asc"
                        data-mobile-responsive="true"
                        data-buttons-class="secondary"
                        data-query-params="queryParams">
                    <thead>
                        <tr>
                            <!-- <th data-checkbox="true"></th> -->
                            <th data-sortable="true" data-field="id" data-visible="false"><?= get_label('id', 'ID') ?></th>
                            <!-- <th data-sortable="false" data-field="image" data-align="center"></th> -->
                            <th data-sortable="true" data-field="user_type"><?= get_label('user_type', 'User Type') ?></th>
                            <th data-sortable="true" data-field="user_id"><?= get_label('user_id', 'User ID') ?></th>
                            <th data-sortable="true" data-field="event"><?= get_label('event', 'Event') ?></th>
                            <th data-sortable="true" data-field="event_id"><?= get_label('event_id', 'Event ID') ?></th>
                            <th data-sortable="true" data-field="venue_id"><?= get_label('venue_id', 'Venue ID') ?></th>
                            <th data-sortable="true" data-field="auditable_type"><?= get_label('auditable_type', 'Auditable Type') ?></th>
                            <th data-sortable="true" data-field="auditable_id"><?= get_label('auditable_id', 'Auditable ID') ?></th>
                            <th data-sortable="true" data-field="old_values"><?= get_label('old_values', 'Old Values') ?></th>
                            <th data-sortable="true" data-field="new_values"><?= get_label('new_values', 'New Values') ?></th>
                            <th data-sortable="true" data-field="url"><?= get_label('url', 'URL') ?></th>
                            <th data-sortable="true" data-field="ip_address"><?= get_label('ip_address', 'IP Address') ?></th>
                            <th data-sortable="true" data-field="user_agent"><?= get_label('user_agent', 'User Agent') ?></th>
                            <th data-sortable="true" data-field="tags"><?= get_label('tags', 'Tags') ?></th>
                            <th data-sortable="true" data-field="created_at" ><?= get_label('created_at', 'Created at') ?></th>
                            <th data-sortable="true" data-field="updated_at" ><?= get_label('updated_at', 'Updated at') ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    ("use strict");

    function bookingQueryParams(p) {
        return {

            mds_schedule_event_filter: $("#mds_schedule_event_filter").val(),
            mds_schedule_venue_filter: $("#mds_schedule_venue_filter").val(),
            mds_schedule_rsp_filter: $("#mds_schedule_rsp_filter").val(),
            mds_date_range_filter: $("#mds_date_range_filter").val(),
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
        return '<i class="bx bx-loader-circle bx-spin bx-flip-vertical" ></i>';
    }

    $("#mds_schedule_event_filter,#mds_schedule_venue_filter,#mds_schedule_rsp_filter,#mds_date_range_filter").on("change", function(e) {
        e.preventDefault();
        console.log("booking card on change");
        $("#bookings_table").bootstrapTable("refresh");
    });
</script>