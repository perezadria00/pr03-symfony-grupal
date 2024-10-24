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
    #[Route('/index', name: 'nurse_index', methods: ['GET'])]
    public function getAll(NurseRepository $nurseRepository): JsonResponse
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
        return new JsonResponse($nursesData, JsonResponse::HTTP_OK);
    }
    
}
