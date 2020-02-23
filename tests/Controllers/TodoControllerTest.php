<?php

namespace Test\Controllers;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Faker;

class TodoControllerTest extends TestCase
{
    protected $serverDomain = 'http://todo.local';

    /**
     * Test index running
     */
    public function testIndexPage()
    {
        $client = new Client();

        $res = $client->request('GET', $this->serverDomain);

        $this->assertEquals(200, $res->getStatusCode());
    }

    /**
     * Test add page running
     */
    public function testAddPage()
    {
        $client = new Client();

        $res = $client->request('GET', $this->serverDomain . '/add');

        $this->assertEquals(200, $res->getStatusCode());
    }

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

    /**
     * Ajax store data
     */
    public function testAddStoreData()
    {
        $client = new Client();

        $faker = Faker\Factory::create();

        $todo = [
            'name' => $faker->streetName,
            'start' => $faker->dateTimeBetween('-15 days', 'now')->format('Y-m-d'),
            'end' => $faker->dateTimeBetween('now', '+15 days')->format('Y-m-d'),
            'status' => $faker->numberBetween(0, 2)
        ];

        /**
         * Form post
         */
        $res = $client->request('post', $this->serverDomain . '/add', [
            'json' => $todo,
            'http_errors' => false
        ]);

        $this->assertEquals(404, $res->getStatusCode());

        /**
         * Json post without requested with
         */
        $res = $client->request('post', $this->serverDomain . '/add', [
            'json' => $todo,
            'http_errors' => false,
            'headers' => ['Accept: application/json']
        ]);

        $this->assertEquals(404, $res->getStatusCode());

        /**
         * Json post with requested with
         */
        $res = $client->request('post', $this->serverDomain . '/add', [
            'json' => $todo,
            'headers' => [
                'Accept: application/json',
                'X-Requested-With' => 'ILoveDeveloper'
            ],
        ]);

        $this->assertEquals(200, $res->getStatusCode());
    }
}