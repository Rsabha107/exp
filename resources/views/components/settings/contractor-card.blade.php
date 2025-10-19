<!-- meetings -->

<div class="card mt-4 mb-5">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            {{$slot}}
            <input type="hidden" id="data_type" value="status">
            <div class="mx-2 mb-2">
                <table id="contractor_table"
                    data-toggle="table"
                    data-classes="table table-hover  fs-9 mb-0 border-top border-translucent"
                    data-loading-template="loadingTemplate"
                    data-url="{{ route('cms.setting.contractor.list') }}"
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
                            <th data-checkbox="true" data-halign="left" data-align="center" data-visible="false"></th>
                            <!-- <th data-sortable="true" data-field="id1"><?= get_label('id', 'ID') ?></th> -->
                            <!-- <th data-sortable="true" data-field="id"><?= get_label('id', 'ID') ?></th> -->
                            <th data-sortable="false" data-field="image" data-formatter="imageFormatter" data-align="center"></th>
                            <th data-sortable="true" data-field="name"><?= get_label('name', 'Name') ?></th>
                            <th data-sortable="true" data-field="email"><?= get_label('email', 'Email') ?></th>
                            <th data-sortable="true" data-field="phone"><?= get_label('phone', 'Phone') ?></th>
                            <th data-sortable="true" data-field="address"><?= get_label('address', 'Address') ?></th>
                            <th data-sortable="true" data-field="company_name"><?= get_label('company_name', 'Company Name') ?></th>
                            <th data-sortable="true" data-field="company_type"><?= get_label('company_type', 'Company Type') ?></th>
                            <th data-sortable="true" data-field="currency_id"><?= get_label('currency_id', 'Currency') ?></th>
                            <th data-sortable="true" data-field="events" data-width="10"><?= get_label('event', 'Event') ?></th>
                            <th data-sortable="true" data-field="venues"><?= get_label('venues', 'Venues') ?></th>
                            <th data-sortable="true" data-field="status"><?= get_label('status', 'Status') ?></th>
                            <th data-sortable="true" data-field="created_at" data-visible="false"><?= get_label('created_at', 'Created at') ?></th>
                            <th data-sortable="true" data-field="updated_at" data-visible="false"><?= get_label('updated_at', 'Updated at') ?></th>
                            <th data-field="actions"><?= get_label('actions', 'Actions') ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>