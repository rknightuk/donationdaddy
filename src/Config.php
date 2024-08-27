<?php 

namespace DonationDaddy;

class Config {

    const SITE_COINME = 'coinme';
    const SITE_DESKMAT = 'deskmat';
    const SITE_TREAT = 'treats';
    const SITE_SEPT = 'septembed';
    const SITE_DD = 'donationdaddy';

    const REPLACERS = [
        [
            'find' => '{{ RELAYLINK }}',
            'replace' => '<a href="https://stjude.tiltify.com/relay-for-st-jude">Relay for St Jude</a>',
        ],
        [
            'find' => '{{ COUNT }}',
            'replace' => 'COUNT',
        ]
    ];

    public static function getForSite(string $site)
    {
        $config = [
            self::SITE_COINME => [
                'key' => self::SITE_COINME,
                'url' => 'https://coinme.dad',
                'title' => 'Coin Me, Daddy',
                'page_title' => 'Help someone out who needs just one dollar to get a Relay for St Jude challenge coin',
                'tagline' => 'Help someone out who needs just one dollar to get a {{ RELAYLINK }} challenge coin',
                'countText' => '<p class="center"><strong>{{ COUNT }} people already raised enough for a coin!</strong></p>',
                'styles' => "
                    .sj-container {
                        padding: 10px 5px 5px 10px;
                        border-radius: 10px;
                        display: block;
                        text-decoration: none;
                        transition:  background 0.5s ease;
                        background:  #FDE18C;
                        color: black;
                        border: 1px solid #FFC52C;
                        margin-bottom: 10px;
                    }

                    .sj-container:hover {
                        background: #FDDB73;
                    }

                    .coins {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-bottom: 20px;
                    }

                    .coin1.empty {
                        animation: spin2 2s linear infinite;
                        cursor: pointer;
                    }

                    .coin2.empty {
                        animation: spin 2s linear infinite;
                    }

                    .coin1:hover {
                        animation: spin2 1s linear infinite;
                        cursor: pointer;
                    }

                    .coin2:hover {
                        animation: spin 1s linear infinite;
                        cursor: pointer;
                    }

                    @keyframes spin {
                        from {
                            transform: rotate(0deg);
                        }
                        to {
                            transform: rotate(360deg);
                        }
                    }

                    @keyframes spin2 {
                        from {
                            transform: rotate(0deg);
                        }
                        to {
                            transform: rotate(-360deg);
                        }
                    }

                ",
                'formatter' => function ($data) {
                    $count = 0;
                    $data = array_filter($data, function($d) use (&$count) {
                        if ($d['raised'] >= 1) $count++;
                        return $d['raised'] < 1;
                    });

                    $content = implode('', array_map(function ($c) {
                        return sprintf(
                            '<a class="sj-container" href="%s" target="_blank">
                                <p class="sj-title" style="margin-top: 0px; margin-bottom: 5px; font-weight: bold;">%s<br style="margin-bottom:5px;"></p>
                            </a>',
                            $c['url'],
                            $c['name'] . ' - @' . $c['user'],
                        );
                    }, $data));

                    return [
                        $content,
                        $count,
                    ];
                },
                'images' => "
                    <div class='coins'>
                        <img id='coin1' class='coin1' style='max-width:50%;max-height:250px;' src='icons/coinme/coin1-trim.png'>
                        <img id='coin2' class='coin2' style='max-width:50%;max-height:250px;' src='icons/coinme/coin2-trim.png'>
                    </div>
                ",
                'emptyState' => "",
                // 'emptyState' => "
                //     <p class='center'>🎉 Every campaign has earned at least one dollar and a coin! 🎉</p>
                //     <p class='center'>💸 Why not help someone out who needs a <a href='https://deskmat.help'>desk mat?</a> 💸</p>
                // ",
                'scripts' => "
                    (function() {
                        isEmpty = !Array.from(document.getElementsByClassName('sj-container')).length

                        if (isEmpty)
                        {
                            document.getElementById('coin1').className += ' empty'
                            document.getElementById('coin2').className += ' empty'
                        }

                        run = function() {
                            if (isEmpty)
                            {
                                window.open('https://deskmat.help', '_blank')
                                return
                            }
                            links = Array.from(document.getElementsByClassName('sj-container')).map(x => x.href)
                            link = links[(Math.floor(Math.random() * links.length)) - 1]
                            setTimeout(() => {
                                window.open(link, '_blank')
                            }, 1000)
                        }
                        document.getElementById('coin1').addEventListener('click', (e) => {
                                e.preventDefault()
                                run()
                        })

                        document.getElementById('coin2').addEventListener('click', (e) => {
                                e.preventDefault()
                                run()
                        })
                    })();
                ",
            ],
            self::SITE_DESKMAT => [
                'key' => self::SITE_DESKMAT,
                'url' => 'https://deskmat.help',
                'title' => 'Desk Mat Help',
                'page_title' => 'Help someone out who is close to getting a Relay FM for St Jude desk mat',
                'tagline' => 'Help someone out who is close to getting a {{ RELAYLINK }} desk mat',
                'formatter' => function ($data) {
                    $count = 0;
                    $data = array_filter($data, function ($d) use (&$count) {
                        if ($d['raised'] >= 250) $count++;
                        return $d['raised'] < 250;
                    });

                    $data = array_map(function($d) {
                        $raised = (float) $d['raised'];
                        $goal = $d['goal'];
                        $d['raised'] = $raised;
                        $d['goal'] = $goal;
                        $d['percentage'] = ($goal > 0 && $raised > 0) ? number_format((($raised / 250) * 100), 2) : null;
                        $d['left'] = number_format(250 - $raised, 2);

                        return $d;
                    }, $data);

                    usort($data, function ($a, $b) {
                        if ($a['raised'] == $b['raised']) return 0;
                        return ($a['raised'] > $b['raised']) ? -1 : 1;
                    });

                    $content = implode('', array_map(function ($c) {
                        return
                        sprintf(
                            '<a class="sj-container" href="%s" target="_blank">
                                <p class="sj-title" style="margin-top: 0px; margin-bottom: 5px; font-weight: bold;">%s<br style="margin-bottom:5px;"></p>
                                <p class="sj-subtitle" style="margin-top: 0px; margin-bottom: 10px;">$%s needed</p>
                                <div style="position: relative; height: 25px; background: rgba(189, 195, 199, 0.6); border-radius: 15px;">
                                <div class="sj-progress" style="width: %s;"></div>
                                <div class="sj-progress-text"> $%s • %s</div>
                                </div>
                            </a>',
                            $c['url'],
                            $c['name'],
                            number_format(250 - $c['raised'], 2),
                            ($c['percentage'] ?? 0) . '%',
                            $c['raised'],
                            ($c['percentage'] ?? 0) . '%',
                        );;
                    }, $data));

                    return [
                        $content,
                        $count,
                    ];
                },
                'styles' => '
                    .sj-container {
                        padding: 10px;
                        border-radius: 10px;
                        display: block;
                        text-decoration: none;
                        transition:  background 0.5s ease;
                        background:  #FDE18C;
                        color: black;
                        border: 1px solid #FFC52C;
                        margin-bottom: 10px;
                    }

                    .sj-container:hover {
                        background: #FDDB73;
                    }

                    .sj-subtitle {
                        font-size: 0.9em;
                    }
                    .sj-progress {
                        box-sizing:border-box;
                        padding-left:10px;
                        height:100%;
                        background:black;
                        border-top-left-radius:15px;
                        border-bottom-left-radius:15px;
                        display:flex;
                        align-items: center;
                        color: white;
                        max-width:100%;
                    }

                    .sj-progress-text {
                        font-size: 0.8em;
                        position: absolute;
                        top: 3px;
                        right: 0;
                        left: 5px;
                        bottom: 0;
                        color: white;
                        text-align: left;
                    }

                    .deskmat {
                        margin: 0 auto;
                        display: flex;
                        max-width: 600px;
                        max-height: auto;
                        margin-bottom: 10px;
                    }
                ',
                'images' => "
                    <div class='flex center deskmat'>
                        <img class='mat'src='icons/deskmat/deskmat.jpg'>
                    </div>
                ",
                'countText' => '<p class="center"><strong>{{ COUNT }} people already raised enough for a desk mat!</strong></p>',
                'emptyState' => '',
                'scripts' => '',
            ],
            self::SITE_TREAT => [
                'key' => self::SITE_TREAT,
                'url' => 'https://donationtreats.rknight.me',
                'title' => 'Donation treats',
                'page_title' => 'Donate some money, get a treat',
                'tagline' => 'Donate some money, get a treat',
                'countText' => '<p class="center"><strong>{{ COUNT }} different rewards available!</strong></p>',
                'formatter' => function ($data) {
                    $exclude = ['Relay Wallpapers and macOS Screensaver', 'Sticker Pack + Digital Bundle', 'No More Chemo Party', 'Family Meal Card', 'Art Supplies', 'Red Wagon', 'Share of Bone Marrow Treatment', '2/3 Share of Chemotherapy Treatment', 'New Toy for Hospital Play Areas'];
                    $count = 0;

                    $rewards = [];

                    foreach ($data as $d)
                    {
                        foreach ($d['rewards'] as $r)
                        {
                            if (!in_array($r['name'], $exclude)) 
                            {
                                $count++;
                                $rewards[] = array_merge($r, [
                                    'name' => $d['name'],
                                    'user' => $d['user'],
                                    'url' => $d['url'],
                                ]);
                            }
                        }
                    }

                    shuffle($rewards);

                    $content = implode('', array_map(function ($c) {
                        return sprintf(
                            "<div class='reward-box'>
                                <div class='reward-image flex'><img src='%s'></div>
                                <p><a href='%s' target='_blank' class='reward-header'>%s - $%s (@%s)</a></p>
                                <p>%s</p>
                            </div>",
                            $c['image'],
                            $c['url'],
                            $c['name'],
                            $c['amount'],
                            $c['user'],
                            $c['description'],
                        );
                    }, $rewards));

                    return [
                        "<div class='reward-wrap'>$content</div>",
                        $count,
                    ];
                },
                'styles' => '
                    .reward-wrap {
                        margin-top: 30px;
                        display: grid;
                        grid-template-columns: repeat(2, 1fr);
                        grid-gap: 20px;
                    }

                    .reward-box {
                        background: #FDE18C;
                        padding: 10px;
                        border: 2px solid #FFC52C;
                    }

                    .reward-image {
                        margin-bottom: 10px;
                    }

                    .reward-image img {
                        max-height: 100px;
                    }
                ',
                'images' => '',
                'emptyState' => '',
                'scripts' => '',
            ],
            self::SITE_SEPT => [
                'key' => self::SITE_SEPT,
                'url' => 'https://septembed.rknight.me',
                'title' => 'Septembed',
                'page_title' => 'Embed your Relay FM for St Jude campaign on your website', 
                'tagline' => 'Embed your {{ RELAYLINK }} campaign on your website', 
                'formatter' => function($data) {
                    $content = "
                        <p>Add this to your page replacing the URL with your campaign url:</p>

                        <pre>&lt;script src='https://septembed.rknight.me/sj.js?u=<strong>https://tiltify.com/@rknightuk/stjude2024</strong>'&gt;&lt;/script&gt;</pre>
                        
                        <p>Example:</p>

                        <br>

                        <script src='/sj.js?u=https://tiltify.com/@rknightuk/stjude2024'></script>

                        <p style='margin-top: 20px;'><em><small>If you get the URL wrong, the embed will fall back to using the main Relay FM campaign. If you always want to show the Relay campaign, don't pass anything to <code>u=</code>.</small></em></p>
                    ";

                    return [
                        $content,
                        '',
                    ];
                },
                'styles' => '',
                'images' => '',
                'countText' => '',
                'emptyState' => '',
                'scripts' => '',
            ],
            self::SITE_DD => [
                'key' => self::SITE_DD,
                'url' => 'https://donationdaddy.rknight.me',
                'title' => 'Donation Daddy',
                'page_title' => 'Be a Donation Daddy Today!',
                'tagline' => 'Be a Donation Daddy today!',
                'formatter' => function($data) {
                    return [
                        "<script src='/sj.js?u=https://tiltify.com/@rknightuk/stjude2024'></script>",
                        ''
                    ];
                },
                'styles' => '',
                'images' => '',
                'countText' => '',
                'emptyState' => '',
                'scripts' => '',
            ],
        ];

        $siteConfig = $config[$site];

        $formatted = $siteConfig['formatter'](Data::campaigns());
        $siteConfig['content'] = $formatted[0];
        $siteConfig['count'] = $formatted[1];

        return $siteConfig;
    }

}