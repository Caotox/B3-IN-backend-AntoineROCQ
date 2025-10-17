<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

#[Route('/api/livres', name: 'api_livre_')]
class LivreController extends AbstractController
{
    private EntityManagerInterface $em;
    private LivreRepository $livreRepository;
    private AuteurRepository $auteurRepository;
    private CategorieRepository $categorieRepository;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository,
        CategorieRepository $categorieRepository,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->livreRepository = $livreRepository;
        $this->auteurRepository = $auteurRepository;
        $this->categorieRepository = $categorieRepository;
        $this->logger = $logger;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $livres = $this->livreRepository->findAll();
            $data = array_map(function (Livre $livre) {
                return $this->serializeLivre($livre);
            }, $livres);

            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error listing books: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        try {
            $livre = $this->livreRepository->find($id);
            
            if (!$livre) {
                return new JsonResponse(['error' => 'Livre non trouvé'], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse($this->serializeLivre($livre), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error showing book: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
            }

            if (!isset($data['titre']) || !isset($data['auteur_id']) || !isset($data['categorie_id'])) {
                return new JsonResponse(['error' => 'Champs requis manquants'], Response::HTTP_BAD_REQUEST);
            }

            $auteur = $this->auteurRepository->find($data['auteur_id']);
            if (!$auteur) {
                return new JsonResponse(['error' => 'Auteur non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $categorie = $this->categorieRepository->find($data['categorie_id']);
            if (!$categorie) {
                return new JsonResponse(['error' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
            }

            $livre = new Livre();
            $livre->setTitre($data['titre']);
            $livre->setIdAuteur($auteur);
            $livre->setCategorie($categorie);
            $livre->setDisponible($data['disponible'] ?? true);

            if (isset($data['datePublication'])) {
                try {
                    $livre->setDatePublication(new \DateTime($data['datePublication']));
                } catch (\Exception $e) {
                    return new JsonResponse(['error' => 'Format de date invalide pour datePublication'], Response::HTTP_BAD_REQUEST);
                }
            }

            $this->em->persist($livre);
            $this->em->flush();

            $this->logger->info('Book created with ID: ' . $livre->getId());

            return new JsonResponse($this->serializeLivre($livre), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->logger->error('Error creating book: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $livre = $this->livreRepository->find($id);
            
            if (!$livre) {
                return new JsonResponse(['error' => 'Livre non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);
            
            if (!$data) {
                return new JsonResponse(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
            }

            if (isset($data['titre'])) {
                $livre->setTitre($data['titre']);
            }

            if (isset($data['datePublication'])) {
                try {
                    $livre->setDatePublication(new \DateTime($data['datePublication']));
                } catch (\Exception $e) {
                    return new JsonResponse(['error' => 'Format de date invalide pour datePublication'], Response::HTTP_BAD_REQUEST);
                }
            }

            if (isset($data['disponible'])) {
                $livre->setDisponible($data['disponible']);
            }

            if (isset($data['auteur_id'])) {
                $auteur = $this->auteurRepository->find($data['auteur_id']);
                if (!$auteur) {
                    return new JsonResponse(['error' => 'Auteur non trouvé'], Response::HTTP_NOT_FOUND);
                }
                $livre->setIdAuteur($auteur);
            }

            if (isset($data['categorie_id'])) {
                $categorie = $this->categorieRepository->find($data['categorie_id']);
                if (!$categorie) {
                    return new JsonResponse(['error' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
                }
                $livre->setCategorie($categorie);
            }

            $this->em->flush();

            $this->logger->info('Book updated with ID: ' . $livre->getId());

            return new JsonResponse($this->serializeLivre($livre), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error updating book: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $livre = $this->livreRepository->find($id);
            
            if (!$livre) {
                return new JsonResponse(['error' => 'Livre non trouvé'], Response::HTTP_NOT_FOUND);
            }

            $this->em->remove($livre);
            $this->em->flush();

            $this->logger->info('Book deleted with ID: ' . $id);

            return new JsonResponse(['message' => 'Livre supprimé'], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error deleting book: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Une erreur est survenue'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function serializeLivre(Livre $livre): array
    {
        return [
            'id' => $livre->getId(),
            'titre' => $livre->getTitre(),
            'datePublication' => $livre->getDatePublication()?->format('Y-m-d'),
            'disponible' => $livre->isDisponible(),
            'auteur' => [
                'id' => $livre->getIdAuteur()->getId(),
                'nom' => $livre->getIdAuteur()->getNom(),
                'prenom' => $livre->getIdAuteur()->getPrenom(),
            ],
            'categorie' => [
                'id' => $livre->getCategorie()->getId(),
                'nom' => $livre->getCategorie()->getNom(),
            ],
        ];
    }
}
