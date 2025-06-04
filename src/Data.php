<?php

namespace DonationDaddy;

use GuzzleHttp\Client;
use Phpfastcache\Helper\Psr16Adapter;

class Data {
  public static function getCampaignData($vanity, $slug)
  {
    $key = str_replace('@', '', $vanity . $slug) ?: 'relayfmmaincampaign';
    $Psr16Adapter = new Psr16Adapter('Files');

    if ($Psr16Adapter->has($key)) {
      return $Psr16Adapter->get($key);
    }

    $variables = [
        'vanity' => $vanity,
        'slug' => $slug,
    ];

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

  public static function campaigns()
  {
    return json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/static/2024/campaigns.json'), true);

    $VANITY = '+relay-for-st-jude';
    $SLUG = 'relay-for-st-jude-2024';

    $Psr16Adapter = new Psr16Adapter('Files');
    $key = 'cache_key_campaigns';

    if ($Psr16Adapter->has($key)) {
      return $Psr16Adapter->get($key);
    }

    $query = 'query get_supporting_campaigns_by_team_event_asc($vanity: String!, $slug: String!, $limit: Int!, $cursor: String) {
teamEvent(vanity: $vanity, slug: $slug) {
publicId
supportingCampaigns(first: $limit, after: $cursor) {
    edges {
    cursor
    node {
        publicId
        name
        description
        user {
        id
        username
        slug
        }
        slug
        avatar {
        alt
        src
        }
        goal {
        value
        currency
        }
        amountRaised {
        value
        currency
        }
        totalAmountRaised {
        value
        currency
        }
        rewards {
        name
        description
        amount {
            value
            currency
        }
        image {
            src
        }
        }
        milestones {
        publicId
        name
        amount {
            value
            currency
        }
        updatedAt
        }
    }
    }
    pageInfo {
    startCursor
    endCursor
    hasNextPage
    hasPreviousPage
    }
}
}
}';

    $operationName = 'get_supporting_campaigns_by_team_event_asc';
    $variables = [
      'vanity' => $VANITY,
      'slug' => $SLUG,
      'limit' => 1000,
    ];

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

    $data = json_decode($response->getBody()->getContents(), true)['data']['teamEvent']['supportingCampaigns']['edges'];

    $data = array_map(function ($d) {
      $raised = $d['node']['totalAmountRaised']['value'];
      $goal = $d['node']['goal']['value'];

      return [
        'name' => $d['node']['name'],
        'user' => $d['node']['user']['slug'],
        'description' => $d['node']['description'],
        'url' => sprintf('https://tiltify.com/@%s/%s', $d['node']['user']['slug'], $d['node']['slug']),
        'avatar' => $d['node']['avatar']['src'],
        'raised' => $raised,
        'goal' => $goal,
        'percentage' => ($goal > 0 && $raised > 0) ? number_format((($raised / $goal) * 100), 2) : null,
        'milestones' => array_map(function ($m) {
          return [
            'name' => $m['name'],
            'amount' => $m['amount']['value'],
          ];
        }, $d['node']['milestones']),
        'rewards' => array_map(function ($m) {
          return [
            'name' => $m['name'],
            'description' => $m['description'],
            'image' => $m['image']['src'],
            'amount' => $m['amount']['value'],
          ];
        }, $d['node']['rewards']),
      ];
    }, $data);
    
    usort($data, function ($a, $b) {
        if ($a['raised'] == $b['raised']) return 0;
        return ($a['raised'] > $b['raised']) ? -1 : 1;
    });

    unset($data[0]);
    $data = array_values($data);

    $Psr16Adapter->set($key, $data, 300);

    return $data;
  }

