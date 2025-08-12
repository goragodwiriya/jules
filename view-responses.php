<?php
// Get the form ID from the URL and sanitize it.
$form_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($form_id <= 0) {
    http_response_code(400);
    $page_title = "Error";
    $error_message = "Invalid or missing Form ID.";
} else {
    $page_title = "Loading Responses...";
    $error_message = null;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Google Fonts & Bootstrap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
      body {
        font-family: 'Noto Sans Thai', sans-serif;
        background-color: #f8f9fa;
      }
      .container {
        margin-top: 40px;
      }
    </style>
</head>
<body>

    <div class="container">
        <div id="responses-header">
            <h1 id="form-title" class="mb-2"><?php echo htmlspecialchars($page_title); ?></h1>
            <p id="response-count" class="text-muted"></p>
        </div>
        <hr>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php else: ?>
            <div id="responses-table-container" class="table-responsive">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading responses...</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const formId = <?php echo json_encode($form_id); ?>;
        if (!formId || formId <= 0) return;

        const container = document.getElementById('responses-table-container');
        const titleEl = document.getElementById('form-title');
        const countEl = document.getElementById('response-count');

        const fetchAndRenderResponses = async () => {
            try {
                const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
                const apiUrl = `${window.location.origin}${basePath}/api/forms/${formId}/submissions`;

                const response = await fetch(apiUrl);
                if (!response.ok) {
                    const err = await response.json();
                    throw new Error(err.message || 'Failed to fetch data.');
                }

                const data = await response.json();
                const form = data.form;
                const submissions = data.submissions;

                document.title = `Responses for ${form.name}`;
                titleEl.textContent = `Responses for "${form.name}"`;
                countEl.textContent = `${submissions.length} response(s)`;

                if (submissions.length === 0) {
                    container.innerHTML = '<div class="alert alert-info">No responses have been submitted for this form yet.</div>';
                    return;
                }

                const table = document.createElement('table');
                table.className = 'table table-striped table-bordered table-hover';

                // --- Create Table Header ---
                const thead = document.createElement('thead');
                thead.className = 'table-dark';
                let headerHtml = '<tr><th>Submission ID</th><th>Submitted At</th>';
                const fieldOrder = form.structure.fields.map(f => f.id);
                form.structure.fields.forEach(field => {
                    headerHtml += `<th>${field.label}</th>`;
                });
                headerHtml += '</tr>';
                thead.innerHTML = headerHtml;
                table.appendChild(thead);

                // --- Create Table Body ---
                const tbody = document.createElement('tbody');
                submissions.forEach(sub => {
                    let rowHtml = `<tr><td>${sub.id}</td><td>${new Date(sub.submitted_at).toLocaleString()}</td>`;
                    // Ensure data is displayed in the same order as headers
                    fieldOrder.forEach(fieldId => {
                        const value = sub.data[fieldId] || ''; // Use empty string for missing data
                        rowHtml += `<td>${escapeHtml(value.toString())}</td>`;
                    });
                    rowHtml += '</tr>';
                    tbody.innerHTML += rowHtml;
                });
                table.appendChild(tbody);

                container.innerHTML = '';
                container.appendChild(table);

            } catch (error) {
                console.error('Error rendering responses:', error);
                titleEl.textContent = 'Error';
                container.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
            }
        };

        function escapeHtml(unsafe) {
            return unsafe
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }

        fetchAndRenderResponses();
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
