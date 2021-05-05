<?php


namespace App\Controller;

use App\Entity\Order;
use App\Entity\Picture;
use App\Repository\OrderRepository;
use App\Repository\RequestRepository;
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




class RequestController extends AbstractController
{


    private $storeRepository;
    private $userRepository;
    private $orderRepository;
    private $requestRepository;

    public function __construct(StoreRepository $storeRepository, UserRepository $userRepository, RequestRepository $requestRepository, OrderRepository $orderRepository)
    {
        $this->storeRepository = $storeRepository;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->requestRepository = $requestRepository;
    }

    /**
     * @Route("/requests/", name="add_request", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add_store(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $storeid = $data['store'];
        $customerid = $data['customer'];
        $detail = $data['detail'];
        if (empty($data['price'])) $price=0;else $price = $data['price'];

        if (empty($data['lead'])) $lead=0;else $lead = $data['lead'];


        $customer= $this->userRepository->find($customerid);
        $store= $this->storeRepository->find($storeid);

        if (empty($store) || empty($customer) || empty($detail)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $id=$this->requestRepository->saveRequest($customer,$store,$detail,$price,$lead);
        return new JsonResponse(['status' => 'request created!','id'=>$id], Response::HTTP_OK);
    }

/**
 * @Route("/userrequests/{id}", name="user_request", methods={"GET"})
 * @return JsonResponse
 */
public function get_user_requests($id): JsonResponse
{

    $customerid = $id;
    $customer= $this->userRepository->find($customerid);
    $requests=$this->requestRepository->findBy(['customer' => $customer]);
    foreach ($requests as $request) {
        $data[] = [
            "id" => $request->getId(),
            "detail" =>$request->getDetail(),
            "price" =>$request->getMaxPrice(),
            "lead" =>$request->getMaxLeadTime()
        ];
    }
    return new JsonResponse($data, Response::HTTP_OK);
}
    /**
     * @Route("/storerequests/{id}", name="store_request", methods={"GET"})
     * @return JsonResponse
     */
    public function get_store_requests($id): JsonResponse
    {


        $store= $this->storeRepository->find($id);
        $requests=$this->requestRepository->findBy(['Store' => $store]);
        foreach ($requests as $request) {
            if ($request->getCustomer()->getId()!=$request->getStore()->getOwner()->getId())
            $data[] = [
                "id" => $request->getId(),
                "detail" =>$request->getDetail(),
                "price" =>$request->getMaxPrice(),
                "lead" =>$request->getMaxLeadTime()
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }
    /**
     * @Route("/storeproducts/{id}", name="store_product", methods={"GET"})
     * @return JsonResponse
     */
    public function get_store_products($id): JsonResponse
    {


        $store= $this->storeRepository->find($id);
        $customer= $this->userRepository->find($store->getOwner()->getId());
        $requests=$this->requestRepository->findBy(['Store' => $store,'customer'=>$customer]);
        foreach ($requests as $request) {
            $data[] = [
                "id" => $request->getId(),
                "detail" =>$request->getDetail(),
                "price" =>$request->getMaxPrice(),
                "lead" =>$request->getMaxLeadTime()
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

}
