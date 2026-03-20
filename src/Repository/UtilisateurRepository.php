<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Récupère les utilisateurs ayant un profil avec un rôle spécifique
     */
    public function findByRoleName(string $roleName): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.profil', 'p')
            ->where('p.Role = :role')
            ->setParameter('role', $roleName)
            ->getQuery()
            ->getResult();
    }
}
