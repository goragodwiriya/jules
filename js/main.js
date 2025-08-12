document.addEventListener('DOMContentLoaded', () => {

    const toolboxButtons = document.querySelectorAll('.add-field');
    const formCanvas = document.getElementById('form-canvas');
    const formCanvasPlaceholder = document.getElementById('form-canvas-placeholder');
    const saveFormButton = document.getElementById('save-form');
    const formNameInput = document.getElementById('form-name');

    // --- Function to add a new field to the canvas ---
    const addField = (fieldType) => {
        // Remove placeholder if it exists
        if (document.getElementById('form-canvas-placeholder')) {
            document.getElementById('form-canvas-placeholder').remove();
        }

        const fieldId = `field_${Date.now()}`;
        const wrapper = document.createElement('div');
        wrapper.className = 'card mb-3 p-3 field-wrapper';
        wrapper.dataset.fieldId = fieldId;
        wrapper.dataset.fieldType = fieldType;

        let fieldHtml = '';
        // In a real app, this label would be editable. For now, it's a placeholder.
        const label = `New ${fieldType.charAt(0).toUpperCase() + fieldType.slice(1)} Field`;

        switch (fieldType) {
            case 'text':
                fieldHtml = `<label class="form-label">${label}</label><input type="text" class="form-control" disabled>`;
                break;
            case 'email':
                fieldHtml = `<label class="form-label">${label}</label><input type="email" class="form-control" disabled>`;
                break;
            case 'number':
                fieldHtml = `<label class="form-label">${label}</label><input type="number" class="form-control" disabled>`;
                break;
            case 'textarea':
                fieldHtml = `<label class="form-label">${label}</label><textarea class="form-control" disabled></textarea>`;
                break;
        }

        wrapper.innerHTML = `
            <div class="field-content">
                ${fieldHtml}
            </div>
            <div class="field-actions text-end mt-2">
                <button type="button" class="btn btn-danger btn-sm delete-field">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        `;

        formCanvas.appendChild(wrapper);
    };

    // --- Event Listeners ---

    // 1. Add field buttons
    toolboxButtons.forEach(button => {
        button.addEventListener('click', () => {
            const fieldType = button.getAttribute('data-field-type');
            addField(fieldType);
        });
    });

    // 2. Delete field buttons (using event delegation)
    formCanvas.addEventListener('click', (event) => {
        const deleteButton = event.target.closest('.delete-field');
        if (deleteButton) {
            const fieldWrapper = event.target.closest('.field-wrapper');
            if (fieldWrapper) {
                fieldWrapper.remove();
                // If canvas is empty, show placeholder again
                if (formCanvas.children.length === 0) {
                    formCanvas.innerHTML = `
                    <div class="text-center text-muted" id="form-canvas-placeholder">
                        <p>พื้นที่สำหรับสร้างฟอร์ม</p>
                        <p><i class="bi bi-hand-index-thumb" style="font-size: 2rem;"></i></p>
                        <p>คลิกฟิลด์จาก Toolbox ด้านซ้ายเพื่อเริ่มต้น</p>
                    </div>`;
                }
            }
        }
    });

    // 3. Save form button
    saveFormButton.addEventListener('click', async () => {
        const formName = formNameInput.value.trim();
        if (!formName) {
            alert('Please enter a name for the form.');
            formNameInput.focus();
            return;
        }

        const fields = [];
        const fieldElements = formCanvas.querySelectorAll('.field-wrapper');

        if (fieldElements.length === 0) {
            alert('Please add at least one field to the form.');
            return;
        }

        fieldElements.forEach(el => {
            const labelElement = el.querySelector('.form-label');
            fields.push({
                id: el.dataset.fieldId,
                type: el.dataset.fieldType,
                label: labelElement ? labelElement.textContent : 'Untitled'
                // In a real app, we'd also save placeholder, required status, etc.
            });
        });

        const formData = {
            name: formName,
            // The plan specified `structure` which is a good idea
            structure: {
                fields: fields
            }
        };

        // API Call
        try {
            const basePath = window.location.pathname.replace(/index\.php$/, '').replace(/\/+$/, '');
            const apiUrl = `${window.location.origin}${basePath}/api/forms`;

            saveFormButton.disabled = true;
            saveFormButton.innerHTML = '<i class="bi bi-arrow-repeat"></i> Saving...';

            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            // Check if response is JSON, otherwise show raw text
            const contentType = response.headers.get("content-type");
            if (!response.ok) {
                let errorMessage = `HTTP Error: ${response.status}`;
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    const errorJson = await response.json();
                    errorMessage = errorJson.message || JSON.stringify(errorJson);
                } else {
                    errorMessage = await response.text();
                }
                throw new Error(errorMessage);
            }

            const result = await response.json();

            alert(`Form saved successfully!\n\nView Link: view-form.php?id=${result.id}\nResponses Link: view-responses.php?id=${result.id}`);
            window.location.reload(); // Reload to clear the builder

        } catch (error) {
            console.error('Error saving form:', error);
            alert(`An error occurred while saving the form:\n${error.message}\n\nCheck the console for more details. Is the backend API running?`);
        } finally {
             saveFormButton.disabled = false;
             saveFormButton.innerHTML = '<i class="bi bi-save me-2"></i> บันทึกฟอร์ม';
        }
    });
});
