<?php
namespace Tests\Controllers;

use GuzzleHttp\Client;
use Tests\Base\TestBaseCase;

class ErrorControllerTest extends TestBaseCase
{
    /**
     * Test error page running
     */
    public function testErrorPage()
    {
        $client = new Client();

        $res = $client->request('GET', $this->serverDomain . '/randomPage', ['http_errors' => false]);

        $this->assertEquals(404, $res->getStatusCode());

        $res = $client->request('GET', $this->serverDomain . '/anotherPage', ['http_errors' => false]);

        $this->assertEquals(404, $res->getStatusCode());
    }
}