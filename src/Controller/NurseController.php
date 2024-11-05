<?php

// src/Controller/NurseController.php

namespace App\Controller;

use App\Entity\Nurse;
use App\Repository\NurseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

#[Route('/nurse')]
class NurseController extends AbstractController
{


    #[Route('/index', name: 'nurse_index', methods: ['GET'])]
    public function getAll(NurseRepository $nurseRepository): Response
    {
        // Obtener todos los enfermeros de la base de datos
        $nurses = $nurseRepository->findAll();
    
        $nursesData = [];
        foreach ($nurses as $nurse) {
            $nursesData[] = [
                'id' => $nurse->getId(),
                'name' => $nurse->getName(),
                'surname' => $nurse->getSurname(),
            ];
        }
    
        // Retornar los datos en formato JSON
        return new JsonResponse($nursesData, Response::HTTP_OK);
    }
    


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

        return new JsonResponse(
            ['found_nurses' => $nurseNames],
            Response::HTTP_OK
        );
    }




    #[Route('/login/{username}/{password}', name: 'nurses_login', methods: ['GET'])]
    public function login(NurseRepository $nurseRepository, string $username, string $password): JsonResponse
    {
        // Buscar enfermero por username
        $nurse = $nurseRepository->findOneBy(['user' => $username]);

        // Verificar si el enfermero existe y si la contraseña coincide
        if ($nurse && $nurse->getPassword() === $password) {
            // Retornar verdadero si las credenciales son correctas
            return new JsonResponse(['message' => 'Login successful'], Response::HTTP_OK);
        }

        // Si no se encuentra coincidencia, retornar un error 404
        return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_NOT_FOUND);
    }


}
