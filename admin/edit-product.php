<?php
require_once '../includes/functions.php';

require_admin();

$id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));

$st = $conn->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
$st->bind_param('i', $id);
$st->execute();
$product = $st->get_result()->fetch_assoc();

if (!$product) {
    redirect('products.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name              = trim($_POST['name'] ?? '');
    $category          = trim($_POST['category'] ?? '');
    $subcategory       = trim($_POST['subcategory'] ?? '');
    $price             = (float)($_POST['price'] ?? 0);
    $stock             = (int)($_POST['stock'] ?? 0);
    $short_description = trim($_POST['short_description'] ?? '');
    $description       = trim($_POST['description'] ?? '');
    $featured          = isset($_POST['is_featured']) ? 1 : 0;

    // Check if new image was uploaded from PC
    $uploadError = null;
    $uploadedFilename = handle_product_image_upload($_FILES['image_file'] ?? null, $uploadError);

    if ($uploadError) {
        $error = $uploadError;
    } elseif ($uploadedFilename) {
        $image = $uploadedFilename;
    } else {
        // Fallback to text input or retain existing image
        $image = trim($_POST['image'] ?? '') !== '' ? trim($_POST['image']) : $product['image'];
    }

    if ($name === '' || $category === '' || $subcategory === '' || $price <= 0 || empty($image) || $description === '') {
        if (!$error) {
            $error = 'Please complete all required fields.';
        }
    } else {
        $st = $conn->prepare(
            'UPDATE products 
             SET name = ?, category = ?, subcategory = ?, price = ?, stock = ?, image = ?, short_description = ?, description = ?, is_featured = ? 
             WHERE id = ?'
        );
        $st->bind_param(
            'sssdisssii',
            $name,
            $category,
            $subcategory,
            $price,
            $stock,
            $image,
            $short_description,
            $description,
            $featured,
            $id
        );
        $st->execute();

        flash('notice', 'Product updated successfully.');
        redirect('products.php');
    }
}

$existingAssets = get_available_asset_images();

$pageTitle = 'Edit Product | GadgetKart';
$basePath  = '../';

require '../includes/header.php';
?>

<!-- Edit Product Section -->
<section class="section">
    <div class="container narrow">
        <div class="form-card">
            <span class="eyebrow">ADMIN</span>
            <h1>Edit Product</h1>

            <?php if ($error): ?>
                <div class="alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="form-two">
                    <label>
                        Product name
                        <input name="name" required value="<?= e($product['name']) ?>">
                    </label>
                    <label>
                        Price (₹)
                        <input name="price" type="number" step="0.01" required value="<?= e($product['price']) ?>">
                    </label>
                </div>

                <div class="form-two">
                    <label>
                        Category
                        <select name="category" required>
                            <option value="Mobile Accessories" <?= $product['category'] === 'Mobile Accessories' ? 'selected' : '' ?>>Mobile Accessories</option>
                            <option value="Computer Accessories" <?= $product['category'] === 'Computer Accessories' ? 'selected' : '' ?>>Computer Accessories</option>
                            <option value="Gaming" <?= $product['category'] === 'Gaming' ? 'selected' : '' ?>>Gaming</option>
                        </select>
                    </label>
                    <label>
                        Subcategory
                        <input name="subcategory" required value="<?= e($product['subcategory']) ?>">
                    </label>
                </div>

                <div class="form-two">
                    <label>
                        Stock Quantity
                        <input name="stock" type="number" min="0" required value="<?= e($product['stock']) ?>">
                    </label>
                    <div style="display:flex; flex-direction:column; justify-content:center;">
                        <label class="check" style="margin-top: 22px;">
                            <input type="checkbox" name="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?>>
                            <span>Show on home page (Featured)</span>
                        </label>
                    </div>
                </div>

                <!-- Product Image Section -->
                <div class="image-upload-container">
                    <label style="margin-bottom: 2px;">
                        Product Image (Current & Replace from PC)
                    </label>

                    <div class="image-upload-box" id="dropzoneBox">
                        <input 
                            type="file" 
                            name="image_file" 
                            id="imageFileInput" 
                            accept="image/png, image/jpeg, image/jpg, image/webp, image/gif, image/svg+xml"
                            style="display: none;"
                        >

                        <!-- Currently saved image preview card -->
                        <div class="preview-card" id="currentSavedPreview">
                            <div class="preview-img-box">
                                <img src="../assets/images/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
                            </div>
                            <div class="preview-meta">
                                <strong><?= e($product['image']) ?></strong>
                                <small>Currently active image in database assets</small>
                                <div><span class="badge-ready">✓ Current Saved Image</span></div>
                            </div>
                        </div>

                        <!-- Dropzone Area to replace -->
                        <div class="upload-dropzone" id="uploadDropzone" onclick="document.getElementById('imageFileInput').click()" style="margin-top: 10px;">
                            <div class="upload-icon-circle">🔄</div>
                            <div class="upload-title">
                                <span>Choose new image from PC to replace</span> or drag & drop
                            </div>
                            <div class="upload-subtext">
                                PNG, JPG, JPEG, WEBP, GIF, SVG (Up to 8MB) • Leave blank to keep current image
                            </div>
                            <div>
                                <span class="btn-browse">📂 Choose New Image File</span>
                            </div>
                        </div>

                        <!-- Live Selected New Image Preview -->
                        <div class="preview-card" id="selectedPreview" style="display: none; border-color: var(--primary);">
                            <div class="preview-img-box">
                                <img id="previewImg" src="" alt="New image preview">
                            </div>
                            <div class="preview-meta">
                                <strong id="previewFileName">filename.png</strong>
                                <small id="previewFileSize">0 KB</small>
                                <div><span class="badge-ready" style="background:#eeeaff; color:var(--primary);">★ New replacement image selected</span></div>
                            </div>
                            <button type="button" class="preview-remove-btn" id="btnRemoveImage" title="Cancel replacement">
                                ✕ Cancel
                            </button>
                        </div>

                        <!-- Existing Asset Library Option -->
                        <details class="existing-asset-details" id="existingAssetDetails">
                            <summary>⚙️ Or change filename to another existing asset in library</summary>
                            <div class="existing-asset-fields">
                                <input 
                                    name="image" 
                                    id="existingImageInput" 
                                    placeholder="e.g. <?= e($product['image']) ?>" 
                                    list="existingAssetsList"
                                    value="<?= e($product['image']) ?>"
                                >
                                <datalist id="existingAssetsList">
                                    <?php foreach ($existingAssets as $asset): ?>
                                        <option value="<?= e($asset) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                                <small>If a new file is chosen from your PC, it will be uploaded and stored automatically.</small>
                            </div>
                        </details>
                    </div>
                </div>

                <label>
                    Short description
                    <input name="short_description" required value="<?= e($product['short_description']) ?>">
                </label>

                <label>
                    Full description
                    <textarea name="description" rows="5" required><?= e($product['description']) ?></textarea>
                </label>

                <button class="btn" type="submit">Update Product</button>
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
});
</script>

<?php require '../includes/footer.php'; ?>
