<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



class UserController extends AbstractController
{

    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }



    /**
     * @Route("/", name="home", methods={"get"})
     */
    public function home(): JsonResponse
    {

        $data=[
            'id' =>'1',
            'username' => 'user1',
            'password' => 'pass1',

        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/test", name="test", methods={"get"})
     */
    public function test(): JsonResponse
    {

        $data=[
            'id' =>'1',
            'username' => 'user2',
            'password' => 'pass2',

        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/users/", name="add_user", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $username = $data['username'];
        $password = $data['password'];


        if (empty($username) || empty($password)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->userRepository->saveUser($username,$password);

        return new JsonResponse(['status' => 'Customer created!'], Response::HTTP_CREATED);
    }


    /**
     * @Route("/users/{id}", name="get_one_user", methods={"GET"})
     */
    public function getOneUser($id):JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $user->getId(),
            'UserName' => $user->getUsername(),
            'PassWord' => $user->getPassword(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }
    /**
     * @Route("/users", name="getall", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $users = $this->userRepository->findAll();

        $data = [];


        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'password' => $user->getPassword(),

            ];
        }


        return new JsonResponse($data, Response::HTTP_OK);
    }
    /**
     * @Route("/customers/{id}", name="update_customer", methods={"PUT"})
     */
    public function updateUser($id, Request $request): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['username']) ? true : $user->setUsername($data['username']);
        empty($data['password']) ? true : $user->setPassword($data['password']);

        $updatedUser = $this->userRepository->updateUser($user);

        return new JsonResponse($updatedUser->toArray(), Response::HTTP_OK);
    }
    /**
     * @Route("/customers/{id}", name="delete_customer", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        $this->userRepository->removeUser($user);

        return new JsonResponse(['status' => 'user deleted'], Response::HTTP_NO_CONTENT);
    }


    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
