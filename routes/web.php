<?php

use Illuminate\Support\Facades\Route;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

Route::get('/metrics', function (CollectorRegistry $registry) {
    $renderer = new RenderTextFormat();
    return response($renderer->render($registry->getMetricFamilySamples()))
        ->header('Content-Type', RenderTextFormat::MIME_TYPE);
});

Route::get('/', function () {
    $delay = random_int(1, 5);  // Random delay between 1 and 5 seconds
    sleep($delay);
    return 'Hello world!';
});
