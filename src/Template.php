<?php 

namespace DonationDaddy;

use DonationDaddy\Config;

class Template {

    public static function render(string $site)
    {
        extract(Config::getForSite($site));

        $output = "
            <!DOCTYPE html>
            <html lang='en'>

            <head>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1'>

                <style>
                    *,
                    *::before,
                    *::after {
                        box-sizing: border-box;
                    }

                    html {
                        -moz-text-size-adjust: none;
                        -webkit-text-size-adjust: none;
                        text-size-adjust: none;
                    }

                    body, h1, h2, h3, h4, p,
                    figure, blockquote, dl, dd {
                        margin: 0;
                    }

                    ul[role='list'],
                    ol[role='list'] {
                        list-style: none;
                    }

                    body {
                        line-height: 1.5;
                    }

                    h1, h2, h3, h4,
                    button, input, label {
                        line-height: 1.1;
                    }

                    h1, h2,
                    h3, h4 {
                        text-wrap: balance;
                    }

                    a:not([class]) {
                        text-decoration-skip-ink: auto;
                        color: currentColor;
                    }

                    img,
                    picture {
                        max-width: 100%;
                        display: block;
                    }

                    input, button,
                    textarea, select {
                        font: inherit;
                    }

                    textarea:not([rows]) {
                        min-height: 10em;
                    }

                    :target {
                        scroll-margin-block: 5ex;
                    }

                    body {
                        font-family: Atkinson Hyperlegible, sans-serif;
                        font-size: 1.2em;
                        background-color: #ecf0f1;
                        color: #343a40;
                    }

                    h1 {
                        font-size: 1.5em;
                        margin-bottom: 0;
                        font-family: 'Press Start 2P', display;
                    }

                    .subtitle {
                        margin-top: 5px;
                        margin-bottom: 0;
                    }

                    .wrapper {
                        max-width: 1200px;
                        margin: 0 auto;
                        padding: 10px;
                        margin-top: 10px;
                    }

                    header {
                        text-align: center;
                    }

                    .content {
                        margin-top: 20px;
                    }

                    nav.main {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        grid-gap: 5px;
                        font-weight: 700;
                        font-size: 0.7em;
                        padding: 10px;
                    }

                    @media (max-width: 600px)
                    {
                        nav.main {
                            grid-template-columns: repeat(2, 1fr);
                        }
                    }

                    nav.main a {
                        text-align: center;
                        padding: 5px;
                        text-decoration: none;
                        font-family: 'Press Start 2P', display;
                        border: 2px solid black;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background-color: #fce18c;
                        color: #343a40;
                    }

                    nav.main a:hover {
                        background-color: #FDDB73;
                    }

                    footer {
                        text-align: center;
                        margin-top: 20px;
                    }

                    footer img {
                        display: initial;
                    }

                    .center {
                        text-align: center;
                        margin-bottom: 10px;
                    }

                    .flex {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    $styles
                </style>

                <title>$title - $page_title</title>

                <meta property='og:title' content='$title' />
                <meta property='description' content='$title - $page_title' />
                <meta property='og:description' content='$title - $page_title' />
                <meta property='og:type' content='article' />
                <meta property='og:url' content='$url' />
                <meta property='og:image' content='/icons/$key/preview.png'>

                <link rel='apple-touch-icon' sizes='180x180' href='/icons/$key/apple-touch-icon.png'>
                <link rel='icon' type='image/png' sizes='32x32' href='/icons/$key/favicon-32x32.png'>
                <link rel='icon' type='image/png' sizes='16x16' href='/icons/$key/favicon-16x16.png'>
                <link rel='manifest' href='/icons/$key/site.webmanifest'>
                <link rel='mask-icon' href='/icons/$key/safari-pinned-tab.svg' color='#5bbad5'>
                <meta name='msapplication-TileColor' content='#da532c'>
                <meta name='theme-color' content='#ffffff'>
                <link rel='icon' type='image/x-icon' href='/icons/$key/favicon.ico'>

                <link rel='preconnect' href='https://fonts.bunny.net'>
                <link href='https://fonts.bunny.net/css?family=atkinson-hyperlegible:400,400i,700,700i|press-start-2p:400' rel='stylesheet' />

                <script src='https://cdn.usefathom.com/script.js' data-site='OQIRZXWS' defer></script>

            </head>

            <body>

                <nav class='main'>
                    <a href='https://coinme.dad/dy'>Coin Me, Daddy</a>
                    <a href='https://deskmat.help'>Desk Mat Help</a>
                    <a href='https://hathelp.rknight.me'>Hat Help</a>
                    <a href='https://donationtreats.rknight.me'>Donation Treats</a>
                    <a href='https://septembed.rknight.me'>Septembed</a>
                    <a href='https://stjude.omg.lol'>robb x omg.lol</a>
                </nav>

                <div class='wrapper'>
                    <header>
                        <h1>$title</h1>
                        <p class='subtitle'><em>$tagline</em></p>
                        <p class='subtitle'><small>Made by <a href='https://stjude.omg.lol'>Robb Knight</small></a></p>
                    </header>
                        
                    <div class='content'>
                        $images
                        $countText
                        $emptyState
                        $content
                    </div>

                    <script>
                        $scripts
                    </script>

                    <footer>
                        <a href='https://donationdaddy.rknight.me'><img src='icons/donationdaddy/DonationDaddy.png' height='50'></a>

                    </footer>
                </div>
            </body>

        </html>
        ";

        foreach (Config::REPLACERS as $replacer)
        {
            if ($replacer['replace'] === 'COUNT')
            {
                $output = str_replace($replacer['find'], $count, $output);
            } else {
                $output = str_replace($replacer['find'], $replacer['replace'], $output);
            }
        }

        return $output;

    }

}