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

    public function findPatientsBySearch(?string $search = null): array
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->leftJoin('u.profil', 'p')
            ->leftJoin('u.login', 'l')
            ->where('p.Role = :role')
            ->setParameter('role', 'ROLE_PATIENT')
            ->orderBy('u.Nom', 'ASC');

        if ($search !== null && $search !== '') {
            $queryBuilder
                ->andWhere('u.Nom LIKE :search OR u.Prenom LIKE :search OR l.Mail LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function findUtilisateursBySearch(?string $search = null): array
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->leftJoin('u.profil', 'p')
            ->leftJoin('u.login', 'l')
            ->orderBy('u.Nom', 'ASC');

        if ($search !== null && $search !== '') {
            $queryBuilder
                ->andWhere('u.Nom LIKE :search OR u.Prenom LIKE :search OR u.Ville_res LIKE :search OR u.CP LIKE :search OR l.Mail LIKE :search OR p.Role LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}
