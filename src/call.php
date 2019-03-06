<?php
require __DIR__ . './src/get_api.php';

function callApi($method, $source)
{
    $callApi = $api->rest($method, $source);

    if ($callApi->errors) {

        echo "Oops! {$callApi->errors->status} error <br>";

        print_r($callApi->errors->body);
    }
    return $callApi;
}
