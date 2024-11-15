<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class NurseCRUDControllerTest extends WebTestCase
{
    public function testGetAllNurses(): void
    {
        $client = static::createClient();
        $client->request('GET', '/nurse/index');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $this->assertJson($response->getContent());
    }

    public function testCreateNurseWithValidData(): void
    {
        $client = static::createClient();
        $client->request('POST', '/nurse/new', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'user' => 'nurse_test',
            'password' => 'password123',
            'name' => 'Test',
            'surname' => 'User'
        ]));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testCreateNurseWithInvalidData(): void
    {
        $client = static::createClient();
        $client->request('POST', '/nurse/new', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'user' => 'nurse_test'
        ]));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testShowNurseById(): void
    {
        $client = static::createClient();
        
        // Aseguramos que existe un registro con ID 1 o cambia el ID según corresponda
        $client->request('GET', '/nurse/3');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $this->assertJson($response->getContent());
    }

    public function testEditNurse(): void
    {
        $client = static::createClient();

        // Aseguramos que el enfermero con ID 1 existe
        $client->request('PUT', '/nurse/3/edit', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Updated Name New'
        ]));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
    }

    public function testDeleteNurse(): void
    {
        $client = static::createClient();

        // Aseguramos que el enfermero con ID 1 existe
        $client->request('DELETE', '/nurse/9');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
    }

    public function testFindNurseByNameAndSurname(): void
    {
        $client = static::createClient();
        $client->request('GET', '/nurse/name/Test/User');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $this->assertJson($response->getContent());
    }

    public function testLoginNurse(): void
    {
        $client = static::createClient();
        $client->request('GET', '/nurse/login/nurse_test/password123');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $this->assertJson($response->getContent());
    }
}
