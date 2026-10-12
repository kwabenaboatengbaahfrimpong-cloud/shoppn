<?php
require_once __DIR__ . '/../../core/core.php';
require_admin(); // before any HTML is sent
require_once __DIR__ . '/../../controllers/ProductController.php';

$controller = new ProductController();
$categories = $controller->getAllCategories();

// ?edit_id=N switches the form into edit mode
$editing = false;
if (isset($_GET['edit_id'])) {
    $edit_id = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT);
    $editing = $edit_id ? $controller->getCategoryById($edit_id) : false;
}
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<main class="admin-page">
    <h1>Categories</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="banner-error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="banner-success"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form id="category-form" class="admin-form"
          action="../../actions/<?php echo $editing ? 'update_category_action.php' : 'add_category_action.php'; ?>"
          method="POST" novalidate>
        <?php if ($editing): ?>
            <input type="hidden" name="cat_id" value="<?php echo (int) $editing['cat_id']; ?>">
        <?php endif; ?>

        <div class="field">
            <label for="cat_name"><?php echo $editing ? 'Edit category name' : 'New category name'; ?></label>
            <input type="text" name="cat_name" id="cat_name" maxlength="100"
                   value="<?php echo $editing ? htmlspecialchars($editing['cat_name']) : ''; ?>" required>
            <span class="field-error" id="category-error"></span>
        </div>

        <button type="submit"><?php echo $editing ? 'Update category' : 'Add category'; ?></button>
        <?php if ($editing): ?>
            <a class="link-button" href="category.php">Cancel</a>
        <?php endif; ?>
    </form>

    <table class="admin-table">
        <thead>
            <tr><th>ID</th><th>Category</th><th></th></tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr><td colspan="3">No categories yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?php echo (int) $c['cat_id']; ?></td>
                    <td><?php echo htmlspecialchars($c['cat_name']); ?></td>
                    <td><a class="link-button" href="category.php?edit_id=<?php echo (int) $c['cat_id']; ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script>
// quick check before the form leaves the browser (the PHP action checks again)
document.getElementById('category-form').addEventListener('submit', function (e) {
    var name = document.getElementById('cat_name').value.trim();
    var err = document.getElementById('category-error');
    err.textContent = '';
    if (name.length === 0) {
        err.textContent = 'Enter a category name';
        e.preventDefault();
    }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
