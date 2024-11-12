<?php

namespace App\Controller;

use App\Entity\Nurse;
use App\Form\NurseType;
use App\Repository\NurseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/nurse')]
final class NurseCRUDController extends AbstractController
{
    //CRUD para entidad Nurse

    //devuelve todos los usuarios
    #[Route('/index', name: 'app_nurse_c_r_u_d_index', methods: ['GET'])]
    public function getAll(NurseRepository $nurseRepository): JsonResponse
    {
        // Obtener todos los enfermeros de la base de datos
        $nurses = $nurseRepository->findAll();
        
        if (empty($nurses)) {
            return new JsonResponse(
                ['status' => 'error', 'message' => 'No nurses found'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $nursesData = array_map(function (Nurse $nurse) {
            return [
                'id' => $nurse->getId(),
                'username' => $nurse->getUser(),
                'name' => $nurse->getName(),
                'surname' => $nurse->getSurname(),
                'password' => $nurse->getPassword(),
            ];
        }, $nurses);

        return new JsonResponse(
            ['status' => 'success', 'nurses' => $nursesData],
            Response::HTTP_OK
        );
    }

// Añadir un nuevo enfermero
#[Route('/new', name: 'app_nurse_c_r_u_d_new', methods: ['POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    // Crear nuevo objeto Nurse
    $nurse = new Nurse();

    // Decodificar la solicitud JSON
    $data = json_decode($request->getContent(), true);

    if ($data === null) {
        return $this->json([
            'status' => 'error',
            'message' => 'Invalid JSON'
        ], Response::HTTP_BAD_REQUEST);
    }

    // Configurar los valores del objeto Nurse usando los datos proporcionados
    if (isset($data['user'], $data['password'], $data['name'], $data['surname'])) {
        $nurse->setUser($data['user']);
        $nurse->setPassword($data['password']);  // **Importante**: Hashear la contraseña en una implementación real
        $nurse->setName($data['name']);
        $nurse->setSurname($data['surname']);
    } else {
        return $this->json([
            'status' => 'error',
            'message' => 'Missing required fields'
        ], Response::HTTP_BAD_REQUEST);
    }

    // Guardar el objeto Nurse en la base de datos
    try {
        $entityManager->persist($nurse);
        $entityManager->flush();
    } catch (\Exception $e) {
        return $this->json([
            'status' => 'error',
            'message' => 'Failed to save the nurse: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Retornar el JSON de éxito
    return $this->json([
        'status' => 'success',
        'nurse' => [
            'id' => $nurse->getId(),
            'name' => $nurse->getName(),
            'surname' => $nurse->getSurname(),
            'user' => $nurse->getUser(),
            'password' => $nurse->getPassword()  // **No devolver la contraseña en producción**
        ]
    ], Response::HTTP_CREATED);
}

    // Devuelve un usuario dado un id
    #[Route('/{id}', name: 'app_nurse_c_r_u_d_show', methods: ['GET'])]
    public function show(NurseRepository $nurseRepository, int $id): JsonResponse
    {
        $nurse = $nurseRepository->find($id);

        if (!$nurse) {
            return new JsonResponse(
                ['message' => 'No nurse found with the given ID: ' . $id],
                Response::HTTP_NOT_FOUND
            );
        }

        $nurseData = [
            'ID' => $nurse->getId(),
            'Name' => $nurse->getName(),
            'Surname' => $nurse->getSurname(),
            'User' => $nurse->getUser(),
            'Password' => $nurse->getPassword()
        ];

        return new JsonResponse(['nurse' => $nurseData], Response::HTTP_OK);
    }

    //actualiza un usuario dado un id
    #[Route('/{id}/edit', name: 'app_nurse_c_r_u_d_edit', methods: ['PUT'])]
    public function edit(int $id, Request $request, NurseRepository $nurseRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $nurse = $nurseRepository->find($id);

        // Si el id introducido no se encuentra, muestra error 404
        if (!$nurse) {
            return $this->json(['error' => 'Nurse not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Si el id introducido se encuentra pero los datos de usuario o contraseña están vacios, muestra un error 400
        $nurse->setUser($data['user'] ?? $nurse->getUser());
        $nurse->setPassword($data['password'] ?? $nurse->getPassword());
        $nurse->setName($data['name'] ?? $nurse->getName());
        $nurse->setSurname($data['surname'] ?? $nurse->getSurname());

        $errors = $validator->validate($nurse);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['status' => 'error', 'errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        return $this->json(['status' => 'success', 'message' => 'Nurse updated successfully'], Response::HTTP_OK);
    }

    //elimina un usuario dado un id
    #[Route('/{id}', name: 'app_nurse_c_r_u_d_delete', methods: ['DELETE'])]
    public function delete(int $id, NurseRepository $nurseRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $nurse = $nurseRepository->find($id);

        if (!$nurse) {
            return $this->json(['error' => 'Nurse not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($nurse);
        $entityManager->flush();

        return $this->json(['status' => 'success', 'message' => 'Nurse deleted successfully'], Response::HTTP_OK);
    }

    // Buscar enfermeros por nombre y apellido
    #[Route('/name/{name}/{surname}', name: 'nurses_names', methods: ['GET'])]
    public function findByNameAndSurname(NurseRepository $nurseRepository, string $name, string $surname): JsonResponse
    {
        $foundNurses = $nurseRepository->findByNameAndSurname($name, $surname);

        if (empty($foundNurses)) {
            return new JsonResponse(
                ['message' => 'No nurses found with the given name: ' . $name . ' and surname: ' . $surname],
                Response::HTTP_NOT_FOUND
            );
        }

        $nurseNames = array_map(function (Nurse $nurse) {
            return $nurse->getName() . ' ' . $nurse->getSurname();
        }, $foundNurses);

        return new JsonResponse(['found_nurses' => $nurseNames], Response::HTTP_OK);
    }

    // Autenticación de enfermeros
    #[Route('/login/{username}/{password}', name: 'nurses_login', methods: ['GET'])]
    public function login(NurseRepository $nurseRepository, string $username, string $password): JsonResponse
    {
        $nurse = $nurseRepository->findOneBy(['user' => $username]);

        if ($nurse && $nurse->getPassword() === $password) {
            return new JsonResponse(['message' => 'Login successful'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_NOT_FOUND);
    }
}