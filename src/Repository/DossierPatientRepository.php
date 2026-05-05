<?php

namespace App\Repository;

use App\Entity\DossierPatient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DossierPatient>
 */
class DossierPatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DossierPatient::class);
    }

    public function save(DossierPatient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DossierPatient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère un DossierPatient pour un Utilisateur donné
     */
    public function findByUtilisateur($utilisateur): ?DossierPatient
    {
        return $this->createQueryBuilder('d')
            ->where('d.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche les dossiers par email, prénom ou nom du patient
     */
    public function findBySearchConsultation(string $search): array
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.utilisateur', 'u')
            ->leftJoin('u.login', 'l')
            ->where('u.Nom LIKE :search')
            ->orWhere('u.Prenom LIKE :search')
            ->orWhere('l.Mail LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }

    public function findDossiersGreffeBySearch(?string $search = null): array
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->leftJoin('d.utilisateur', 'u')
            ->leftJoin('u.login', 'l')
            ->where('d.utilisateur IS NOT NULL')
            ->orderBy('u.Nom', 'ASC');

        if ($search !== null && $search !== '') {
            $queryBuilder
                ->andWhere('d.id_dossier_patient LIKE :search OR u.Nom LIKE :search OR u.Prenom LIKE :search OR l.Mail LIKE :search OR d.Etat_greffe LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}
