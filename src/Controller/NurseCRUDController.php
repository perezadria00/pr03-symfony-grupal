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
    public function getAll(Request $request, NurseRepository $nurseRepository): JsonResponse
    {

        $nameFilter = $request->query->get('name', '');
        $surnameFilter = $request->query->get('surname', '');

        // Obtener todos los enfermeros de la base de datos
        $nurses = $nurseRepository->findAll();

        // Filtrar por nombre o apellido si se proporciona
        $filteredNurses = array_filter($nurses, function (Nurse $nurse) use ($nameFilter, $surnameFilter) {
            return str_contains(strtolower($nurse->getName() ?? ''), strtolower($nameFilter)) ||
            str_contains(strtolower($nurse->getSurname() ?? ''), strtolower($surnameFilter));
        });

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
                'speciality' => $nurse->getSpeciality(),
                'shift' => $nurse->getShift(),
                'phone' => $nurse->getPhone()
            ];
        }, $filteredNurses);

        return new JsonResponse(
            ['status' => 'success', 'nurses' => $nursesData],
            Response::HTTP_OK
        );
    }

    #[Route('/new', name: 'app_nurse_c_r_u_d_new', methods: ['POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, NurseRepository $nurseRepository): Response
{
    $data = json_decode($request->getContent(), true);

    if ($data === null) {
        return $this->json([
            'status' => 'error',
            'message' => 'Invalid JSON'
        ], Response::HTTP_BAD_REQUEST);
    }

    // Verificar si el usuario o teléfono ya existen
    $existingUsername = $nurseRepository->findOneBy(['user' => $data['username']]);
    $existingPhone = $nurseRepository->findOneBy(['phone' => $data['phone']]);

    if ($existingUsername) {
        return $this->json([
            'status' => 'error',
            'message' => 'El usuario ya está registrado.'
        ], Response::HTTP_BAD_REQUEST);
    }

    if ($existingPhone) {
        return $this->json([
            'status' => 'error',
            'message' => 'El teléfono ya está registrado.'
        ], Response::HTTP_BAD_REQUEST);
    }

    $nurse = new Nurse();
    $nurse->setUser($data['username']);
    $nurse->setPassword($data['password']);
    $nurse->setName($data['name']);
    $nurse->setSurname($data['surname']);
    $nurse->setSpeciality($data['speciality']);
    $nurse->setShift($data['shift']);
    $nurse->setPhone($data['phone']);

    try {
        $entityManager->persist($nurse);
        $entityManager->flush();
    } catch (\Exception $e) {
        return $this->json([
            'status' => 'error',
            'message' => 'Error al guardar el registro: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    return $this->json([
        'status' => 'success',
        'message' => 'Registro exitoso.',
        'nurse' => [
            'id' => $nurse->getId(),
            'name' => $nurse->getName(),
            'surname' => $nurse->getSurname(),
            'username' => $nurse->getUser(),
            'speciality' => $nurse->getSpeciality(),
            'shift' => $nurse->getShift(),
            'phone' => $nurse->getPhone()
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
            'Username' => $nurse->getUser(),
            'Password' => $nurse->getPassword(),
            'Speciality' => $nurse->getSpeciality(),
            'Shift' => $nurse->getShift(),
            'Phone' => $nurse->getPhone()
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
            // En vez de lanzar una excepción, devuelve una respuesta adecuada
            return $this->json(['error' => 'Nurse not found'], Response::HTTP_NOT_FOUND);
        }


        $data = json_decode($request->getContent(), true);

        // Si el id introducido se encuentra pero los datos de usuario o contraseña están vacios, muestra un error 400
        $nurse->setUser($data['username'] ?? $nurse->getUser());
        $nurse->setPassword($data['password'] ?? $nurse->getPassword());
        $nurse->setName($data['name'] ?? $nurse->getName());
        $nurse->setSurname($data['surname'] ?? $nurse->getSurname());
        $nurse->setSpeciality($data['speciality'] ?? $nurse->getSpeciality());
        $nurse->setShift($data['shift'] ?? $nurse->getSurname());
        $nurse->setPhone($data['phone']) ?? $nurse->getPhone();

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
