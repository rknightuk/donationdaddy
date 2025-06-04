<?php

require '../../vendor/autoload.php';

use DonationDaddy\Data;

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");

function getRelay($devMode = false)
{
    $data = Data::relay();

    $milestones = $data['milestones'] ?? [];
    $nextMilestone = null;
    usort($milestones, function ($a, $b) {
        return $a['amount'] <=> $b['amount'];
    });

    foreach ($milestones as $milestone) {
        if ($milestone['amount'] > $data['raised']) {
            $nextMilestone = $milestone;
            break;
        }
    }

    if ($devMode) {
        $nextMilestone = [
            'name' => 'Big Milestone',
            'amount' => 1000,
        ];
    }

    return [
        'title' => $data['name'],
        'url' => $data['url'],
        'goal' => '$' . $data['goal'],
        'raised' => '$' . $data['raised'],
        'percentage' => $data['percentage'] ?? 0,
        'avatar' => $data['avatar'] ?? null,
        'milestone' => $nextMilestone ? [
            'name' => $nextMilestone['name'],
            'amount' => '$' . $nextMilestone['amount'],
        ] : null,
    ];
}

$slug = $_GET['slug'] ?? null;
$vanity = $_GET['vanity'] ?? null;
$devMode = $_GET['dev_mode'] ?? null;

$data = [
    'relay' => getRelay($devMode),
    'campaign' => null,
];

if (is_null($vanity) || is_null($slug)) {
    echo json_encode($data);
    die;
}

$campaignData = Data::getCampaignData($vanity, $slug);

if (isset($campaignData['errors'])) {
    echo json_encode($data);
    die;
}

$goal = $campaignData['data']['campaign']['goal']['value'];
$raised = $campaignData['data']['campaign']['totalAmountRaised']['value'];
$currency = '$';

$milestones = $campaignData['data']['campaign']['milestones'] ?? [];
$nextMilestone = null;
usort($milestones, function ($a, $b) {
    return $a['amount']['value'] <=> $b['amount']['value'];
});

foreach ($milestones as $milestone) {
    if ($milestone['amount']['value'] > $raised) {
        $nextMilestone = $milestone;
        break;
    }
}

if ($devMode) {
    $nextMilestone = [
        'name' => 'Test Milestone',
        'amount' => ['value' => 1000],
    ];
}

$formatted = [
    'title' => $campaignData['data']['campaign']['name'],
    'url' => sprintf('https://tiltify.com/%s/%s', $vanity, $slug),
    'goal' => $currency . $goal,
    'raised' => $currency . $raised,
    'percentage' => ($goal > 0 && $raised > 0) ? number_format((($raised / $goal) * 100), 2) : 0,
    'avatar' => $campaignData['data']['campaign']['avatar']['src'] ?? null,
    'milestone' => $nextMilestone ? [
        'name' => $nextMilestone['name'],
        'amount' => '$' . $nextMilestone['amount']['value'],
    ] : null,
];

$data['campaign'] = $formatted;

echo json_encode($data);
