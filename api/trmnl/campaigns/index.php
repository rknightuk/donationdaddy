<?php

require '../../../vendor/autoload.php';

use DonationDaddy\Data;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

$data = array_map(function($campaign) {
    return [
        '@' . $campaign['user'] . ' / ' . $campaign['name'],
        $campaign['url'],
    ];
}, Data::campaigns());

usort($data, fn($a, $b) => strcmp($a[0], $b[0]));

$newData = [];

foreach ($data as $d) {
    $newData[] = [$d[0] => $d[1]];
}

echo json_encode($newData);
