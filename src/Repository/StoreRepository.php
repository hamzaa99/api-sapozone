<?php

namespace App\Repository;

use App\Entity\Store;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Store|null find($id, $lockMode = null, $lockVersion = null)
 * @method Store|null findOneBy(array $criteria, array $orderBy = null)
 * @method Store[]    findAll()
 * @method Store[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreRepository extends ServiceEntityRepository
{
    private $manager;
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $manager)
    {
        parent::__construct($registry, Store::class);
        $this->manager=$manager;    }

    // /**
    //  * @return Store[] Returns an array of Store objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Store
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function saveStore($Owner,$name)
    {
        $newstore = new Store();
        $user = $Owner;


        $newstore
            ->setOwner($user)
            ->setName($name);

        $this->manager->persist($newstore);
        $this->manager->flush();
        return $newstore->getId();
    }

    public function updateStore($store) : Store
    {
        $this->manager->persist($store);
        $this->manager->flush();

        return $store;
    }
    public function removeStore(Store $store)
    {
        $this->manager->remove($store);
        $this->manager->flush();


    }

    public function searchStore($query){

        $query = '%'.$query.'%';
        return $this->createQueryBuilder('m')
            ->Where('m.name LIKE :name')
            ->setParameter('name', $query)

            ->getQuery()
            ->getResult();
    }
}
