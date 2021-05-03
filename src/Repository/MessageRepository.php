<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Store;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $manager)
    {
        parent::__construct($registry, Message::class);
        $this->manager=$manager;

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
            ->getResult();
    }
    public function findUserConv(User $user){

        $sql = 'SELECT m.id, m.sender_id, m.reciever_id, m.date, m.content from message m JOIN (
                    SELECT sender_id, reciever_id, max(`date`) as last_date from message  where sender_id='.$user->getId().' or reciever_id='.$user->getId().' group by sender_id, reciever_id)
                    AS lm on m.date = lm.last_date and m.sender_id=lm.sender_id and m.reciever_id=lm.reciever_id order by m.date DESC';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute([]);

        return $stmt->fetchAll();


    }
    public function findConvMessages($user1,$user2)
    {
        return $this->createQueryBuilder('m')
            ->Where('m.sender in (:user1,:user2)')
            ->andWhere('m.Reciever in (:user1,:user2)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->add('orderBy','m.date desc')
            ->getQuery()
            ->getResult();
    }
    public function saveMessage(User $sender,User $reciever,Store $store,string $content)
    {
        $message = new Message();

        $message->setDate(new \DateTime());
        $message->setStore($store);
        $message->setSender($sender);
        $message->setReciever($reciever);
        $message->setContent($content);

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
