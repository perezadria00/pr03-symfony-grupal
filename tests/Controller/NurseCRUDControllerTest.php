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
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Failed asserting GET /nurse/index returns 200. Response: " . $response->getContent()
        );

        $data = json_decode($response->getContent(), true);
        $this->assertCount(4, $data['nurses'], "Expected 4 nurses, found: " . count($data['nurses']));
    }

    public function testCreateNurseWithValidData(): void
    {
        $client = static::createClient();
        $client->request('POST', '/nurse/new', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'user' => 'new_nurse',
            'password' => 'newpassword123',
            'name' => 'New',
            'surname' => 'Nurse'
        ]));

        $response = $client->getResponse();
        $this->assertEquals(
            Response::HTTP_CREATED,
            $response->getStatusCode(),
            "Failed asserting POST /nurse/new returns 201. Response: " . $response->getContent()
        );
        $this->assertJson($response->getContent());
    }

    public function testCreateNurseWithInvalidData(): void
    {
        $client = static::createClient();
        $client->request('POST', '/nurse/new', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'user' => 'invalid_nurse'
        ]));

        $response = $client->getResponse();
        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $response->getStatusCode(),
            "Failed asserting POST /nurse/new with invalid data returns 400. Response: " . $response->getContent()
        );
        $this->assertJson($response->getContent());
    }

    public function testShowNurseById(): void
    {
        $client = static::createClient();
        $client->request('GET', '/nurse/1'); // Cambiar ID según las fixtures

        $response = $client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Failed asserting GET /nurse/1 returns 200. Response: " . $response->getContent()
        );
        $this->assertJson($response->getContent());
    }

    public function testEditNurse(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/nurse/1/edit', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Updated Name'
        ]));

        $response = $client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Failed asserting PUT /nurse/1/edit returns 200. Response: " . $response->getContent()
        );
    }

    public function testDeleteNurse(): void
    {
        $client = static::createClient();
    
        // Crear un enfermero para garantizar que existe en la base de datos
        $client->request('POST', '/nurse/new', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'user' => 'delete_test',
            'password' => 'deletepass',
            'name' => 'Delete',
            'surname' => 'Test'
        ]));
    
        // Obtener el ID del enfermero recién creado
        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);
        $nurseId = $data['nurse']['id'] ?? null;
    
        // Asegúrate de que se haya creado correctamente
        $this->assertNotNull($nurseId, "Failed to create a nurse for the DELETE test.");
    
        // Eliminar el enfermero
        $client->request('DELETE', '/nurse/' . $nurseId);
    
        $response = $client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Failed asserting DELETE /nurse/{$nurseId} returns 200. Response: " . $response->getContent()
        );
    
        // Verificar que el enfermero fue eliminado
        $client->request('GET', '/nurse/' . $nurseId);
        $response = $client->getResponse();
        $this->assertEquals(
            Response::HTTP_NOT_FOUND,
            $response->getStatusCode(),
            "Failed asserting GET /nurse/{$nurseId} after delete returns 404. Response: " . $response->getContent()
        );
    }
    

    public function testFindNurseByNameAndSurname(): void
    {
        $client = static::createClient();
        $client->request('GET', '/nurse/name/Test/User');

        $response = $client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Failed asserting GET /nurse/name/Test/User returns 200. Response: " . $response->getContent()
        );
        $this->assertJson($response->getContent());
    }

    public function testLoginNurse(): void
    {
        $client = static::createClient();
        $client->request('GET', '/nurse/login/nurse_test/password123');

        $response = $client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "Failed asserting GET /nurse/login/nurse_test/password123 returns 200. Response: " . $response->getContent()
        );
        $this->assertJson($response->getContent());
    }
}
