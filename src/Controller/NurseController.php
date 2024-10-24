<?php

// src/Controller/NurseController.php

namespace App\Controller;

use App\Entity\Nurse;
use App\Repository\NurseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NurseController extends AbstractController
{
    // private $nurseRepository;

    // public function __construct(NurseRepository $nurseRepository)
    // {
    //     $this->nurseRepository = $nurseRepository;
    // }

    // #[Route('/name/{name}/{surname}', name: 'nurses_names', methods: ['GET'])]
    // public function findByNameAndSurname(NurseRepository $nurseRepository, string $name, string $surname): Response
    // {
    //     $foundNurses = $nurseRepository->findByNameAndSurname($name, $surname);

    //     if (empty($foundNurses)) {
    //         return new Response(
    //             'No nurses found with the given name: ' . $name . ' and surname: ' . $surname,
    //             Response::HTTP_NOT_FOUND
    //         );
    //     }

    //     $nurseNames = array_map(function (Nurse $nurse) {
    //         return $nurse->getName() . ' ' . $nurse->getSurname();
    //     }, $foundNurses);

    //     return new Response(
    //         'Found nurses: ' . implode(', ', $nurseNames),
    //         Response::HTTP_OK
    //     );
    // }

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
    
        return new Response(print_r($nursesData, true));
    }

    // #[Route('/login/{username}/{password}', name: 'nurses_login', methods: ['GET'])]
    // public function login(NurseRepository $nurseRepository, string $username, string $password): Response
    // {
    //     // Buscar enfermero por username
    //     $nurse = $nurseRepository->findOneBy(['username' => $username]);

    //     // Verificar si el enfermero existe y si la contraseña coincide
    //     if ($nurse && $nurse->getPassword() === $password) {
    //         // Retornar verdadero si las credenciales son correctas
    //         return new Response('Login successful', Response::HTTP_OK);
    //     }

    //     // Si no se encuentra coincidencia, retornar un error 404
    //     return new Response('Invalid credentials', Response::HTTP_NOT_FOUND);
    // }
}



