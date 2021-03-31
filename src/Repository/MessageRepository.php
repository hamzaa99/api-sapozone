<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */

    public function findUserMessages($user)
    {
        return $this->createQueryBuilder('m')
            ->Where('m.sender = :sender')
            ->setParameter('sender', $user)
            ->orWhere('m.Reciever = :reciever')
            ->setParameter('reciever', $user)
            ->getQuery()
            ->getResult()
        ;
    }
    public function saveMessage($sender,$reciever, $store,$content)
    {
        $message = new Message();

        $message
            ->setDate(date())
            ->setStore($store)
            ->setSender($sender)
            ->setReciever($reciever)
            ->setContent($content);

        $this->manager->persist($message);
        $this->manager->flush();
    }


    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
