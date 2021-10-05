<?php
/**
 * Frontend Routes
 * 
 * Define cache TTL in seconds
 * 
 * @see conf/config.json
 * 
 * $ttl->off: 0 (Disabled)
 * $ttl->short: 300
 * $ttl->medium: 900
 * $ttl->long: 1800
 * $ttl->images: 86400
 * 
 */
$f3->map(
    '/',
    'pages\ContentResource',
    ($ttl->debug === true ? $ttl->off : $ttl->short)
);
$f3->map(
    '/show',
    'pages\ShowResource',
    ($ttl->debug === true ? $ttl->off : $ttl->short)
);
$f3->map(
    '/about',
    'pages\AboutResource',
    ($ttl->debug === true ? $ttl->off : $ttl->long)
);
$f3->map(
    '/search',
    'pages\SearchResource',
    ($ttl->debug === true ? $ttl->off : $ttl->short)
);
$f3->map(
    '/results',
    'pages\ResultsResource',
    ($ttl->debug === true ? $ttl->off : $ttl->short)
);
$f3->map(
    '/view',
    'pages\ViewResource',
    ($ttl->debug === true ? $ttl->off : $ttl->short)
);
$f3->map(
    '/terms',
    'pages\TermsResource',
    ($ttl->debug === true ? $ttl->off : $ttl->short)
);
