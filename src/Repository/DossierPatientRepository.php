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
            ->where('u.nom LIKE :search')
            ->orWhere('u.prenom LIKE :search')
            ->orWhere('l.mail LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }
}
