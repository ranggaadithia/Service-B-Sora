<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Prometheus\CollectorRegistry;


class PrometheusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    private $registry;
    private $counter;
    private $histogram;
    private $gauge;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
        $this->counter = $registry->getOrRegisterCounter(
            'app',
            'http_requests_total',
            'Total number of HTTP requests',
            ['status', 'path', 'method']
        );

        $this->gauge = $this->registry->getOrRegisterGauge(
            'app',                         // namespace
            'http_active_requests',        // name
            'Number of active HTTP requests', // help text
        );

        $this->histogram = $registry->getOrRegisterHistogram(
            'app',
            'http_request_duration_seconds',
            'HTTP request duration in seconds',
            ['status', 'path', 'method'],
            [0.1, 0.25, 0.5, 1, 2.5, 5]
        );
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->gauge->inc();
        $start = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $start;

        $this->counter->inc([
            'status' => $response->getStatusCode(),
            'path' => $request->path(),
            'method' => $request->method()
        ]);

        $this->histogram->observe(
            $duration,
            [
                'status' => $response->getStatusCode(),
                'path' => $request->path(),
                'method' => $request->method()
            ]
        );

        $this->gauge->dec();

        return $response;
    }
}
