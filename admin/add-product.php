<?php
require_once '../includes/functions.php';

require_admin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name              = trim($_POST['name'] ?? '');
    $category          = trim($_POST['category'] ?? '');
    $subcategory       = trim($_POST['subcategory'] ?? '');
    $price             = $_POST['price'] ?? '';
    $stock             = $_POST['stock'] ?? '';
    $short_description = trim($_POST['short_description'] ?? '');
    $description       = trim($_POST['description'] ?? '');
    $featured          = isset($_POST['is_featured']) ? 1 : 0;

    // Process image: Upload from PC / System takes priority
    $uploadError = null;
    $uploadedFilename = handle_product_image_upload($_FILES['image_file'] ?? null, $uploadError);

    if ($uploadError) {
        $error = $uploadError;
    } elseif ($uploadedFilename) {
        $image = $uploadedFilename;
    } else {
        // Fallback to chosen or existing image filename
        $image = trim($_POST['image'] ?? '');
    }

    if (
        $name === '' ||
        $category === '' ||
        $subcategory === '' ||
        !is_numeric($price) ||
        !is_numeric($stock) ||
        empty($image) ||
        $description === ''
    ) {
        if (!$error) {
            if (empty($image)) {
                $error = 'Please choose an image file from your PC or select an existing asset image.';
            } else {
                $error = 'Please complete all required fields.';
            }
        }
    } else {
        $price = (float)$price;
        $stock = (int)$stock;

        $st = $conn->prepare(
            'INSERT INTO products (name, category, subcategory, price, stock, image, short_description, description, is_featured) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $st->bind_param(
            'sssdisssi',
            $name,
            $category,
            $subcategory,
            $price,
            $stock,
            $image,
            $short_description,
            $description,
            $featured
        );
        $st->execute();

        flash('notice', 'Product added successfully with persistent image asset.');
        redirect('products.php');
    }
}

$existingAssets = get_available_asset_images();

$pageTitle = 'Add Product | GadgetKart';
$basePath  = '../';

require '../includes/header.php';
?>

