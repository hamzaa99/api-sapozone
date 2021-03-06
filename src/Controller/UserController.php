<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\User;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\ErrorMappingException;
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
     * @Route("/test", name="test", methods={"get"})
     */
    public function test(): JsonResponse
    {

        $data=[
            'message' =>'bienvenue dans l\'api sapozone'
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }
    /**
     * @Route("/test2", name="test2", methods={"get"})
     */
    public function test2(): JsonResponse
    {

        $data=[
            'message' =>'bienvenue dans l\'api sapozone'
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/users/", name="add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $username = $data['username'];
        $password =$data['password'];


        if (empty($username) || empty($password)|| empty($email)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $user_email=$this->userRepository->findby(array("email"=>$email));
        $user_username=$this->userRepository->findby(array("username"=>$username));

        if(!is_null($user_email))
            return new JsonResponse(array("error"=>"Email is already taken"),Response::HTTP_INTERNAL_SERVER_ERROR);
        if (!is_null($user_username))
            return new JsonResponse(array("error"=>"Username is already taken"),Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->userRepository->saveUser($username,$email, $password);
        $user = new User();
            $this->userRepository->saveUser($username,$email, $password);

     $user = $this->userRepository->findOneBy(['username' => $username]);
        $data=[
            'status' =>'succes',
            'user' => $user->toArray()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/sign_in/", name="signin", methods={"POST"})
     */
    public function signin(Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $username = $data['username'];
        $password =$data['password'];


        if (empty($username) || empty($password)) {
            return new JsonResponse(['Error' => 'expecting mandatory parameters'], Response::HTTP_CREATED);
        }

        $user=$this->userRepository->findOneBy(['username' => $username]);
        $data=$user->toArray();

        if($password==$user->getPassword())
            return new JsonResponse($data, Response::HTTP_CREATED);
        else return new JsonResponse(['status' => 'error','message' => 'wrong-password','password' => password_hash($password,PASSWORD_BCRYPT)], Response::HTTP_CREATED);
    }

    /**
     * @Route("/users/{id}", name="getOneUser", methods={"GET"})
     */
    public function getOneUser($id):JsonResponse
    {

        $user = $this->userRepository->findOneBy(['id' => $id]);

        if (empty($user))
            return new JsonResponse(['Error' => 'this user doesnt exist!'], Response::HTTP_OK);
        $data[] = $user->toArray();
        if (empty($data))
          return new JsonResponse(['Error' => 'this user doesnt exist!'], Response::HTTP_OK);

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
                'firstName' => $user->getFirstname(),
                'lastName' => $user->getName(),
                'email' => $user->getEmail(),
                'phoneNumber' => $user->getPhoneNumber(),
                'password' => $user->getPassword(),
                'username' => $user->getUsername(),
                'city' => $user->getCity(),

            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);

    }
    /**
     * @Route("/users/{id}", name="updateUser", methods={"PUT"})
     */
    public function updateUser($id, Request $request,EntityManagerInterface $em): JsonResponse
    {
        if(
        $user = $this->userRepository->findOneBy(['id' => $id]))
        {

            $data = json_decode($request->getContent(), true);

            empty($data['username']) ? true : $user->setUsername($data['username']);
            empty($data['password']) ? true : $user->setPassword($data['password']);
            empty($data['lastname']) ? true : $user->setName($data['lastname']);
            empty($data['firstname']) ? true : $user->setFirstname($data['firstname']);
            empty($data['email']) ? true : $user->setEmail($data['email']);
            empty($data['street_name']) ? true : $user->setStreetname($data['street_name']);
            empty($data['street_number']) ? true : $user->setStreetNumber($data['street_number']);
            empty($data['postal_code']) ? true : $user->setPostalCode($data['postal_code']);
            empty($data['city']) ? true : $user->setCity($data['city']);
            empty($data['phone_number']) ? true : $user->setPhoneNumber($data['phone_number']);
            empty($data['bio']) ? true : $user->setBio($data['bio']);


            if (!empty($data['pp'])) {
                $picture = new Picture();
                $picture->setLocation($data['pp']);
                $em->persist($picture);
                $em->flush();
                $user->setProfilePicture($picture);
            }


            $updatedUser = $this->userRepository->updateUser($user);

            return new JsonResponse($updatedUser->toArray(), Response::HTTP_OK);}
        else return new JsonResponse(['status' => 'error'], Response::HTTP_OK);
    }
    /**
     * @Route("/users/{id}", name="delete", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        $this->userRepository->removeUser($user);

        return new JsonResponse(['status' => 'user deleted'], Response::HTTP_OK);
    }
    /**
     * @Route("/update_pp/{id}", name="update_pp", methods={"PUT"})
     */
    public function update_pp($id,Request $request): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        $file = $request->files->get('pp');



    }




    /**
     * @Route("/", name="")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }
}
