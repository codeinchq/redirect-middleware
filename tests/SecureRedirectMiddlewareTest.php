<?php
//
// +---------------------------------------------------------------------+
// | CODE INC. SOURCE CODE                                               |
// +---------------------------------------------------------------------+
// | Copyright (c) 2018 - Code Inc. SAS - All Rights Reserved.           |
// | Visit https://www.codeinc.fr for more information about licensing.  |
// +---------------------------------------------------------------------+
// | NOTICE:  All information contained herein is, and remains the       |
// | property of Code Inc. SAS. The intellectual and technical concepts  |
// | contained herein are proprietary to Code Inc. SAS are protected by  |
// | trade secret or copyright law. Dissemination of this information or |
// | reproduction of this material  is strictly forbidden unless prior   |
// | written permission is obtained from Code Inc. SAS.                  |
// +---------------------------------------------------------------------+
//
// Author:   Joan Fabrégat <joan@codeinc.fr>
// Date:     28/05/2018
// Time:     14:14
// Project:  RedirectMiddleware
//
declare(strict_types=1);
namespace CodeInc\RedirectMiddleware\Tests;
use CodeInc\MiddlewareTestKit\FakeRequestHandler;
use CodeInc\MiddlewareTestKit\FakeServerRequest;
use CodeInc\RedirectMiddleware\RedirectResponse;
use CodeInc\RedirectMiddleware\SecureRedirectMiddleware;
use PHPUnit\Framework\TestCase;


/**
 * Class SecureRedirectMiddlewareTest
 *
 * @uses SecureRedirectMiddleware
 * @package CodeInc\RedirectMiddleware\Tests
 * @author  Joan Fabrégat <joan@codeinc.fr>
 */
class SecureRedirectMiddlewareTest extends TestCase
{
    private const TEST_URL = 'https://www.example.com';

    /**
     * @throws \Exception
     */
    public function testSimpleRedirect():void
    {
        $middleware = new SecureRedirectMiddleware(bin2hex(random_bytes(16)));

        /** @var RedirectResponse $response */
        $response = $middleware->process(
            FakeServerRequest::getSecureServerRequestWithPath($middleware::DEFAULT_URI_PATH)
                ->withQueryParams([$middleware::DEFAULT_QUERY_PARAMETER => $middleware->encodeUrlJwt(self::TEST_URL)]),
            new FakeRequestHandler()
        );

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals(self::TEST_URL, $response->getRedirectUrl());
    }

    /**
     * @throws \Exception
     */
    public function testJwtEncryption():void
    {
        $middleware = new SecureRedirectMiddleware(bin2hex(random_bytes(16)));
        $jwt = $middleware->encodeUrlJwt(self::TEST_URL);
        self::assertNotEmpty($jwt);
        self::assertNotNull($url = $middleware->decodeUrlJwt($jwt));
        self::assertEquals($url, self::TEST_URL);
    }
}