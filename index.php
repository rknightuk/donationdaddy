<?php

require 'vendor/autoload.php';

use DonationDaddy\Config;
use DonationDaddy\Template;

$site = $_SERVER['HTTP_HOST'];

switch ($site) {
    case 'coinme.dad':
        $key = Config::SITE_COINME;
        break;
    case 'deskmat.help':
        $key = Config::SITE_DESKMAT;
        break;
    case 'donationtreats.rknight.me':
        $key = Config::SITE_TREAT;
        break;
    case 'septembed.rknight.me':
        $key = Config::SITE_SEPT;
        break;
    case 'hathelp.rknight.me':
        $key = Config::SITE_500;
        break;
    default:
        $key = Config::SITE_DD;
}

header("Content-Type: text/HTML");

echo Template::render($key);