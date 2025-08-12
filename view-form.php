<?php
// Get the form ID from the URL and sanitize it to ensure it's an integer.
$form_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If no valid ID is provided, set an error state.
if ($form_id <= 0) {
    http_response_code(400);
    $page_title = "Error";
    $error_message = "Invalid or missing Form ID provided in the URL.";
} else {
    $page_title = "Loading Form...";
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
      .form-container {
        max-width: 700px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }
    </style>
</head>
<body>

    <div class="form-container">
        <div id="form-header">
             <h1 id="form-title" class="mb-4 border-bottom pb-3"><?php echo htmlspecialchars($page_title); ?></h1>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Error!</h4>
                <p><?php echo htmlspecialchars($error_message); ?></p>
                <hr>
                <p class="mb-0">Please check the URL and try again.</p>
            </div>
        <?php else: ?>
            <div id="rendered-form">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading form, please wait...</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const formId = <?php echo json_encode($form_id); ?>;
        if (!formId || formId <= 0) {
            return; // Stop execution if PHP already detected an invalid ID
        }

        const formContainer = document.getElementById('rendered-form');
        const formTitle = document.getElementById('form-title');

        const fetchAndRenderForm = async () => {
            try {
                // This logic correctly determines the base path whether the app
                // is in the root or a subfolder.
                const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
                const apiUrl = `${window.location.origin}${basePath}/api/forms/${formId}`;

                const response = await fetch(apiUrl);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: 'An unknown error occurred.' }));
                    throw new Error(errorData.message || `The server responded with status: ${response.status}`);
                }

                const form = await response.json();

                // --- Update page title and header ---
                document.title = form.name;
                formTitle.textContent = form.name;

                // --- Create the <form> element ---
                const formElement = document.createElement('form');
                // The form will not be submitted via traditional methods.
                // We will handle submission with JS in a future step to show a success message.
                // For now, setting action/method is a good fallback.
                formElement.action = `${basePath}/api/forms/${formId}/submissions`;
                formElement.method = 'POST';
                formElement.id = 'dynamic-form';

                // --- Render fields ---
                if (form.structure && form.structure.fields) {
                    form.structure.fields.forEach(field => {
                        const fieldWrapper = document.createElement('div');
                        fieldWrapper.className = 'mb-3';

                        let fieldHtml = '';
                        const inputId = `field-${field.id}`;
                        // Use field.id as the name attribute to link submissions back to the structure
                        const inputName = field.id;
                        const requiredAttr = field.required ? 'required' : '';

                        fieldHtml += `<label for="${inputId}" class="form-label">${field.label}</label>`;

                        switch (field.type) {
                            case 'text':
                                fieldHtml += `<input type="text" id="${inputId}" name="${inputName}" class="form-control" ${requiredAttr}>`;
                                break;
                            case 'email':
                                fieldHtml += `<input type="email" id="${inputId}" name="${inputName}" class="form-control" ${requiredAttr}>`;
                                break;
                            case 'number':
                                fieldHtml += `<input type="number" id="${inputId}" name="${inputName}" class="form-control" ${requiredAttr}>`;
                                break;
                            case 'textarea':
                                fieldHtml += `<textarea id="${inputId}" name="${inputName}" class="form-control" rows="3" ${requiredAttr}></textarea>`;
                                break;
                            default:
                                fieldHtml += `<input type="text" id="${inputId}" name="${inputName}" class="form-control" ${requiredAttr} disabled value="Unsupported field type: ${field.type}">`;
                        }
                        fieldWrapper.innerHTML = fieldHtml;
                        formElement.appendChild(fieldWrapper);
                    });
                }

                // --- Add submit button ---
                const submitButton = document.createElement('button');
                submitButton.type = 'submit';
                submitButton.className = 'btn btn-primary w-100 py-2';
                submitButton.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Submit Form';
                formElement.appendChild(submitButton);

                // --- Replace loading spinner with the form ---
                formContainer.innerHTML = '';
                formContainer.appendChild(formElement);

            } catch (error) {
                console.error('Failed to render form:', error);
                formTitle.textContent = 'Error Loading Form';
                document.title = 'Error';
                formContainer.innerHTML = `<div class="alert alert-danger"><strong>Error:</strong> ${error.message}</div>`;
            }
        };

        fetchAndRenderForm();
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