<!-- Add Product Section -->
<section class="section">
    <div class="container narrow">
        <div class="form-card">
            <span class="eyebrow">ADMIN</span>
            <h1>Add Product</h1>

            <?php if ($error): ?>
                <div class="alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-two">
                    <label>
                        Product name
                        <input name="name" required placeholder="e.g. Wireless RGB Mechanical Keyboard" value="<?= e($_POST['name'] ?? '') ?>">
                    </label>
                    <label>
                        Price (₹)
                        <input name="price" type="number" step="0.01" required placeholder="2499" value="<?= e($_POST['price'] ?? '') ?>">
                    </label>
                </div>

                <div class="form-two">
                    <label>
                        Category
                        <select name="category" required>
                            <option value="Mobile Accessories" <?= (($_POST['category'] ?? '') === 'Mobile Accessories') ? 'selected' : '' ?>>Mobile Accessories</option>
                            <option value="Computer Accessories" <?= (($_POST['category'] ?? '') === 'Computer Accessories') ? 'selected' : '' ?>>Computer Accessories</option>
                            <option value="Gaming" <?= (($_POST['category'] ?? '') === 'Gaming') ? 'selected' : '' ?>>Gaming</option>
                        </select>
                    </label>
                    <label>
                        Subcategory
                        <input name="subcategory" placeholder="e.g. Chargers, Keyboards, Audio" required value="<?= e($_POST['subcategory'] ?? '') ?>">
                    </label>
                </div>

                <div class="form-two">
                    <label>
                        Stock Quantity
                        <input name="stock" type="number" min="0" required value="<?= e($_POST['stock'] ?? '10') ?>">
                    </label>
                    <div style="display:flex; flex-direction:column; justify-content:center;">
                        <label class="check" style="margin-top: 22px;">
                            <input type="checkbox" name="is_featured" <?= !empty($_POST['is_featured']) ? 'checked' : '' ?>>
                            <span>Show on home page (Featured)</span>
                        </label>
                    </div>
                </div>

                <!-- Product Image Upload Section -->
                <div class="image-upload-container">
                    <label style="margin-bottom: 2px;">
                        Product Image (Upload from PC / System)
                    </label>

                    <div class="image-upload-box" id="dropzoneBox">
                        <input 
                            type="file" 
                            name="image_file" 
                            id="imageFileInput" 
                            accept="image/png, image/jpeg, image/jpg, image/webp, image/gif, image/svg+xml"
                            style="display: none;"
                        >

                        <!-- Dropzone Area -->
                        <div class="upload-dropzone" id="uploadDropzone" onclick="document.getElementById('imageFileInput').click()">
                            <div class="upload-icon-circle">📁</div>
                            <div class="upload-title">
                                <span>Choose image from PC</span> or drag and drop here
                            </div>
                            <div class="upload-subtext">
                                Supported formats: PNG, JPG, JPEG, WEBP, GIF, SVG (Up to 8MB)
                            </div>
                            <div>
                                <span class="btn-browse">📂 Browse Files from Computer</span>
                            </div>
                        </div>

                        <!-- Live Selected Image Preview -->
                        <div class="preview-card" id="selectedPreview" style="display: none;">
                            <div class="preview-img-box">
                                <img id="previewImg" src="" alt="Selected product image preview">
                            </div>
                            <div class="preview-meta">
                                <strong id="previewFileName">filename.png</strong>
                                <small id="previewFileSize">0 KB</small>
                                <div><span class="badge-ready">✓ Ready to store persistently</span></div>
                            </div>
                            <button type="button" class="preview-remove-btn" id="btnRemoveImage" title="Remove selected image">
                                ✕ Change
                            </button>
                        </div>

                        <!-- Existing Asset Library Option -->
                        <details class="existing-asset-details" id="existingAssetDetails">
                            <summary>⚙️ Or choose from existing asset images in library</summary>
                            <div class="existing-asset-fields">
                                <input 
                                    name="image" 
                                    id="existingImageInput" 
                                    placeholder="Select or type existing asset filename (e.g. keyboard.png)" 
                                    list="existingAssetsList"
                                    value="<?= e($_POST['image'] ?? '') ?>"
                                >
                                <datalist id="existingAssetsList">
                                    <?php foreach ($existingAssets as $asset): ?>
                                        <option value="<?= e($asset) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                                <small>Note: If you select a file from your PC above, it will be uploaded and stored automatically in the assets directory.</small>
                            </div>
                        </details>
                    </div>
                </div>

                <label>
                    Short description
                    <input name="short_description" placeholder="Brief summary of the product (shown in product cards)" required value="<?= e($_POST['short_description'] ?? '') ?>">
                </label>

                <label>
                    Full description
                    <textarea name="description" rows="5" placeholder="Detailed product specifications, features, warranty..." required><?= e($_POST['description'] ?? '') ?></textarea>
                </label>

                <button class="btn" type="submit">Save Product</button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('imageFileInput');
    const dropzoneBox = document.getElementById('dropzoneBox');
    const uploadDropzone = document.getElementById('uploadDropzone');
    const selectedPreview = document.getElementById('selectedPreview');
    const previewImg = document.getElementById('previewImg');
    const previewFileName = document.getElementById('previewFileName');
    const previewFileSize = document.getElementById('previewFileSize');
    const btnRemoveImage = document.getElementById('btnRemoveImage');
    const existingInput = document.getElementById('existingImageInput');

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function displayFile(file) {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewFileName.textContent = file.name;
            previewFileSize.textContent = formatBytes(file.size);
            uploadDropzone.style.display = 'none';
            selectedPreview.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            displayFile(this.files[0]);
        }
    });

    btnRemoveImage.addEventListener('click', function (e) {
        e.stopPropagation();
        fileInput.value = '';
        previewImg.src = '';
        selectedPreview.style.display = 'none';
        uploadDropzone.style.display = 'flex';
    });

    // Drag and drop events
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzoneBox.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropzoneBox.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzoneBox.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropzoneBox.classList.remove('dragover');
        });
    });

    dropzoneBox.addEventListener('drop', function (e) {
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            displayFile(e.dataTransfer.files[0]);
        }
    });

    // If existing input is filled and no file picked, sync preview if valid asset
    if (existingInput && existingInput.value.trim() !== '') {
        const details = document.getElementById('existingAssetDetails');
        if (details) details.open = true;
    }
});
</script>

<?php require '../includes/footer.php'; ?>
