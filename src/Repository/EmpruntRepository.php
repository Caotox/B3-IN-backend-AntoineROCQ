<?php

namespace App\Repository;

use App\Entity\Emprunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    public function countActiveEmpruntsByUser(int $utilisateurId): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.utilisateur = :utilisateurId')
            ->andWhere('e.dateRetour IS NULL')
            ->setParameter('utilisateurId', $utilisateurId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findActiveEmpruntsByUserSortedByDate(int $utilisateurId): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.utilisateur = :utilisateurId')
            ->andWhere('e.dateRetour IS NULL')
            ->orderBy('e.dateEmprunt', 'ASC')
            ->setParameter('utilisateurId', $utilisateurId)
            ->getQuery()
            ->getResult();
    }
}
