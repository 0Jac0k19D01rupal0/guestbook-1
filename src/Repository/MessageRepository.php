<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */

    public function findAllOrderedByCol($col, $sort_Type)
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.'.$col, $sort_Type)
            ->andWhere('m.is_enabled = true')
//            ->setFirstResult( $first_result )
//            ->setMaxResults( $limit )
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllUserMessages($col, $sort_Type, $username)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.username = :username')
            ->setParameter('username', $username)
            ->orderBy('m.'.$col, $sort_Type)
            ->getQuery()
            ->getResult()
            ;
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
