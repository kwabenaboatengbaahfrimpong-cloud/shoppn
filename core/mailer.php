<?php
// Sends the order receipt by email with PHP's mail().
// If the server has no mail set up, mail() returns false and the order
// still stands. The receipt page is the main proof of purchase.
function send_order_receipt($to_email, $to_name, $order) {
    $lines = [];
    $lines[] = 'Hi ' . $to_name . ',';
    $lines[] = '';
    $lines[] = 'Thank you for your order at Shoppn.';
    $lines[] = 'Invoice no: ' . $order['invoice_no'];
    $lines[] = 'Date: ' . $order['order_date'];
    $lines[] = '';
    foreach ($order['items'] as $item) {
        $lines[] = $item['qty'] . ' x ' . $item['product_title'] . ' @ GHS '
                 . number_format($item['product_price'], 2) . ' = GHS '
                 . number_format($item['qty'] * $item['product_price'], 2);
    }
    $lines[] = '';
    $lines[] = 'Total paid: GHS ' . number_format($order['amt'], 2);
    $lines[] = '';
    $lines[] = 'Shoppn';

    $subject = 'Your Shoppn receipt - invoice ' . $order['invoice_no'];
    $headers = "From: Shoppn <no-reply@shoppn.local>\r\nContent-Type: text/plain; charset=UTF-8";

    return @mail($to_email, $subject, implode("\n", $lines), $headers);
}
