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
        'percentage' => $data['percentage'],
    ];
}

function getData($variables)
{
    $operationName = 'get_campaign_by_vanity_and_slug';
    $query = 'query get_campaign_by_vanity_and_slug($vanity: String!, $slug: String!) {
  campaign(vanity: $vanity, slug: $slug) {
    publicId
    legacyCampaignId
    name
    slug
    status
    showPolyline
    fitnessTotals {
      averagePaceMinutesMile
      averagePaceMinutesKilometer
      totalDistanceMiles
      totalDurationSeconds
      totalDistanceKilometers
      __typename
    }
    fitnessDailyActivity {
      date
      totalDistanceMiles
      totalDistanceKilometers
      __typename
    }
    fitnessActivities(first: 10) {
      edges {
        node {
          distanceMiles
          distanceKilometers
          durationSeconds
          elevationGainFeet
          elevationGainMeters
          id
          paceMinutesMile
          paceMinutesKilometer
          startDate
          obfuscatedPolyline
          fitnessActivityType {
            type
            __typename
          }
          __typename
        }
        __typename
      }
      __typename
    }
    fitnessGoals {
      currentValue
      goal
      type
      __typename
    }
    fitnessSettings {
      measurementUnit
      __typename
    }
    membership {
      id
      status
      __typename
    }
    originalGoal {
      value
      currency
      __typename
    }
    region {
      name
      __typename
    }
    team {
      id
      avatar {
        src
        alt
        __typename
      }
      name
      slug
      __typename
    }
    supportingAuctionHouses(first: 5) {
      edges {
        node {
          publicId
          name
          avatar {
            src
            __typename
          }
          link
          description
          user {
            id
            username
            __typename
          }
          __typename
        }
        __typename
      }
      __typename
    }
    bonfireCampaign {
      id
      description
      featuredItemImage {
        src
        __typename
      }
      featuredItemName
      featuredItemPrice {
        currency
        value
        __typename
      }
      url
      products {
        id
        productType
        sellingPrice {
          value
          currency
          __typename
        }
        __typename
      }
      __typename
    }
    supportedTeamEvent {
      publicId
      team {
        id
        avatar {
          src
          alt
          __typename
        }
        name
        slug
        __typename
      }
      avatar {
        alt
        height
        width
        src
        __typename
      }
      name
      slug
      currentSlug
      __typename
    }
    description
    totalAmountRaised {
      currency
      value
      __typename
    }
    goal {
      currency
      value
      __typename
    }
    avatar {
      alt
      height
      width
      src
      __typename
    }
    user {
      id
      username
      slug
      avatar {
        src
        alt
        __typename
      }
      __typename
    }
    livestream {
      type
      channel
      __typename
    }
    milestones {
      publicId
      name
      amount {
        value
        currency
        __typename
      }
      __typename
    }
    schedules {
      publicId
      name
      description
      startsAt
      endsAt
      __typename
    }
    rewards {
      active
      promoted
      fulfillment
      amount {
        currency
        value
        __typename
      }
      name
      image {
        src
        __typename
      }
      fairMarketValue {
        currency
        value
        __typename
      }
      legal
      description
      publicId
      startsAt
      endsAt
      quantity
      remaining
      __typename
    }
    challenges {
      publicId
      amount {
        currency
        value
        __typename
      }
      name
      active
      endsAt
      amountRaised {
        currency
        value
        __typename
      }
      __typename
    }
    polls {
      active
      amountRaised(vanity: $vanity, slug: $slug) {
        currency
        value
        __typename
      }
      totalAmountRaised {
        currency
        value
        __typename
      }
      name
      publicId
      pollOptions {
        name
        publicId
        amountRaised(vanity: $vanity, slug: $slug) {
          currency
          value
          __typename
        }
        totalAmountRaised {
          currency
          value
          __typename
        }
        __typename
      }
      __typename
    }
    cause {
      id
      publicId
      name
      slug
      description
      avatar {
        alt
        height
        width
        src
        __typename
      }
      paymentMethods {
        type
        currency
        sellerId
        minimumAmount {
          currency
          value
          __typename
        }
        __typename
      }
      paymentOptions {
        currency
        additionalDonorDetails
        additionalDonorDetailsType
        monthlyGiving
        monthlyGivingMinimumAmount
        minimumAmount
        __typename
      }
      __typename
    }
    fundraisingEvent {
      publicId
      legacyFundraisingEventId
      name
      slug
      avatar {
        alt
        height
        width
        src
        __typename
      }
      paymentMethods {
        type
        currency
        sellerId
        minimumAmount {
          currency
          value
          __typename
        }
        __typename
      }
      paymentOptions {
        currency
        additionalDonorDetails
        additionalDonorDetailsType
        monthlyGiving
        minimumAmount
        __typename
      }
      __typename
    }
    donationMatches {
      publicId
      startsAt
      endsAt
      startedAtAmount {
        value
        currency
        __typename
      }
      totalAmountRaised {
        value
        currency
        __typename
      }
      matchedAmountTotalAmountRaised {
        value
        currency
        __typename
      }
      pledgedAmount {
        value
        currency
        __typename
      }
      amount {
        value
        currency
        __typename
      }
      active
      matchedBy
      __typename
    }
    __typename
  }
}
';

    $response = (new Client)->request('post', 'https://api.tiltify.com/', [
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'body' => json_encode([
            'query' => $query,
            'operationName' => $operationName,
            'variables' => $variables,
        ])
    ]);

    $data = json_decode($response->getBody()->getContents(), true);

    return $data;
}

$slug = $_GET['slug'] ?? null;
$vanity = $_GET['vanity'] ?? null;

$key = str_replace('@', '', $vanity . $slug) ?: 'relayfmmaincampaign';
$Psr16Adapter = new Psr16Adapter('Files');

// return relay always
echo json_encode(getRelay());
die;

if (!$Psr16Adapter->has($key)) {
    if (is_null($vanity) || is_null($slug)) {
        echo json_encode(getRelay());
        die;
    }
    
    $variables = [
        'vanity' => $vanity,
        'slug' => $slug,
    ];

    $data = getData($variables);

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
        'percentage' => ($goal > 0 && $raised > 0) ? number_format((($raised / $goal) * 100), 2) : null,
    ];

    $Psr16Adapter->set($key, $data, 600);
} else {
    $data = $Psr16Adapter->get($key);
}

echo json_encode($data);
