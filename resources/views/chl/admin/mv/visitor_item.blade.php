<script src="{{ asset('fnx/assets/js/phoenix.js') }}"></script>
<script src="{{ asset('assets/js/pages/sps/admin/status_list_update.js') }}"></script>

<style>
    .select2-profile-status-container .select2-selection--single {
        background-color: transparent !important;
        border: 0px solid #ccc;
        padding: 6px;
        min-height: 38px;
        color: #333;
    }
</style>

<script>
    // $('.badge-cell').on('click', function() {
    //     const cell = $(this);
    //     cell.find('.badge-display').addClass('d-none');
    //     cell.find('.badge-select').removeClass('d-none').focus();
    // });

    $('.editable').on('focus', function() {
        $(this).data('initialText', $(this).text().trim());
    });

    $('.editable').on('blur', function() {
        let td = $(this);
        let value = td.text().trim();
        let id = td.data('id');
        let field = td.data('field');
        let originalValue = td.data('initialText');
        if (value === originalValue) {
            return; // No change, do nothing
        }
        // console.log('Updating field:', field, 'with value:', value, 'for item ID:', id);
        td.css('background-color', '#fff3cd'); // yellow loading background
        td.append(
            '<span class="spinner-border spinner-border-sm float-end" role="status" aria-hidden="true"></span>'
        );
        // td.prop('contenteditable', false); // Disable editing while saving
        // td.off('blur'); // Remove the blur event handler to prevent multiple submissions
        $.ajax({
            url: '/sps/admin/item/update-field/' + id,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                field: field,
                value: value
            },
            success: function(response) {
                td.css('background-color', '#d4edda'); // light green
                setTimeout(() => td.css('background-color', ''), 5000);
                toastr.success(response["message"]);
            },
            error: function() {
                td.css('background-color', '#f8d7da'); // red error
                td.text(originalValue); // revert on error
                toastr.error(response["message"]);
            },
            complete: function() {
                td.find('.spinner-border').remove(); // remove spinner
                setTimeout(() => td.css('background-color', ''), 1000);
            }
        });
    });
</script>

