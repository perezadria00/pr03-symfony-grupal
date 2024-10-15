<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route '/nurse'
 */

class NurseController extends AbstractController


{
    #[Route('/name/{name}', name: 'nurses_names', methods: ['GET'], requirements: ['name' => '[a-zA-Z]+'])]

    public function findByName(string $name): JsonResponse
    {
        
        $nurses = [
            ['id' => 1, 'name' => 'Juan'],
            ['id' => 2, 'name' => 'Ana'],
            ['id' => 3, 'name' => 'María'],
            ['id' => 4, 'name' => 'Pedro'],
            ['id' => 5, 'name' => 'Laura']
        ];

       
        
        $foundNurses = array_filter($nurses, function ($nurse) use ($name) {
            return stripos($nurse['name'], $name) !== false;
        });

        
        if (empty($foundNurses)) {
            return new JsonResponse(
                [
                    'error' => 'No nurses found with the given name.',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

       

        
        return new JsonResponse(array_values($foundNurses));
        
    }

    #[Route('/index', name: 'nurse_index', methods: ['GET'])]
    public function getAll(): JsonResponse

    {

        

        $nurses = [
            [
                'id' => 1,
                'name' => 'Juan Pérez',
                'specialty' => 'Cardiology',
                'age' => 35
            ],
            [
                'id' => 2,
                'name' => 'María López',
                'specialty' => 'Pediatrics',
                'age' => 29
            ],
            [
                'id' => 3,
                'name' => 'Carlos García',
                'specialty' => 'Surgery',
                'age' => 43
            ]
        ];

       

        return new JsonResponse($nurses);
    }



    #[Route('/name/{username}/{password}', name: 'nurses_names', methods: ['GET'], requirements: ['name' => '[a-zA-Z]+'])]
    public function login ($username, $password): JsonResponse {
    
        $nurses = [
            ['id' => 1, 'username' => 'Juan', 'password' => 'a'],
            ['id' => 2, 'username' => 'Ana', 'password' => 'b'],
            ['id' => 3, 'username' => 'María', 'password' => 'c'],
            ['id' => 4, 'username' => 'Pedro', 'password' => 'd'],
            ['id' => 5, 'username' => 'Laura', 'password' => 'e']
        ];
    
        // Buscar si el usuario y la contraseña coinciden
        foreach ($nurses as $nurse) {
            if ($nurse['username'] === $username && $nurse['password'] === $password) {
                // Retornar la información del enfermero en formato JSON
                return new JsonResponse(true);
            }
        }
    
        // Si no se encuentra coincidencia, retornar un error 404
        return new JsonResponse(false, 404);
    }
    
    

   
    

   
}

