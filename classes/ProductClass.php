<?php
require_once __DIR__ . '/../core/db_class.php';

// All product, brand and category queries live here.
// No HTML, no $_POST, no echo. Every list method returns an array
// (empty array when nothing is found, never false).
class ProductClass extends Database {

    // ---------- helper ----------

    // runs a prepared SELECT and gives back every row as an array
    private function fetchRows($sql, $types = '', $params = []) {
        $stmt = $this->conn->prepare($sql);
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    // ---------- brands ----------

    public function brandExists($name, $ignore_id = 0) {
        $rows = $this->fetchRows(
            "SELECT brand_id FROM brands WHERE brand_name = ? AND brand_id != ?",
            'si', [$name, $ignore_id]
        );
        return count($rows) > 0;
    }

    public function addBrand($name) {
        $stmt = $this->conn->prepare("INSERT INTO brands (brand_name) VALUES (?)");
        $stmt->bind_param('s', $name);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getAllBrands() {
        return $this->fetchRows("SELECT * FROM brands ORDER BY brand_name ASC");
    }

    public function getBrandById($id) {
        $rows = $this->fetchRows("SELECT * FROM brands WHERE brand_id = ?", 'i', [$id]);
        return $rows ? $rows[0] : false;
    }

    public function updateBrand($id, $name) {
        $stmt = $this->conn->prepare("UPDATE brands SET brand_name = ? WHERE brand_id = ?");
        $stmt->bind_param('si', $name, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // ---------- categories ----------

    public function categoryExists($name, $ignore_id = 0) {
        $rows = $this->fetchRows(
            "SELECT cat_id FROM categories WHERE cat_name = ? AND cat_id != ?",
            'si', [$name, $ignore_id]
        );
        return count($rows) > 0;
    }

    public function addCategory($name) {
        $stmt = $this->conn->prepare("INSERT INTO categories (cat_name) VALUES (?)");
        $stmt->bind_param('s', $name);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getAllCategories() {
        return $this->fetchRows("SELECT * FROM categories ORDER BY cat_name ASC");
    }

    public function getCategoryById($id) {
        $rows = $this->fetchRows("SELECT * FROM categories WHERE cat_id = ?", 'i', [$id]);
        return $rows ? $rows[0] : false;
    }

    public function updateCategory($id, $name) {
        $stmt = $this->conn->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ?");
        $stmt->bind_param('si', $name, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // ---------- products (add / edit) ----------

    public function addProduct($cat, $brand, $title, $price, $desc, $image, $keywords) {
        $stmt = $this->conn->prepare(
            "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('iisdsss', $cat, $brand, $title, $price, $desc, $image, $keywords);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords) {
        $stmt = $this->conn->prepare(
            "UPDATE products
             SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?,
                 product_desc = ?, product_image = ?, product_keywords = ?
             WHERE product_id = ?"
        );
        $stmt->bind_param('iisdsssi', $cat, $brand, $title, $price, $desc, $image, $keywords, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // one product with its category name and brand name (JOIN)
    public function getProductById($id) {
        $rows = $this->fetchRows(
            "SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b ON p.product_brand = b.brand_id
             WHERE p.product_id = ?",
            'i', [$id]
        );
        return $rows ? $rows[0] : false;
    }

    public function getAllProducts() {
        return $this->fetchRows(
            "SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b ON p.product_brand = b.brand_id
             ORDER BY p.product_id DESC"
        );
    }

    // ---------- products (customer side) ----------

    public function getFeaturedProducts($limit = 6) {
        return $this->fetchRows("SELECT * FROM products ORDER BY RAND() LIMIT ?", 'i', [$limit]);
    }

    public function getProductsByCategory($cat_id) {
        return $this->fetchRows("SELECT * FROM products WHERE product_cat = ?", 'i', [$cat_id]);
    }

    public function getProductsByBrand($brand_id) {
        return $this->fetchRows("SELECT * FROM products WHERE product_brand = ?", 'i', [$brand_id]);
    }

    public function searchProducts($query) {
        // escape % and _ so a shopper typing them searches for the characters themselves
        $like = '%' . addcslashes($query, '%_\\') . '%';
        return $this->fetchRows(
            "SELECT * FROM products WHERE product_title LIKE ? OR product_keywords LIKE ?",
            'ss', [$like, $like]
        );
    }
}
