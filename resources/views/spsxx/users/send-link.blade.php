<!-- resources/views/users/send-link.blade.php -->
@php
    use App\Services\SignedUserLinkGenerator;

    $signedUrl = SignedUserLinkGenerator::generate('John Doe', 'john@example.com');
@endphp

<div class="p-4 bg-white rounded shadow">
    <label>Secure Link for User Creation:</label>
    <input type="text" readonly class="form-control" value="{{ $signedUrl }}" id="signedUrlInput">

    <button class="btn btn-primary mt-2" onclick="navigator.clipboard.writeText(document.getElementById('signedUrlInput').value)">
        📋 Copy Link
    </button>
</div>
