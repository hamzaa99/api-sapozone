<?php


namespace App\Controller;


use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\OrderRepository;
use App\Repository\StoreRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AbstractController
{


    private $storeRepository;
    private $userRepository;
    private $messageRepository;
    public function __construct(StoreRepository $storeRepository, UserRepository $userRepository,MessageRepository $messageRepository)
    {
        $this->storeRepository = $storeRepository;
        $this->userRepository = $userRepository;
        $this->messageRepository = $messageRepository;

    }

    /**
     * @Route("/messages/user/{id}", name="userMessages", methods={"GET"})
     * @return JsonResponse
     */
    public function getUserMessages($id): JsonResponse
    {
        $user=$this->userRepository->find($id);
        $messages = $this->messageRepository->findUserMessages($user);
        foreach ($messages as $message) {

            $data[] = [
               "id"=> $message->getId(),
               "sender_id"=> $message->getSender()->getId(),
               "receiver_id"=> $message->getReciever()->getId(),
                "date"=> $message->getDate(),
                "content"=>$message->getContent()
            ];
                }
        return new JsonResponse($data,Response::HTTP_OK);
    }
    /**
     * @Route("/last_messages/user/{id}", name="userMessages", methods={"GET"})
     * @return JsonResponse
     */
    public function getUserlastMessages($id): JsonResponse
    {
        $user=$this->userRepository->find($id);
        $messages = $this->messageRepository->findUserMessages($user);
        return $messages;
    }
    /**
     * @Route("/last_messages/user/{id}", name="userMessages", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function addMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data)){
            $sender = $this->userRepository->find($data['id_sender']);
            $reciever = $this->userRepository->find($data['id_reciever']);
            $store = $this->storeRepository->find($data['id_store']);
         $this->messageRepository->saveMessage($sender,$reciever,$store,$data['content']);
            $data=[
                'status' =>'succes'
            ];

            return new JsonResponse($data, Response::HTTP_OK);

        }
        $data=[
            'status' =>'failed',
            'message'=>'no data sent'
        ];

        return new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}