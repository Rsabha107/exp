<!-- meetings -->

<div class="card mt-4 mb-5">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            {{$slot}}
            <input type="hidden" id="data_type" value="venue">
            <div class="mx-2 mb-2">
                <table id="products_table"
                    data-toggle="table"
                    data-classes="table table-hover  fs-9 mb-0 border-top border-translucent"
                    data-loading-template="loadingTemplate"
                    data-url="{{ route('cms.setting.product.list')}}"
                    data-icons-prefix="bx"
                    data-icons="icons"
                    data-show-export="true"
                    data-show-columns-toggle-all="true"
                    data-show-refresh="true"
                    data-total-field="total"
                    data-show-toggle="true"
                    data-trim-on-search="false"
                    data-data-field="rows"
                    data-page-list="[5, 10, 20, 50, 100, 200]"
                    data-search="true"
                    data-side-pagination="server"
                    data-show-columns="true"
                    data-pagination="true"
                    data-sort-name="id"
                    data-sort-order="desc"
                    data-mobile-responsive="true"
                    data-buttons-class="secondary"
                    data-query-params="queryParams">
                    <thead>
                        <tr>
                            <!-- <th data-checkbox="true"></th> -->
                            <!-- <th data-sortable="true" data-field="id" class="align-middle white-space-wrap fw-bold fs-9"><?= get_label('id', 'ID') ?></th> -->
                            <th data-sortable="false" data-field="image" data-formatter="imageFormatter" data-align="center"></th>
                            <th data-sortable="true" data-field="product_name"><?= get_label('product_name', 'Product') ?></th>
                            <th data-sortable="true" data-field="product_price"><?= get_label('price', 'Price') ?></th>
                            <th data-sortable="true" data-field="unit_type"><?= get_label('unit_type', 'Unit') ?></th>
                            <th data-sortable="true" data-field="description"><?= get_label('description', 'Description') ?></th>
                            <!-- <th data-sortable="true" data-field="chicken_quantity"><?= get_label('chicken_quantity', 'Chicken Quantity') ?></th>
                            <th data-sortable="true" data-field="meat_quantity"><?= get_label('meat_quantity', 'Meat Quantity') ?></th>
                            <th data-sortable="true" data-field="vegetarian_quantity"><?= get_label('vegetarian_quantity', 'Vegetarian Quantity') ?></th> -->
                            <th data-sortable="true" data-field="created_at" data-visible="false"><?= get_label('created_at', 'Created at') ?></th>
                            <th data-sortable="true" data-field="updated_at" data-visible="false"><?= get_label('updated_at', 'Updated at') ?></th>
                            <th data-field="actions" class="text-end"><?= get_label('actions', 'Actions') ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>