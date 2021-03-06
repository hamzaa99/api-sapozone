<?php


namespace App\Controller;

use App\Entity\Picture;
use App\Repository\UserRepository;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;




class StoreController extends AbstractController
{


    private $storeRepository;
    private $userRepository;
    public function __construct(StoreRepository $storeRepository,UserRepository $userRepository)
    {
        $this->storeRepository = $storeRepository;
        $this->userRepository = $userRepository;
    }




    /**
     * @Route("/stores/", name="add_store", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add_store(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $ownerid = $data['owner'];
        $name = $data['name'];



        $owner= $this->userRepository->find($ownerid);

        if (empty($name) || empty($owner)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $id=$this->storeRepository->saveStore($owner,$name);
        return new JsonResponse(['status' => 'Store created!','id'=>$id], Response::HTTP_OK);
    }


    /**
     * @Route("/stores/{id}", name="get_onestore", methods={"GET"})
     */
    public function getOnestore($id):JsonResponse
    {
        $store = $this->storeRepository->findOneBy(['id' => $id]);

        $data = $store->toArray();

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/store/owner/{id}", name="get_one_store", methods={"GET"})
     */
    public function getuserstore($id):JsonResponse
    {
        $owner =  $this->userRepository->findOneBy(['id' => $id]);
        $store = $this->storeRepository->findOneBy(['Owner' => $owner]);

        if($store!=null) {
            $data = $store->toArray();
            return new JsonResponse($data, Response::HTTP_OK);
        }
        else
        return new JsonResponse(['status' => 'store dosent exist'], Response::HTTP_NOT_FOUND);

    }
    /**
     * @Route("/storeowner/{id}", name="getstore_city", methods={"GET"})
     */
    public function getStore_city($id):JsonResponse
    {
        $owner = $this->userRepository->findOneBy(['id' => $id]);
        $stores = $this->storeRepository->findOneBy(['Owner' => $owner]);


            $data = $stores->toArray();



        return new JsonResponse($data, Response::HTTP_OK);
    }
    /**
     * @Route("/stores/", name="getAll", methods={"GET"})
     */
    public function getAll(): JsonResponse
    {
        $stores = $this->storeRepository->findAll();



        foreach ($stores as $store) {
            $pictures = $store->getPictures();
            if (!empty($pictures)){
                $firstpicture=$pictures->get(0);
                if(!empty($firstpicture))
                    $url=$firstpicture->getLocation();
                else $url="";
            }
            else $url="";
            $data[] = [
                "id" => $store->getId(),
                "name" => $store->getName(),
                "street_number" =>$store->getStreetNUMBER(),
                "street_name" =>$store->getStreetName(),
                "city" =>$store->getCity(),
                "postal_code"=>$store->getPostalCode(),
                "bio" =>$store->getBio(),
                "owner" => $store->getOwner()->getId(),
                "picture" => $url
            ];
        }


        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/stores/search/{query}", name="getAllsearch", methods={"GET"})
     */
    public function getAllbysearch($query): JsonResponse
    {


        $stores = $this->storeRepository->searchStore($query);




        foreach ($stores as $store) {
            $pictures = $store->getPictures();
            if (!empty($pictures)){
                $firstpicture=$pictures->get(0);
                if(!empty($firstpicture))
                    $url=$firstpicture->getLocation();
                else $url="";
            }
            else $url="";
            $data[] = [
                "id" => $store->getId(),
                "name" => $store->getName(),
                "street_number" =>$store->getStreetNUMBER(),
                "street_name" =>$store->getStreetName(),
                "city" =>$store->getCity(),
                "postal_code"=>$store->getPostalCode(),
                "bio" =>$store->getBio(),
                "owner" => $store->getOwner()->getId(),
                "picture" => $url
            ];
        }


        return new JsonResponse($data, Response::HTTP_OK);
    }
    /**
     * @Route("/stores/{id}", name="update_store", methods={"PUT"})
     */
    public function updateStore($id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $store = $this->storeRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['name']) ? true : $store->setName($data['name']);
        empty($data['streetname']) ? true : $store->setStreetName($data['streetname']);
        empty($data['street_number']) ? true : $store->setStreetNUMBER($data['street_number']);
        empty($data['city']) ? true : $store->setCity($data['city']);
        empty($data['bio']) ? true : $store->setBio($data['bio']);
        empty($data['phone_number']) ? true : $store->setPhoneNumber($data['phone_number']);
        empty($data['postal_code']) ? true : $store->setPostalCode($data['postal_code']);
        if (!empty($data['picture'])) {
            $picture = new Picture();
            $picture->setLocation($data['picture']);
            $em->persist($picture);
            $em->flush();
            $store->addPicture($picture);
        }

        $updatedstore = $this->storeRepository->updatestore($store);

        return new JsonResponse($updatedstore->toArray(), Response::HTTP_OK);
    }
    /**
     * @Route("/stores/{id}", name="delete_store", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {

        $owner = $this->userRepository->findOneBy(['id' => $id]);
        $store = $this->storeRepository->findOneBy(['Owner' => $owner]);

        if(empty($store)) return new JsonResponse(['status' => 'store dosent exist'], Response::HTTP_NO_CONTENT);

        $this->storeRepository->removeStore($store);
        return new JsonResponse(['status' => 'store deleted'], Response::HTTP_OK);
    }


}
