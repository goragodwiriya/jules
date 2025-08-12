<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Builder</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <style>
      body {
        font-family: 'Noto Sans Thai', sans-serif;
      }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-ui-checks-grid"></i> Form Builder</a>
        </div>
    </nav>

    <main class="container-fluid mt-4">
        <div class="row">
            <!-- Toolbox Column -->
            <aside class="col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Toolbox</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">คลิกเพื่อเพิ่มฟิลด์ลงในฟอร์มของคุณ</p>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary text-start add-field" data-field-type="text"><i class="bi bi-fonts me-2"></i> Text Input</button>
                            <button type="button" class="btn btn-outline-secondary text-start add-field" data-field-type="email"><i class="bi bi-at me-2"></i> Email</button>
                            <button type="button" class="btn btn-outline-secondary text-start add-field" data-field-type="number"><i class="bi bi-123 me-2"></i> Number</button>
                            <button type="button" class="btn btn-outline-secondary text-start add-field" data-field-type="textarea"><i class="bi bi-textarea-resize me-2"></i> Textarea</button>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Form Canvas Column -->
            <section class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Form Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="form-name" class="form-label">ชื่อฟอร์ม (Form Name)</label>
                            <input type="text" class="form-control" id="form-name" placeholder="เช่น ฟอร์มลงทะเบียน">
                        </div>
                        <hr>
                        <div id="form-canvas" class="p-3 bg-light min-vh-100">
                            <div class="text-center text-muted" id="form-canvas-placeholder">
                                <p>พื้นที่สำหรับสร้างฟอร์ม</p>
                                <p><i class="bi bi-hand-index-thumb" style="font-size: 2rem;"></i></p>
                                <p>คลิกฟิลด์จาก Toolbox ด้านซ้ายเพื่อเริ่มต้น</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="button" id="save-form" class="btn btn-primary"><i class="bi bi-save me-2"></i> บันทึกฟอร์ม</button>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html>
