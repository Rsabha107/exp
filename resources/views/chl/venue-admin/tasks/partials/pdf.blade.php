<div class="pdf-container">
    <h2>Daily Operations Checklist ({{ $categories->sum(fn($c) => $c->leadTasks->count()) }})</h2>
    <div class="col">
        <h3 class="mb-0 text-primary position-relative fw-bold">
            <span class=" pe-2">{{ $currentEvent?->name ?? 'N/A' }}</span>
        </h3>
        <p class="mb-0 fw-bold">{{ $currentVenue?->title ?? 'N/A' }}</p>
        <p class="mb-0 fw-bold">{{ format_date($lead_report->reporting_date) ?? 'N/A' }}</p>
    </div>
    @foreach ($categories as $category)
    <h5 class="category-label">{{ $category->title }}</h5>

    @foreach ($category->leadTasks as $task)
    <div class="task-row">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" @checked($task->completed_flag)>
            {{-- <label class="form-check-label">{{ $task->title }}</label> --}}

            @if ($task->completed_flag)
            <span style="color:#28a745;">
                {{ $task->title }}

            </span>
            @else
            <span style="color:#dc3545;">
                {{ $task->title }}
            </span>
            @endif
        </div>
    </div>
    @endforeach
    @endforeach
    <h3>User for Event: {{ $currentEvent->name }}</h3>

    <ul class="list-group" style="width: 100%;">
        <li class="list-group-item d-flex justify-content-between">
            <strong>Name:</strong> <span>{{ $lead_report->user?->name }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <strong>Email:</strong> <span>{{ $lead_report->user?->email }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <strong>Phone:</strong> <span>{{ $lead_report->user?->phone ?? '-' }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <strong>Stadium:</strong> <span>{{ $categories->first()?->venue?->title ?? 'N/A' }}</span>
        </li>

        <li class="list-group-item">
            <strong class="d-block mb-2 text-primary">Report Comments / Things to Report:</strong>
            <div class=" table-responsive" style="max-height: 220px; overflow-y: auto;">

                <br>
                <table style="border-collapse: collapse; width: 100%; ">
                    <thead style="background-color: #0d6efd; color:#fff;">
                        <tr>
                            <th style="width: 6%; text-align:center; border: 1px solid #5f5f5f; padding: 6px;">#</th>
                            <th style="width: 28%; border: 1px solid #5f5f5f; padding: 6px;">Category</th>
                            <th style="border: 1px solid #5f5f5f; padding: 6px;">Comment</th>
                            <th style="border: 1px solid #5f5f5f; padding: 6px;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $count = 1; @endphp
                        @foreach ($categories as $category)
                        @foreach ($category->leadComments ?? [] as $comment)
                        <tr>
                            <td style="border: 1px solid #5f5f5f; text-align:center; color:#6c757d; padding: 6px;">
                                {{ $count++ }}
                            </td>
                            <td style="border: 1px solid #5f5f5f; padding: 3px;">
                                <span>
                                    {{ $category->title }}
                                </span>
                            </td>
                            <td style="border: 1px solid #5f5f5f;  padding: 3px;">
                                {{ $comment->comment }}
                            </td>
                            <td style="border: 1px solid #5f5f5f;  padding: 3px;">
                                @if ($comment->created_at)
                                <small class="text-muted mb-0">
                                    {{ $comment->created_at->format('d M, Y') }}
                                </small>
                                <small class="text-muted mb-0">
                                    {{ $comment->created_at?->format('h:i A') ?? '—' }}
                                </small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @endforeach

                        @if ($count === 1)
                        <tr>
                            <td colspan="3"
                                style="border: 1px solid #000; text-align:center; color:#6c757d; padding: 6px;">
                                No comments available
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>

            </div>
        </li>

    </ul>

</div>

{{-- <script>
    document.getElementById('generatePdf').addEventListener('click', function() {
        fetch("{{ route('chl.venue.admin.tasks.export.pdf') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.preview_url) {
                    window.open(data.preview_url, '_blank');
                }
            })
            .catch(err => console.error(err));
    });
</script> --}}

<style>
    body {
        font-family: 'Poppins', 'Inter', 'Montserrat', sans-serif;
        font-size: 12px;
        color: #333;
    }

    h2 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    ul.list-group {
        padding: 0;
        margin: 0;
        list-style: none;
        border: 1px solid #ccc;
        /* outer border */
        border-radius: 4px;
        width: 600px;
    }

    ul.list-group li {
        border-bottom: 1px solid #ccc;
        padding: 6px 10px;
        display: flex;
        justify-content: space-between;
    }

    ul.list-group li:last-child {
        border-bottom: none;
        /* remove bottom border on last item */
    }

    .category-label {
        background: #0d6efd;
        /* blue background */
        color: #fff;
        /* make text white */
        padding: 6px 10px;
        border-radius: 4px;
        font-weight: bold;
    }

    /* Optional: hover effect for comments */
    .comment-item:hover {
        background-color: #e9ecef;
    }

    #event-comments {
        max-height: 250px;
        /* scrollable if too many comments */
        overflow-y: auto;
    }

    .comment-text {
        font-weight: 500;
    }
</style>