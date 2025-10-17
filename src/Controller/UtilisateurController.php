<?php

namespace App\Controller;

use App\Repository\EmpruntRepository;
use App\Repository\LivreRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

#[Route('/api/utilisateurs', name: 'api_utilisateur_')]
class UtilisateurController extends AbstractController
{
    private EmpruntRepository $empruntRepository;
    private LivreRepository $livreRepository;
    private UtilisateurRepository $utilisateurRepository;
    private LoggerInterface $logger;

    public function __construct(
        EmpruntRepository $empruntRepository,
        LivreRepository $livreRepository,
        UtilisateurRepository $utilisateurRepository,
        LoggerInterface $logger
    ) {
        $this->empruntRepository = $empruntRepository;
        $this->livreRepository = $livreRepository;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->logger = $logger;
    }

    #[Route('/{id}/emprunts', name: 'emprunts', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getEmprunts(int $id): JsonResponse
    {
        try {
            $utilisateur = $this->utilisateurRepository->find($id);
            
            if (!$utilisateur) {
                return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $emprunts = $this->empruntRepository->findActiveEmpruntsByUserSortedByDate($id);
            $count = count($emprunts);

            $data = [
                'utilisateur_id' => $id,
                'nombre_emprunts' => $count,
                'emprunts' => array_map(function ($emprunt) {
                    return [
                        'id' => $emprunt->getId(),
                        'livre' => [
                            'id' => $emprunt->getLivre()->getId(),
                            'titre' => $emprunt->getLivre()->getTitre(),
                        ],
                        'dateEmprunt' => $emprunt->getDateEmprunt()->format('Y-m-d H:i:s'),
                    ];
                }, $emprunts)
            ];

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error getting user borrowings: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/auteur/{auteurId}/livres', name: 'auteur_livres', methods: ['GET'], requirements: ['auteurId' => '\d+'])]
    public function getLivresAuteurEmpruntes(int $auteurId, Request $request): JsonResponse
    {
        try {
            $dateDebut = $request->query->get('dateDebut');
            $dateFin = $request->query->get('dateFin');

            if (!$dateDebut || !$dateFin) {
                return new JsonResponse(['error' => 'Les paramètres dateDebut et dateFin sont requis'], Response::HTTP_BAD_REQUEST);
            }

            try {
                $startDate = new \DateTime($dateDebut);
                $endDate = new \DateTime($dateFin);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Format de date invalide'], Response::HTTP_BAD_REQUEST);
            }

            $livres = $this->livreRepository->findBooksOfAuthorBorrowedBetweenDates($auteurId, $startDate, $endDate);

            $data = array_map(function ($livre) {
                return [
                    'id' => $livre->getId(),
                    'titre' => $livre->getTitre(),
                    'datePublication' => $livre->getDatePublication()?->format('Y-m-d'),
                    'auteur' => [
                        'id' => $livre->getAuteur()->getId(),
                        'nom' => $livre->getAuteur()->getNom(),
                        'prenom' => $livre->getAuteur()->getPrenom(),
                    ],
                ];
            }, $livres);

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error getting author books borrowed between dates: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
