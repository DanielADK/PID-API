<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OpeningHoursControllerTest extends WebTestCase {
    public function testGetOpenedPoints(): void {
        $client = static::createClient();

        // Set up mock time and date
        $time = '10:00';
        $date = '2022-12-31';

		// Get the server URL from environment variable
        $serverUrl = getenv('APP_URL');

        // Send request to the endpoint
        $client->request('GET', "$serverUrl/api/point/opened?time=$time&date=$date");
		print_r($client->getResponse()->getContent());

        // Assert that the response status code is 200 (HTTP_OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that the response is a JSON
        $this->assertJson($client->getResponse()->getContent());
    }
}