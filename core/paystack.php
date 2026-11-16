<?php
// Talks to Paystack from the server. Shared helper, no SQL and no HTML.
require_once __DIR__ . '/paystack_keys.php';

if (!defined('PAYSTACK_API_BASE')) {
    define('PAYSTACK_API_BASE', 'https://api.paystack.co');
}

// Makes a new reference for one checkout. Paystack only accepts
// letters, numbers and - . = in a reference.
function new_payment_reference() {
    return 'SHOPPN-' . time() . '-' . bin2hex(random_bytes(4));
}

// Asks Paystack whether a payment with this reference really succeeded.
// The browser's "success" message is never trusted on its own, because
// anyone can type process_payment_action.php?ref=fake into the address bar.
// Returns ['ok' => true, 'amount' => pesewas, 'currency' => 'GHS']
//      or ['ok' => false, 'error' => '...'].
function verify_paystack_payment($reference) {
    $ch = curl_init(PAYSTACK_API_BASE . '/transaction/verify/' . rawurlencode($reference));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
            'Cache-Control: no-cache',
        ],
    ]);
    $body  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        error_log('Paystack verify failed to connect: ' . $error);
        return ['ok' => false, 'error' => 'Could not reach Paystack to confirm the payment.'];
    }

    $result = json_decode($body, true);
    if (!is_array($result) || empty($result['status']) || !isset($result['data'])) {
        return ['ok' => false, 'error' => 'Paystack could not find this payment.'];
    }

    $data = $result['data'];
    if (($data['status'] ?? '') !== 'success') {
        return ['ok' => false, 'error' => 'Paystack says this payment was not successful.'];
    }
    if (($data['reference'] ?? '') !== $reference) {
        return ['ok' => false, 'error' => 'Payment reference does not match.'];
    }

    return [
        'ok'       => true,
        'amount'   => (int) $data['amount'],
        'currency' => $data['currency'] ?? '',
    ];
}