  public static function relay()
  {

    return json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/static/2024/relay.json'), true);

    $Psr16Adapter = new Psr16Adapter('Files');
    $key = 'cache_key_relay_campaign';

    if ($Psr16Adapter->has($key)) {
      return $Psr16Adapter->get($key);
    }

    $operationName = 'get_team_event_by_vanity_and_slug';
    $query = 'query get_team_event_by_vanity_and_slug($vanity: String!, $slug: String!) {
  teamEvent(vanity: $vanity, slug: $slug) {
    publicId
    legacyCampaignId
    name
    slug
    currentSlug
    status
    templateId
    publishedAt
    supportingCampaignCount
    colors {
      background
    }
    visibility {
      donate
      fePageCampaigns
      goal
      raised
      teamEventStats
      toolkit {
        url
        visible
      }
    }
    parentTeamEvent {
      slug
    }
    originalGoal {
      value
      currency
    }
    supportingType
    team {
      id
      publicId
      avatar {
        src
        alt
      }
      name
      slug
      memberCount
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
      }
      paymentMethods {
        type
        currency
        sellerId
        minimumAmount {
          currency
          value
        }
      }
      paymentOptions {
        currency
        additionalDonorDetails
        additionalDonorDetailsType
        monthlyGiving
        monthlyGivingMinimumAmount
        minimumAmount
      }
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
      }
      paymentMethods {
        type
        currency
        sellerId
        minimumAmount {
          currency
          value
        }
      }
      paymentOptions {
        currency
        additionalDonorDetails
        additionalDonorDetailsType
        monthlyGiving
        minimumAmount
      }
    }
    supporting {
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
        }
        paymentMethods {
          type
          currency
          sellerId
          minimumAmount {
            currency
            value
          }
        }
        paymentOptions {
          currency
          additionalDonorDetails
          additionalDonorDetailsType
          monthlyGiving
          monthlyGivingMinimumAmount
          minimumAmount
        }
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
        }
        paymentMethods {
          type
          currency
          sellerId
          minimumAmount {
            currency
            value
          }
        }
        paymentOptions {
          currency
          additionalDonorDetails
          additionalDonorDetailsType
          monthlyGiving
          minimumAmount
        }
      }
    }
    description
    totalAmountRaised {
      currency
      value
    }
    goal {
      currency
      value
    }
    avatar {
      alt
      height
      width
      src
    }
    banner {
      alt
      height
      width
      src
    }
    livestream {
      type
      channel
    }
    milestones {
      publicId
      name
      amount {
        value
        currency
      }
      updatedAt
    }
    schedules {
      publicId
      name
      description
      startsAt
      endsAt
      updatedAt
    }
    rewards {
      active
      promoted
      fulfillment
      amount {
        currency
        value
      }
      name
      image {
        src
      }
      fairMarketValue {
        currency
        value
      }
      legal
      description
      publicId
      startsAt
      endsAt
      quantity
      remaining
      updatedAt
    }
    challenges {
      publicId
      amount {
        currency
        value
      }
      name
      active
      endsAt
      amountRaised {
        currency
        value
      }
      updatedAt
    }
    updatedAt
  }
}';

    $variables = [
      'vanity' => '+relay-for-st-jude',
      'slug' => 'relay-for-st-jude-2024',
    ];

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

    header('Access-Control-Allow-Origin: *');
    header("Content-Type: application/json");

    $data = json_decode($response->getBody()->getContents(), true)['data']['teamEvent'];

    $raised = $data['totalAmountRaised']['value'];
    $goal = $data['goal']['value'];

    $data = [
      'name' => $data['fundraisingEvent']['name'],
      'user' => 'Relay FM',
      'description' => $data['description'],
      'url' => 'https://relay.experience.stjude.org/',
      'avatar' => $data['avatar']['src'],
      'raised' => $raised,
      'goal' => $goal,
      'percentage' => ($goal > 0 && $raised > 0) ? number_format((($raised / $goal) * 100), 2) : null,
      'milestones' => array_map(function ($m) {
        return [
          'name' => $m['name'],
          'amount' => $m['amount']['value'],
        ];
      }, $data['milestones']),
      'rewards' => array_map(function ($m) {
        return [
          'name' => $m['name'],
          'description' => $m['description'],
          'image' => $m['image']['src'],
          'amount' => $m['amount']['value'],
        ];
      }, $data['rewards']),
    ];

    $Psr16Adapter->set($key, $data, 300);

    return $data;
  }
}