<?php

declare(strict_types=1);

namespace App\Tests\Functional\Rest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HealthControllerTest extends WebTestCase
{
    public function testHealthEndpointReturnsOk(): void
    {
        $client = self::createClient();

        $client->request('GET', '/health');

        self::assertResponseIsSuccessful();
        $response = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertSame('ok', $response['status']);
    }
}
