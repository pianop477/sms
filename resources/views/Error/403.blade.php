<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied - SRT Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --danger-color: #e63946;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --success-color: #4cc9f0;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: var(--dark-color);
        }

        .error-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem;
        }

        .error-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .error-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .error-icon {
            font-size: 5rem;
            color: var(--danger-color);
            margin-bottom: 1.5rem;
        }

        .error-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .error-body {
            padding: 2rem;
            text-align: center;
        }

        .btn-modern {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
            color: white;
        }

        .error-code {
            font-size: 5rem;
            font-weight: 700;
            color: var(--danger-color);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .error-description {
            color: var(--gray-color);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .logo span {
            color: var(--success-color);
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
            color: var(--gray-color);
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center h-100">
    <div class="error-container">
        <div class="error-card">
            <div class="error-header">
                <h1 class="error-code">403</h1>
                <h2 class="error-title">Access Denied</h2>
            </div>

            <div class="error-body">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <p class="error-description">
                    You don't have permission to access this resource.
                    Please contact your administrator if you believe this is an error.
                </p>

                <a href="{{ url()->previous() }}" class="btn btn-modern">
                    <i class="fas fa-arrow-left me-2"></i> Back to Previous Page
                </a>
            </div>
        </div>

        @include('SRTDashboard.footer')
    </div>
    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
