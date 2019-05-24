<?php

namespace App\Repository;

use App\Entity\Sms;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Sms|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sms|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sms[]    findAll()
 * @method Sms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Sms::class);
    }

    // /**
    //  * @return Sms[] Returns an array of Sms objects
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
    public function findOneBySomeField($value): ?Sms
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findById($value): ?Sms
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findUnsentMessages()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.sentState = :val')
            ->setParameter('val', 0)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getAllMessages()
    {
        return $this->getEntityManager()->createQuery(
            'SELECT s.id, s.messageBody, s.phoneNumber, s.creationDate, s.sentState, s.api FROM App:sms s ORDER BY s.id ASC'
        )->getResult();
    }

    public function findApiUsage($apiNum)
    {
        return $this->getEntityManager()->createQuery(
            'SELECT s.id FROM App:sms s WHERE s.api = ' . $apiNum . ' ORDER BY s.id ASC'
        )->getResult();
    }

    public function findMostUsedNumbers()
    {
        return array_slice($this->getEntityManager()->createQuery(
            'SELECT s.phoneNumber, COUNT(s.id) as counter FROM App:sms s GROUP BY s.phoneNumber ORDER BY counter DESC'
        )->getResult(), 0, 10);
    }
}
