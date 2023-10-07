<?php

namespace DonationDaddy;

use GuzzleHttp\Client;
use Phpfastcache\Helper\Psr16Adapter;

class Data {
  public static function campaigns()
  {
    return json_decode(file_get_contents('./static/2023/campaigns.json'), true);

    $VANITY = '+relay-fm';
    $SLUG = 'relay-fm-for-st-jude-2023';

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

    $Psr16Adapter->set($key, $data, 300);

    return $data;
  }

  public static function relay()
  {
    return json_decode(file_get_contents('./static/2023/relay.json'), true);

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
      'vanity' => '+relay-fm',
      'slug' => 'relay-fm-for-st-jude-2023',
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