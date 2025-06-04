<?php

require 'vendor/autoload.php';

use DonationDaddy\Data;
use GuzzleHttp\Client;
use Phpfastcache\Helper\Psr16Adapter;

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");

function getRelay()
{
    $data = Data::relay();

    return [
        'title' => $data['name'],
        'url' => $data['url'],
        'goal' => '$' . $data['goal'],
        'raised' => '$' . $data['raised'],
        'percentage' => $data['percentage'] ?? 0,
    ];
}

$slug = $_GET['slug'] ?? null;
$vanity = $_GET['vanity'] ?? null;

if (is_null($vanity) || is_null($slug)) {
    echo json_encode(getRelay());
    die;
}

$data = Data::getCampaignData($vanity, $slug);

if (isset($data['errors'])) {
    echo json_encode(getRelay());
    die;
}

$goal = $data['data']['campaign']['goal']['value'];
$raised = $data['data']['campaign']['totalAmountRaised']['value'];
$currency = '$';

$data = [
    'title' => $data['data']['campaign']['name'],
    'url' => sprintf('https://tiltify.com/%s/%s', $vanity, $slug),
    'goal' => $currency . $goal,
    'raised' => $currency . $raised,
    'percentage' => ($goal > 0 && $raised > 0) ? number_format((($raised / $goal) * 100), 2) : 0,
];

echo json_encode($data);
