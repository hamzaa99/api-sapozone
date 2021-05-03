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
use Symfony\Component\Routing\Annotation\Route;


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
     * @Route("/test2", name="test", methods={"get"})
     */
    public function test(): JsonResponse
    {

        $data=[
            'message' =>'bienvenue dans l\'api sapozone'
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }
    /**
     * @Route("/messages/user/{id}", name="userMessages", methods={"get"})
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

        if(!empty($data) &&count($data))
        return new JsonResponse($data,Response::HTTP_OK);
        else $data[]= [ 'message'=>'no data found'];
        return new JsonResponse($data,Response::HTTP_OK);
    }
    /**
     * @Route("/conversations/user/{id}", name="userConvs", methods={"get"})
     * @return JsonResponse
     */
    public function getUserConverstions($id): JsonResponse
    {
        $user=$this->userRepository->find($id);
        $messages = $this->messageRepository->findUserConv($user);
        $known_id = [];
        foreach ($messages as $message) {

            if( in_array($message['sender_id'],$known_id) || in_array($message['receiver_id'],$known_id)  ) {}
            else {
            $data[] = [
                "id"=> $message['id'],
                "sender"=> $this->userRepository->find($message['sender_id'])->getUsername(),
                "sender_id"=>$message['sender_id'],
                "receiver"=> $this->userRepository->find($message['reciever_id'])->getUsername(),
                "receiver_id" =>$message['reciever_id'],
                "date"=> $message['date'],
                "content"=>$message['content']
            ];

            array_push($known_id,$message['sender_id'],$message['reciever_id']);
            }
        }

        if(!empty($data) &&count($data))
            return new JsonResponse($data,Response::HTTP_OK);
        else $data[]= [ 'message'=>'no data found'];
        return new JsonResponse($data,Response::HTTP_OK);

    }

    /**
     * @Route("/messages/user1/{id}/user2/{id2}", name="convMessages", methods={"get"})
     * @return JsonResponse
     */
    public function getConvMessages($id,$id2): JsonResponse
    {
        $user1=$this->userRepository->find($id);
        $user2=$this->userRepository->find($id2);
        $messages = $this->messageRepository->findConvMessages($user1,$user2);
        foreach ($messages as $message) {

            $data[] = [
                "id"=> $message->getId(),
                "sender_id"=> $message->getSender()->getId(),
                "sender"=> $message->getSender()->getUsername(),
                "receiver_id"=> $message->getReciever()->getId(),
                "receiver"=> $message->getReciever()->getUsername(),
                "date"=> $message->getDate(),
                "content"=>$message->getContent()
            ];
        }

        if(!empty($data) &&count($data))
            return new JsonResponse($data,Response::HTTP_OK);
        else $data[]= [ 'message'=>'no data found'];
        return new JsonResponse($data,Response::HTTP_OK);
    }

    /**
     * @Route("/messages", name="addMessage", methods={"post"})
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
            if(is_null($sender) ||is_null($reciever)||is_null($store)){
                $data=[
                    'error'=>"reciever not found"
                ];
                return new JsonResponse($data,Response::HTTP_OK);
            }
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