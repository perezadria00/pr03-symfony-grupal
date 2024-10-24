<?php

// src/Controller/NurseController.php

namespace App\Controller;

use App\Entity\Nurse;
use App\Repository\NurseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nurse')]
class NurseController extends AbstractController
{


    #[Route('/login/{username}/{password}', name: 'nurses_login', methods: ['GET'])]
    public function login(NurseRepository $nurseRepository, string $username, string $password): JsonResponse
    {
        // Buscar enfermero por username
        $nurse = $nurseRepository->findOneBy(['user' => $username]);

        // Verificar si el enfermero existe y si la contraseña coincide
        if ($nurse && $nurse->getPassword() === $password) {
            // Retornar verdadero si las credenciales son correctas
            return new JsonResponse(['message' => 'Login successful'], JsonResponse::HTTP_OK);
        }

        // Si no se encuentra coincidencia, retornar un error 404
        return new JsonResponse(['message' => 'Invalid credentials'], JsonResponse::HTTP_NOT_FOUND);
    }
    
}
