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
        // Utilise une sous-requête pour trouver le Login avec un Utilisateur associé
        $qb = $this->createQueryBuilder('l');
        
        return $qb
            ->addSelect('u')
            ->addSelect('p')
            ->innerJoin('l.utilisateurs', 'u')  // INNER JOIN pour avoir au moins 1 utilisateur
            ->leftJoin('u.profil', 'p')
            ->where('l.Mail = :mail')
            ->setParameter('mail', $mail)
            ->orderBy('l.id_login', 'DESC')  // Prend le plus récent
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
