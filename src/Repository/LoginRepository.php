<?php

namespace App\Repository;

use App\Entity\Login;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Login>
 */
class LoginRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Login::class);
    }

    public function save(Login $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByMail(string $mail): ?Login
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.utilisateurs', 'u')
            ->addSelect('u')
            ->leftJoin('u.profils', 'p')
            ->addSelect('p')
            ->where('l.Mail = :mail')
            ->setParameter('mail', $mail)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
