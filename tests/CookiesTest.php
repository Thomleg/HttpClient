<?php
/**
 * This file is part of Berlioz framework.
 *
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2017 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace Berlioz\Http\Client\Tests;

use Berlioz\Http\Client\Cookies;
use Berlioz\Http\Message\Request;
use Berlioz\Http\Message\Response;
use Berlioz\Http\Message\Uri;
use PHPUnit\Framework\TestCase;

class CookiesTest extends TestCase
{
    public function testGetCookiesForUri()
    {
        $cookiesManager = new Cookies();
        $cookiesManager->addCookieRaw('test=value; Expires=Wed, 21 Oct 2050 07:28:00 GMT; Domain=getberlioz.com');
        $cookiesManager->addCookieRaw('test2=value2; Expires=Wed, 21 Oct 2050 07:28:00 GMT; Domain=www.getberlioz.fr');

        {
            $uri = new Uri('http', 'getberlioz.com');
            $this->assertEquals('test=value', $cookiesManager->getCookiesForUri($uri));

            $uri = new Uri('http', 'www.getberlioz.com');
            $this->assertEquals('test=value', $cookiesManager->getCookiesForUri($uri));

            $uri = new Uri('http', 'getberlioz.fr');
            $this->assertEquals('', $cookiesManager->getCookiesForUri($uri));

            $uri = new Uri('http', 'www.getberlioz.fr');
            $this->assertEquals('test2=value2', $cookiesManager->getCookiesForUri($uri));
        }

        $cookiesManager->addCookieRaw('secured=value2; Expires=Wed, 21 Oct 2050 07:28:00 GMT; Domain=getberlioz.com; Secure');

        {
            $uri = new Uri('http', 'getberlioz.com');
            $this->assertEquals('test=value', $cookiesManager->getCookiesForUri($uri));

            $uri = new Uri('https', 'getberlioz.com');
            $this->assertEquals('test=value; secured=value2', $cookiesManager->getCookiesForUri($uri));

            $uri = new Uri('https', 'getberlioz.fr');
            $this->assertEquals('', $cookiesManager->getCookiesForUri($uri));
        }
    }

    public function testAddCookiesFromResponse()
    {
        $cookiesManager = new Cookies();

        $uri = new Uri('http', 'getberlioz.com');
        $response = new Response(null,
                                 200,
                                 ['Set-Cookie' =>
                                      ['test=value; Expires=Wed, 21 Oct 2050 07:28:00 GMT; Domain=getberlioz.com',
                                       'test2=value2; Expires=Wed, 21 Oct 2050 07:28:00 GMT; Domain=www.getberlioz.fr',
                                       'test3=value3; Expires=Wed, 21 Oct 2050 07:28:00 GMT']]);

        $cookiesManager->addCookiesFromResponse($uri, $response);
        $this->assertEquals('test=value; test3=value3', $cookiesManager->getCookiesForUri($uri));

        $uri = new Uri('http', 'www.getberlioz.fr');
        $this->assertEquals('test2=value2', $cookiesManager->getCookiesForUri($uri));
    }

    public function testAddCookiesToRequest()
    {
        $cookiesManager = new Cookies();
        $cookiesManager->addCookieRaw('test=value; Expires=Wed, 21 Oct 2050 07:28:00 GMT; Domain=getberlioz.com');
        $cookiesManager->addCookieRaw('secured=value2; Expires=Wed, 21 Oct 2050 07:28:00 GMT; Domain=getberlioz.com; Secure');
        $cookiesManager->addCookieRaw('expired=value; Expires=Wed, 21 Oct 2000 07:28:00 GMT; Domain=getberlioz.com');

        $uri = new Uri('http', 'getberlioz.com');
        $request = new Request('post', $uri);
        $request = $cookiesManager->addCookiesToRequest($request);
        $this->assertEquals(['test=value'], $request->getHeader('Cookie'));

        $uri = new Uri('https', 'getberlioz.com');
        $request = new Request('post', $uri);
        $request = $cookiesManager->addCookiesToRequest($request);
        $this->assertEquals(['test=value; secured=value2'], $request->getHeader('Cookie'));
    }
}