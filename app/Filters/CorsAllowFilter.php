<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Always allow Angular local origin for API calls.
 * Deploy this + Filters.php change to primiumadmin.qzenddev.com
 */
class CorsAllowFilter implements FilterInterface
{
    private const ALLOWED_ORIGINS = [
        'http://localhost:4200',
        'http://127.0.0.1:4200',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $origin = (string) $request->getHeaderLine('Origin');

        if (strtolower($request->getMethod()) === 'options') {
            $response = service('response');
            $this->applyCorsHeaders($response, $origin);

            return $response->setStatusCode(204);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $origin = (string) $request->getHeaderLine('Origin');
        $this->applyCorsHeaders($response, $origin);

        return $response;
    }

    private function applyCorsHeaders(ResponseInterface $response, string $origin): void
    {
        if ($origin !== '' && (
            in_array($origin, self::ALLOWED_ORIGINS, true)
            || preg_match('#\Ahttps?://(localhost|127\.0\.0\.1)(:\d+)?\z#', $origin)
            || preg_match('#\Ahttps?://([a-z0-9-]+\.)?qzenddev\.com\z#i', $origin)
        )) {
            $allowOrigin = $origin;
        } elseif ($origin !== '') {
            $allowOrigin = $origin;
        } else {
            $allowOrigin = '*';
        }

        $response->setHeader('Access-Control-Allow-Origin', $allowOrigin);
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->setHeader(
            'Access-Control-Allow-Headers',
            'Content-Type, Authorization, X-Requested-With, Accept, Origin'
        );
        $response->setHeader('Access-Control-Max-Age', '7200');
        $response->setHeader('Vary', 'Origin');
    }
}
