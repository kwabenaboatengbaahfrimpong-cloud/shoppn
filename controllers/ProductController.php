<?php
require_once __DIR__ . '/../classes/ProductClass.php';

// Middleman between the actions/views and ProductClass.
// Views call this, never the class directly. No SQL, no HTML here.
class ProductController {
    private $model;

    public function __construct() {
        $this->model = new ProductClass();
    }

    // ---------- brands ----------

    public function addBrand($name) {
        if ($this->model->brandExists($name)) {
            return ['success' => false, 'error' => 'That brand already exists'];
        }
        if (!$this->model->addBrand($name)) {
            return ['success' => false, 'error' => 'Could not add the brand, try again'];
        }
        return ['success' => true];
    }

    public function getAllBrands() {
        return $this->model->getAllBrands();
    }

    public function getBrandById($id) {
        return $this->model->getBrandById($id);
    }

    public function updateBrand($id, $name) {
        if ($this->model->brandExists($name, $id)) {
            return ['success' => false, 'error' => 'That brand already exists'];
        }
        if (!$this->model->updateBrand($id, $name)) {
            return ['success' => false, 'error' => 'Could not update the brand, try again'];
        }
        return ['success' => true];
    }

    // ---------- categories ----------

    public function addCategory($name) {
        if ($this->model->categoryExists($name)) {
            return ['success' => false, 'error' => 'That category already exists'];
        }
        if (!$this->model->addCategory($name)) {
            return ['success' => false, 'error' => 'Could not add the category, try again'];
        }
        return ['success' => true];
    }

    public function getAllCategories() {
        return $this->model->getAllCategories();
    }

    public function getCategoryById($id) {
        return $this->model->getCategoryById($id);
    }

    public function updateCategory($id, $name) {
        if ($this->model->categoryExists($name, $id)) {
            return ['success' => false, 'error' => 'That category already exists'];
        }
        if (!$this->model->updateCategory($id, $name)) {
            return ['success' => false, 'error' => 'Could not update the category, try again'];
        }
        return ['success' => true];
    }

    // ---------- products ----------
    // The image file itself is moved in the action. Here we only get the filename.

    public function addProduct($cat, $brand, $title, $price, $desc, $image, $keywords) {
        if (!$this->model->addProduct($cat, $brand, $title, $price, $desc, $image, $keywords)) {
            return ['success' => false, 'error' => 'Could not add the product, try again'];
        }
        return ['success' => true];
    }

    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords) {
        if (!$this->model->updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords)) {
            return ['success' => false, 'error' => 'Could not update the product, try again'];
        }
        return ['success' => true];
    }

    public function getProductById($id) {
        return $this->model->getProductById($id);
    }

    public function getAllProducts() {
        return $this->model->getAllProducts();
    }

    public function getFeaturedProducts($limit = 6) {
        return $this->model->getFeaturedProducts($limit);
    }

    public function getProductsByCategory($cat_id) {
        return $this->model->getProductsByCategory($cat_id);
    }

    public function getProductsByBrand($brand_id) {
        return $this->model->getProductsByBrand($brand_id);
    }

    public function searchProducts($query) {
        return $this->model->searchProducts($query);
    }
}
