<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Repository\EmpruntRepository;
use App\Repository\LivreRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

#[Route('/api/emprunts', name: 'api_emprunt_')]
class EmpruntController extends AbstractController
{
    private EntityManagerInterface $em;
    private EmpruntRepository $empruntRepository;
    private LivreRepository $livreRepository;
    private UtilisateurRepository $utilisateurRepository;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        EmpruntRepository $empruntRepository,
        LivreRepository $livreRepository,
        UtilisateurRepository $utilisateurRepository,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->empruntRepository = $empruntRepository;
        $this->livreRepository = $livreRepository;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->logger = $logger;
    }

    #[Route('/emprunter', name: 'borrow', methods: ['POST'])]
    public function emprunter(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data || !isset($data['utilisateur_id']) || !isset($data['livre_id'])) {
                return new JsonResponse(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
            }

            $utilisateur = $this->utilisateurRepository->find($data['utilisateur_id']);
            if (!$utilisateur) {
                return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $livre = $this->livreRepository->find($data['livre_id']);
            if (!$livre) {
                return new JsonResponse(['error' => 'Livre non trouvé'], Response::HTTP_NOT_FOUND);
            }

            if (!$livre->isDisponible()) {
                return new JsonResponse(['error' => 'Le livre n\'est pas disponible'], Response::HTTP_CONFLICT);
            }

            $activeEmpruntsCount = $this->empruntRepository->countActiveEmpruntsByUser($utilisateur->getId());
            if ($activeEmpruntsCount >= 4) {
                return new JsonResponse(['error' => 'L\'utilisateur a atteint la limite de 4 emprunts'], Response::HTTP_CONFLICT);
            }

            $emprunt = new Emprunt();
            $emprunt->setUtilisateur($utilisateur);
            $emprunt->setLivre($livre);
            $emprunt->setDateEmprunt(new \DateTime());

            $livre->setDisponible(false);

            $this->em->persist($emprunt);
            $this->em->flush();

            $this->logger->info('Book borrowed - User ID: ' . $utilisateur->getId() . ', Book ID: ' . $livre->getId());

            return new JsonResponse([
                'message' => 'Emprunt créé avec succès',
                'emprunt' => $this->serializeEmprunt($emprunt)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->logger->error('Error borrowing book: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/retourner/{id}', name: 'return', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function retourner(int $id): JsonResponse
    {
        try {
            $emprunt = $this->empruntRepository->find($id);

            if (!$emprunt) {
                return new JsonResponse(['error' => 'Emprunt non trouvé'], Response::HTTP_NOT_FOUND);
            }

            if ($emprunt->getDateRetour() !== null) {
                return new JsonResponse(['error' => 'Ce livre a déjà été retourné'], Response::HTTP_CONFLICT);
            }

            $emprunt->setDateRetour(new \DateTime());
            $emprunt->getLivre()->setDisponible(true);

            $this->em->flush();

            $this->logger->info('Book returned - Emprunt ID: ' . $emprunt->getId());

            return new JsonResponse([
                'message' => 'Livre retourné avec succès',
                'emprunt' => $this->serializeEmprunt($emprunt)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error returning book: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function serializeEmprunt(Emprunt $emprunt): array
    {
        return [
            'id' => $emprunt->getId(),
            'utilisateur' => [
                'id' => $emprunt->getUtilisateur()->getId(),
                'nom' => $emprunt->getUtilisateur()->getNom(),
                'prenom' => $emprunt->getUtilisateur()->getPrenom(),
            ],
            'livre' => [
                'id' => $emprunt->getLivre()->getId(),
                'titre' => $emprunt->getLivre()->getTitre(),
            ],
            'dateEmprunt' => $emprunt->getDateEmprunt()->format('Y-m-d H:i:s'),
            'dateRetour' => $emprunt->getDateRetour()?->format('Y-m-d H:i:s'),
        ];
    }
}
