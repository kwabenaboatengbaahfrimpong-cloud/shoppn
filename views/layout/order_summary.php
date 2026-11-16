<?php
// Read-only table of items and total. Used by checkout, payment and the invoice.
// Expects $summary_items (rows with product_title, product_price, product_image, qty)
// and $summary_total (number).
?>
<table class="admin-table cart-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Unit price</th>
            <th>Line total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($summary_items as $item): ?>
            <tr>
                <td class="cart-product">
                    <img src="<?php echo BASE_URL; ?>/images/products/<?php echo htmlspecialchars($item['product_image']); ?>"
                         alt="<?php echo htmlspecialchars($item['product_title']); ?>">
                    <span><?php echo htmlspecialchars($item['product_title']); ?></span>
                </td>
                <td><?php echo (int) $item['qty']; ?></td>
                <td>GHS <?php echo number_format($item['product_price'], 2); ?></td>
                <td>GHS <?php echo number_format($item['qty'] * $item['product_price'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="subtotal-label">Total</td>
            <td class="subtotal-value">GHS <?php echo number_format($summary_total, 2); ?></td>
        </tr>
    </tfoot>
</table>
