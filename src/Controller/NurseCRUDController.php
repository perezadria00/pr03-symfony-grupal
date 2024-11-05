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

#[Route('/nurse/crud')]
final class NurseCRUDController extends AbstractController
{
    #[Route(name: 'app_nurse_c_r_u_d_index', methods: ['GET'])]
    public function index(NurseRepository $nurseRepository): Response
    {
        return $this->render('nurse_crud/index.html.twig', [
            'nurses' => $nurseRepository->findAll(),
        ]);
    }

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

    #[Route('/{id}', name: 'app_nurse_c_r_u_d_show', methods: ['GET'])]
    public function show(Nurse $nurse): Response
    {
        return $this->render('nurse_crud/show.html.twig', [
            'nurse' => $nurse,
        ]);
    }

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

    #[Route('/{id}', name: 'app_nurse_c_r_u_d_delete', methods: ['POST'])]
    public function delete(Request $request, Nurse $nurse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nurse->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($nurse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nurse_c_r_u_d_index', [], Response::HTTP_SEE_OTHER);
    }

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