<div class="row g-3 mb-3">
    <div class="col-4 col-lg-4 col-xl-4">
        <div class="card  px-2 h-100">
            <div class="border-bottom border-translucent">
                <h5 class="pb-4 border-bottom border-translucent"></h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
                        <div class="d-flex justify-content-between"><span class="fw-normal fs-9 mx-1"> <span
                                    class="fw-bold"></span>Status </span>
                            <span>
                                <div class="col-auto badge-cell profile-status-cell">
                                    <span
                                        class="badge-display badge-phoenix fs--2 align-middle white-space-wrap ms-1 badge-phoenix-{{ $spectator->profileStatus?->color }}">
                                        <span class="badge-label">
                                            {{ ucfirst($spectator->profileStatus?->title) }}
                                        </span>
                                    </span>
                                </div>
                            </span>
                        </div>
                    </li>
                    <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
                        <div class="d-flex justify-content-between"><span class="fw-normal fs-9 mx-1"> <span
                                    class="fw-bold"></span>Ref # </span>
                            <span>
                                {{ $spectator->ref_number }}
                            </span>
                        </div>
                    </li>
                    <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
                        <div class="d-flex justify-content-between"><span class="fw-normal fs-9 mx-1"> <span
                                    class="fw-bold"></span>Storage Type </span>
                            <span>
                                {{ $spectator->storageType->title }}
                            </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-4 col-lg-4 col-xl-4">
        <div class="card  px-2 h-100">
            <div class="border-bottom border-translucent">
                <h5 class="pb-4 border-bottom border-translucent"></h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
                        <div class="d-flex justify-content-between"><span class="fw-normal fs-9 mx-1"> <span
                                    class="fw-bold"></span>Event </span>
                            <span
                                class="fw-bold {{ optional($spectator->event)->id == session()->get('EVENT_ID') ? 'text-success' : 'text-danger' }}">
                                {{ $spectator->event->name }}
                            </span>
                        </div>
                    </li>
                    <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
                        <div class="d-flex justify-content-between"><span class="fw-normal fs-9 mx-1"> <span
                                    class="fw-bold"></span>Venue </span>
                            <span
                                class="fw-bold {{ optional($spectator->venue)->id == session()->get('VENUE_ID') ? 'text-success' : 'text-danger' }}">
                                {{ $spectator->venue->title }}
                            </span>
                        </div>
                    </li>
                    <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
                        <div class="d-flex justify-content-between"><span class="fw-normal fs-9 mx-1"> <span
                                    class="fw-bold"></span>Location </span>
                            <span>
                                {{ $spectator->location->title }}
                            </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-4 col-lg-4 col-xl-4">
        <div class="card  px-2 h-100">
            <div class="border-bottom border-translucent">
                <h5 class="pb-4 border-bottom border-translucent"></h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
                        <div class="d-flex justify-content-between"><span class="fw-normal fs-9 mx-1"> <span
                                    class="fw-bold"></span>Full name </span>
                            <span>
                                {{ $spectator->full_name }}
                            </span>
                        </div>
                    </li>
                    <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
                        <div class="d-flex justify-content-between"><span class="fw-normal fs-9 mx-1"> <span
                                    class="fw-bold"></span>Email </span>
                            <span>
                                {{ $spectator->email_address }}
                            </span>
                        </div>
                    </li>
                    <li class="list-group-item bg-transparent list-group-crm fw-bold text-body fs-9 py-2">
                        <div class="d-flex justify-content-between"><span class="fw-normal fs-9 mx-1"> <span
                                    class="fw-bold"></span>Phone </span>
                            <span>
                                {{ $spectator->phone_number }}
                            </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="col-12 col-lg-12">
    <div class="card  h-100">
        <div class="card-body">
            <div class="table-responsive mx-n1 px-1 scrollbar">
                <table class="table fs-9 mb-0 border-top border">
                    <thead>
                        <tr>
                            <th class="sort white-space-nowrap align-middle border-end border-translucent"
                                scope="col"></th>
                            <th class="sort text-start pe-0 align-middle border-end border-translucent"
                                scope="col">ACTION</th>
                            <th class="sort white-space-nowrap align-middle border-end border-translucent"
                                scope="col" data-sort="product" style="width:10%;">LOCATION</th>
                            <th class="sort align-middle border-end border-translucent" scope="col"
                                data-sort="customer" style="width:10%;">TAG
                            </th>
                            <th class="sort align-middle border-end border-translucent" scope="col"
                                data-sort="rating" style="min-width:110px;">ITEM</th>
                            <th class="sort align-middle border-end border-translucent" scope="col"
                                data-sort="rating" style="min-width:110px;">ITEM DESCRIPTION</th>
                            <th class="sort align-middle border-end border-translucent" scope="col"
                                data-sort="rating" style="min-width:110px;">Operator Commnets</th>
                            <th class="sort align-middle border-end border-translucent" scope="col"
                                style="max-width:350px;" data-sort="review">TIME</th>
                            <th class="sort text-start ps-5 align-middle border-end border-translucent"
                                scope="col" data-sort="status">DATE
                            </th>
                        </tr>
                    </thead>
                    <tbody class="list" id="table-latest-review-body">
                        <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                            @foreach ($spectator->items as $item)
                        <tr>
                            <td class="py-2 align-middle product white-space-nowrap border-end border-translucent">
                                <div class="d-flex justify-content-center align-items-center">
                                    <a href="{{ asset('storage/items/img/' . $item->item_image) }}"
                                        class="glidebox" data-gallery="gallery1">
                                        <img src="{{ asset('storage/items/img/' . $item->item_image) }}"
                                            alt="Event Logo" class="rounded-circle shadow-sm pull-up"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    </a>
                                </div>
                            </td>
                            <td
                                class="align-middle text-end white-space-nowrap pe-0 action py-2 border-end border-translucent pe-2">
                                <select class="form-select select2-status mx-auto" data-id="{{ $item->id }}"
                                    data-url="{{ route('sps.admin.item.update.status') }}">
                                    @foreach ($item_statuses as $status)
                                    @hasrole('SuperAdmin|VenueAdmin')
                                    <option value="{{ $status->id }}"
                                        {{ $item->item_status_id == $status->id ? 'selected' : '' }}
                                        data-color="#28a745">{{ $status->title }}</option>
                                    @endhasrole
                                    @endforeach
                                </select>
                            </td>
                            <td contenteditable="true" data-id="{{ $item->id }}" data-field="storage_location"
                                class="delivery align-middle white-space-nowrap text-body py-2 editable">
                                {{ $item->storage_location }}
                                <i class="fas fa-pencil-alt edit-hover-icon text-muted d-none ms-2"></i>
                            </td>
                            <td contenteditable="true" data-id="{{ $item->id }}"
                                data-field="storage_tag_number"
                                class="delivery align-middle white-space-nowrap text-body py-2 editable">
                                {{ $item->storage_tag_number }}
                                <i class="fas fa-pencil-alt edit-hover-icon text-muted d-none ms-2"></i>
                            </td>
                            <td class="align-middle product white-space-wrap border-end border-translucent">
                                <div class="fw-semibold">{{ $item->prohibited_item->item_name }}
                                </div>
                            </td>
                            <td class="align-middle product white-space-wrap border-end border-translucent">
                                <div class="fw-semibold">{{ $item->item_description }}
                                </div>
                            </td>
                            <td contenteditable="true" data-id="{{ $item->id }}"
                                data-field="operator_comments"
                                class="delivery align-middle white-space-wrap text-body py-2 editable">
                                {{ $item->operator_comments }}
                                <i class="fas fa-pencil-alt edit-hover-icon text-muted d-none ms-2"></i>
                            </td>
                            <td class="align-middle product white-space-nowrap border-end border-translucent">
                                <div class="fw-semibold">{{ $item->created_at->format('h:i A') }}
                                </div>
                            </td>
                            <td class="align-middle product white-space-nowrap pe-2">
                                <div class="fw-semibold">{{ $item->created_at->format('d M Y') }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>