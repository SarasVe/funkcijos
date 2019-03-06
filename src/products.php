<?php
require __DIR__ . './src/call.php';

function products()
{
    $type = 'GET';
    $source = '/admin/products/count.json';

    $call = callApi($type, $source);

    echo nl2br("Number of products in shop: {$call->body->count}.\n");
}
