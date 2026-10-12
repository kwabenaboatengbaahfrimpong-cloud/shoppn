<?php
require_once __DIR__ . '/../../core/core.php';
require_admin(); // before any HTML is sent
require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController();
$categories = $controller->getAllCategories();
$brands     = $controller->getAllBrands();
$products   = $controller->getAllProducts();

// ?edit_id=N switches the form into edit mode and pre-fills it
$editing = false;
if (isset($_GET['edit_id'])) {
    $edit_id = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT);
    $editing = $edit_id ? $controller->getProductById($edit_id) : false;
}
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="admin-page">
    <h1>Products</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="banner-error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="banner-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (empty($categories) || empty($brands)): ?>
        <div class="banner-error">Add at least one category and one brand before adding a product.</div>
    <?php endif; ?>

    <form id="product-form" class="admin-form"
          action="../../actions/<?php echo $editing ? 'update_product_action.php' : 'add_product_action.php'; ?>"
          method="POST" enctype="multipart/form-data" novalidate>

        <?php if ($editing): ?>
            <input type="hidden" name="product_id" value="<?php echo (int) $editing['product_id']; ?>">
        <?php endif; ?>

        <div class="field">
            <label for="product_title">Product title</label>
            <input type="text" name="product_title" id="product_title" maxlength="200"
                   value="<?php echo $editing ? htmlspecialchars($editing['product_title']) : ''; ?>" required>
            <span class="field-error" id="title-error"></span>
        </div>

        <div class="field">
            <label for="product_price">Price (GHS)</label>
            <input type="text" name="product_price" id="product_price"
                   value="<?php echo $editing ? htmlspecialchars($editing['product_price']) : ''; ?>" required>
            <span class="field-error" id="price-error"></span>
        </div>

        <div class="field">
            <label for="product_cat">Category</label>
            <select name="product_cat" id="product_cat" required>
                <option value="">Choose a category</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo (int) $c['cat_id']; ?>"
                        <?php echo ($editing && (int) $editing['product_cat'] === (int) $c['cat_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['cat_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="cat-error"></span>
        </div>

        <div class="field">
            <label for="product_brand">Brand</label>
            <select name="product_brand" id="product_brand" required>
                <option value="">Choose a brand</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?php echo (int) $b['brand_id']; ?>"
                        <?php echo ($editing && (int) $editing['product_brand'] === (int) $b['brand_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($b['brand_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="brand-error"></span>
        </div>

        <div class="field">
            <label for="product_desc">Description</label>
            <textarea name="product_desc" id="product_desc" rows="4" maxlength="500"><?php echo $editing ? htmlspecialchars($editing['product_desc']) : ''; ?></textarea>
        </div>

        <div class="field">
            <label for="product_keywords">Keywords (used by search)</label>
            <input type="text" name="product_keywords" id="product_keywords" maxlength="100"
                   value="<?php echo $editing ? htmlspecialchars($editing['product_keywords']) : ''; ?>">
        </div>

        <div class="field">
            <label for="product_image">Product image <?php echo $editing ? '(leave empty to keep the current one)' : ''; ?></label>
            <?php if ($editing && $editing['product_image']): ?>
                <img class="current-image" src="../../images/products/<?php echo htmlspecialchars($editing['product_image']); ?>" alt="Current product image">
            <?php endif; ?>
            <input type="file" name="product_image" id="product_image" accept="image/jpeg,image/png,image/gif,image/webp">
            <span class="field-error" id="image-error"></span>
        </div>

        <button type="submit"><?php echo $editing ? 'Update product' : 'Add product'; ?></button>
        <?php if ($editing): ?>
            <a class="link-button" href="product.php">Cancel</a>
        <?php endif; ?>
    </form>

    <table class="admin-table">
        <thead>
            <tr><th>Image</th><th>Title</th><th>Category</th><th>Brand</th><th>Price</th><th></th></tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="6">No products yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><img class="thumb" src="../../images/products/<?php echo htmlspecialchars($p['product_image']); ?>" alt=""></td>
                    <td><?php echo htmlspecialchars($p['product_title']); ?></td>
                    <td><?php echo htmlspecialchars($p['cat_name']); ?></td>
                    <td><?php echo htmlspecialchars($p['brand_name']); ?></td>
                    <td>GHS <?php echo number_format($p['product_price'], 2); ?></td>
                    <td><a class="link-button" href="product.php?edit_id=<?php echo (int) $p['product_id']; ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script>
// Client-side checks are only for a quick message. The PHP action checks
// everything again because a browser check can be skipped.
var isEditing = <?php echo $editing ? 'true' : 'false'; ?>;
var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
var maxSize = 2 * 1024 * 1024; // 2 MB

document.getElementById('product-form').addEventListener('submit', function (e) {
    var ok = true;
    document.querySelectorAll('.field-error').forEach(function (el) { el.textContent = ''; });

    var title = document.getElementById('product_title').value.trim();
    var price = document.getElementById('product_price').value.trim();
    var cat = document.getElementById('product_cat').value;
    var brand = document.getElementById('product_brand').value;
    var file = document.getElementById('product_image').files[0];

    if (title.length === 0) {
        document.getElementById('title-error').textContent = 'Enter a title';
        ok = false;
    }
    if (price === '' || isNaN(price) || Number(price) <= 0) {
        document.getElementById('price-error').textContent = 'Enter a price greater than 0';
        ok = false;
    }
    if (cat === '') {
        document.getElementById('cat-error').textContent = 'Choose a category';
        ok = false;
    }
    if (brand === '') {
        document.getElementById('brand-error').textContent = 'Choose a brand';
        ok = false;
    }
    if (!file && !isEditing) {
        document.getElementById('image-error').textContent = 'Choose an image';
        ok = false;
    }
    if (file && allowedTypes.indexOf(file.type) === -1) {
        document.getElementById('image-error').textContent = 'Only JPG, PNG, GIF or WEBP images';
        ok = false;
    }
    if (file && file.size > maxSize) {
        document.getElementById('image-error').textContent = 'Image must be 2 MB or smaller';
        ok = false;
    }

    if (!ok) e.preventDefault();
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
