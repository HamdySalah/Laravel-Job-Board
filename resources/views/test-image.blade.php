<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Image Display</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Test Image Display</h3>
                    </div>
                    <div class="card-body">
                        <h5>Company Logo</h5>
                        <div class="mb-4">
                            <p>Direct URL:</p>
                            <img src="{{ asset('storage/company_logos/ms98ekltgQ1ABdqHXQOvtl6c8Qqc2v24SgEdH5Ic.png') }}" alt="Company Logo" class="img-fluid mb-2">
                            <p class="text-muted">URL: {{ asset('storage/company_logos/ms98ekltgQ1ABdqHXQOvtl6c8Qqc2v24SgEdH5Ic.png') }}</p>
                        </div>

                        <h5>Storage Information</h5>
                        <div class="mb-4">
                            <p>APP_URL: {{ env('APP_URL') }}</p>
                            <p>FILESYSTEM_DISK: {{ env('FILESYSTEM_DISK') }}</p>
                            <p>Storage Path: {{ storage_path('app/public/company_logos') }}</p>
                            <p>Public Path: {{ public_path('storage/company_logos') }}</p>
                        </div>

                        <h5>File Exists Check</h5>
                        <div class="mb-4">
                            <p>Storage File Exists: {{ file_exists(storage_path('app/public/company_logos/ms98ekltgQ1ABdqHXQOvtl6c8Qqc2v24SgEdH5Ic.png')) ? 'Yes' : 'No' }}</p>
                            <p>Public File Exists: {{ file_exists(public_path('storage/company_logos/ms98ekltgQ1ABdqHXQOvtl6c8Qqc2v24SgEdH5Ic.png')) ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
