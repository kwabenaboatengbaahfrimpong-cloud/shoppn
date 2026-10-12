<?php
require_once __DIR__ . '/../../core/core.php';
require_admin(); // before any HTML is sent
require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController();
$brands = $controller->getAllBrands();

// ?edit_id=N switches the form into edit mode
$editing = false;
if (isset($_GET['edit_id'])) {
    $edit_id = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT);
    $editing = $edit_id ? $controller->getBrandById($edit_id) : false;
}
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="admin-page">
    <h1>Brands</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="banner-error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="banner-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form id="brand-form" class="admin-form"
          action="../../actions/<?php echo $editing ? 'update_brand_action.php' : 'add_brand_action.php'; ?>"
          method="POST" novalidate>
        <?php if ($editing): ?>
            <input type="hidden" name="brand_id" value="<?php echo (int) $editing['brand_id']; ?>">
        <?php endif; ?>

        <div class="field">
            <label for="brand_name"><?php echo $editing ? 'Edit brand name' : 'New brand name'; ?></label>
            <input type="text" name="brand_name" id="brand_name" maxlength="100"
                   value="<?php echo $editing ? htmlspecialchars($editing['brand_name']) : ''; ?>" required>
            <span class="field-error" id="brand-error"></span>
        </div>

        <button type="submit"><?php echo $editing ? 'Update brand' : 'Add brand'; ?></button>
        <?php if ($editing): ?>
            <a class="link-button" href="brand.php">Cancel</a>
        <?php endif; ?>
    </form>

    <table class="admin-table">
        <thead>
            <tr><th>ID</th><th>Brand</th><th></th></tr>
        </thead>
        <tbody>
            <?php if (empty($brands)): ?>
                <tr><td colspan="3">No brands yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($brands as $b): ?>
                <tr>
                    <td><?php echo (int) $b['brand_id']; ?></td>
                    <td><?php echo htmlspecialchars($b['brand_name']); ?></td>
                    <td><a class="link-button" href="brand.php?edit_id=<?php echo (int) $b['brand_id']; ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script>
// quick check before the form leaves the browser (the PHP action checks again)
document.getElementById('brand-form').addEventListener('submit', function (e) {
    var name = document.getElementById('brand_name').value.trim();
    var err = document.getElementById('brand-error');
    err.textContent = '';
    if (name.length === 0) {
        err.textContent = 'Enter a brand name';
        e.preventDefault();
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
