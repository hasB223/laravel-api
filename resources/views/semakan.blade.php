<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semakan No Insolvensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
        }

        .insolvency-form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="insolvency-form-container">
        <h2 class="text-center mb-4">Semakan No Insolvensi</h2>

        <!-- Form to enter Insolvensi number -->
        <form method="POST" action="{{ url('/semak-insolvensi') }}">
            @csrf
            <div class="mb-3">
                <label for="noInsolvensi" class="form-label">No. Insolvensi</label>
                <input type="text" id="noInsolvensi" name="noInsolvensi" class="form-control" required
                       pattern="[A-Za-z0-9/]{1,13}" maxlength="13"
                       oninput="this.value = this.value.toUpperCase()">
                <small class="form-text text-muted">
                    Format: BP000001/2006
                </small>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Semak</button>
            </div>
        </form>

        <!-- Display the API response -->
        @if(session('success'))
            <div class="mt-4">
                <h5>Maklumat Ansuran</h5>
                <table class="table">
                    <tr>
                        <td><strong>No. Insolvensi:</strong></td>
                        <td>{{ session('insolvencyno') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama:</strong></td>
                        <td>{{ session('name') }}</td>
                    </tr>
                    <tr>
                        <td><strong>No. Kad Pengenalan:</strong></td>
                        <td>{{ session('idno') }}</td>
                    </tr>
                </table>
            </div>
        @elseif(session('error'))
            <div class="mt-4 alert alert-danger">
                <p>No. Insolvensi: {{ session('insolvencyno') }}</p>
                <p>Ralat: Tiada rekod bagi {{ session('insolvencyno') }}</p>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
