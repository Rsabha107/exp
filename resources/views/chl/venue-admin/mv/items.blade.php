<script src="{{ asset('fnx/assets/js/phoenix.js') }}"></script>

<div class="modal-body">

    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="text-align: center;"></th>
                <th scope="col">Item Category</th>
                <th scope="col">Item Description</th>
                <th scope="col">Time</th>
                <th scope="col">Date</th>
                {{-- <th scope="col">Action</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td class="align-middle product white-space-nowrap">
                        <div class="avatar avatar-l">
                            <a class="d-block rounded-2 border border-translucent"
                                href="{{ asset('storage/items/img/' . $item->item_image) }}" class="glightbox"
                                data-gallery="gallery1">
                                <img src="{{ asset('storage/items/img/' . $item->item_image) }}"
                                    class="rounded-circle pull-up" alt="" width="53">
                            </a>
                        </div>
                    </td>

                    <td class="align-middle product white-space-nowrap">
                        {{ $item->prohibited_item->item_name }}</td>
                    <td class="align-middle product white-space-nowrap">
                        {{ $item->item_description }}
                    </td>
                    <td class="align-middle product white-space-nowrap">
                        {{ $item->created_at->format('h:i A') }}</td>
                    <td class="align-middle product white-space-nowrap">
                        {{ $item->created_at->format('d M Y') }}</td>
                    {{-- <td class="align-middle product white-space-nowrap">

                        @if ($item->status == 0)
                            <span class="badge bg-danger">Pending</span>
                        @elseif ($item->status == 1)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Rejected</span>
                        @endif
                    </td> --}}
                    {{-- <td class="align-middle product white-space-nowrap">
                        Action
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
