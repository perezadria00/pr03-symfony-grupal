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

#[Route('/nurse')]
final class NurseCRUDController extends AbstractController
{
    //CRUD para entidad Nurse

    //devuelve todos los usuarios
    #[Route('/index', name: 'app_nurse_c_r_u_d_index', methods: ['GET'])]
    public function getAll(NurseRepository $nurseRepository): Response
    {
        // Obtener todos los enfermeros de la base de datos
        $nurses = $nurseRepository->findAll();
        
        // Si no hay enfermeros, retornar un 400 KO
        if (empty($nurses)) {
            return new JsonResponse(
                ['status' => 'error', 'message' => 'No nurses found'],
                Response::HTTP_BAD_REQUEST
            );
        }
    
        // Procesar los datos si hay enfermeros
        $nursesData = [];
        foreach ($nurses as $nurse) {
            $nursesData[] = [
                'id' => $nurse->getId(),
                'username' => $nurse->getUser(),
                'name' => $nurse->getName(),
                'surname' => $nurse->getSurname(),
            ];
        }
    
        // Retornar los datos en formato JSON con un código 201 OK
        return new JsonResponse(
            ['status' => 'success', 'nurses' => $nursesData],
            Response::HTTP_CREATED
        );
    }
    

    //añade un usuario
    #[Route('/new', name: 'app_nurse_c_r_u_d_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nurse = new Nurse();
        $form = $this->createForm(NurseType::class, $nurse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($nurse);
            $entityManager->flush();

            return $this->redirectToRoute('app_nurse_c_r_u_d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nurse_crud/new.html.twig', [
            'nurse' => $nurse,
            'form' => $form,
        ]);
    }

    //devuelve un usuario dado un id
    #[Route('/find/{id}', name: 'app_nurse_c_r_u_d_show', methods: ['GET'])]
    public function show(NurseRepository $nurseRepository, int $id): JsonResponse {

        $foundNurses = $nurseRepository->findByID($id);

        if (empty($foundNurses)) {
            return new JsonResponse(
                ['message' => 'No nurses found with the given id : ' . $id],
                Response::HTTP_NOT_FOUND
            );
        }

        $nurseNames = array_map(function (Nurse $nurse) {
            return [
                'ID' => $nurse->getId(),
                'Name' => $nurse->getName(),
                'Surname' => $nurse->getSurname(),
                'User' => $nurse->getUser(),
                'Password' => $nurse->getPassword()
            ];
        }, $foundNurses);
        
        return new JsonResponse(
            ['found_nurses' => $nurseNames],
            Response::HTTP_OK
        );
        

       
    }

    //actualiza un usuario dado un id
    #[Route('/{id}/edit', name: 'app_nurse_c_r_u_d_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nurse $nurse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NurseType::class, $nurse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_nurse_c_r_u_d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nurse_crud/edit.html.twig', [
            'nurse' => $nurse,
            'form' => $form,
        ]);
    }

    //elimina un usuario dado un id
    #[Route('/{id}', name: 'app_nurse_c_r_u_d_delete', methods: ['POST'])]
    public function delete(Request $request, Nurse $nurse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nurse->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($nurse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nurse_c_r_u_d_index', [], Response::HTTP_SEE_OTHER);
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
