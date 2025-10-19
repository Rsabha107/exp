<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Restricted</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">

    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/vendors/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('fnx/assets/css/theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet">

    <style>
    body {
        background: url("{{ asset('assets/img/background/P1-P2_1920x1080_pxl.png') }}") no-repeat center top fixed;
        background-size: cover;
        font-family: 'Nunito Sans', sans-serif;
        color: #fff;
        min-height: 100vh;
    }

    .card-box {
        background: rgba(255, 255, 255, 1);
        border-radius: 15px;
        padding: 2.5rem;
        text-align: center;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 500px;
    }

    .card-box h2 {
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 1rem;
        color: #000000ff;
    }

    .card-box p {
        font-size: 1.1rem;
        font-weight: 600;
        color: #000000ff;
    }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">
    <div class="col-sm-10 col-md-8 col-lg-5 col-xl-4">
        <div class="card p-4 p-sm-5 text-center">
            <h2 class="text-danger mb-3">Access Restricted</h2>
            <p class="text-dark fw-semibold">
            <p>{{ $message ?? 'You are not assigned to this event or venue. Please contact the administrator.' }}</p>
            <div class="px-3">
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-secondary d-flex align-items-center">
                        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                    </button>

                </form>
            </div>
        </div>
    </div>
</body>

</html>