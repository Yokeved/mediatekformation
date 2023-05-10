<?php

namespace App\BackOffice\Controller;

use App\BackOffice\Repository\CategorieRepository;
use App\BackOffice\Repository\FormationRepository;
use App\BackOffice\Repository\PlaylistRepository;
use App\BackOffice\Entity\Categorie;
use App\BackOffice\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\BackOffice\Form\CategorieType;


/**
 * Controleur des formations
 *
 * @author emds
 */
class CategoriesController extends AbstractController
{

    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;

        
    public function __construct(
        FormationRepository $formationRepository,
        CategorieRepository $categorieRepository,
        PlaylistRepository $playlistRepository
        ) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
        $this->playlistRepository= $playlistRepository;
    }

    /**
     * @Route("/backoffice/categories", name="categoriesbackoffice")
     * @return Response
     */
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        $playlists  = $this->playlistRepository->findAll();

        return $this->render("backoffice/pages/categories.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'playlists'  => $playlists
        ]);
    }

    /**
     * @Route("/backoffice/categories/categorie/{id}", name="categories.delete")
     * @param type $id
     * @return Response
     */
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $categorie = $this->categorieRepository->find($id);

        // Vérifie si la playlist contient des formations
        $formations = count($categorie->getFormations());
        if ($formations > 0) {
            // Redirige l'utilisateur vers la page des playlists sans supprimer la playlist
            return $this->redirectToRoute('categoriesbackoffice');
        }

        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->redirectToRoute('categoriesbackoffice');
    }


    /**
     * @Route("/backoffice/categorieadd", name="categoriesbackoffice.add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categories = $this->categorieRepository->findAll();
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();
            $existingCat = $this->categorieRepository->findBy(['name' => $name]);

            if (!$existingCat) { // Si aucune catégorie n'existe avec ce nom
                $categorie->setName($name);
                $entityManager->persist($categorie);
                $entityManager->flush();
                $this->addFlash('success', 'Categorie ajoutée avec succès !');
                return $this->redirectToRoute('categoriesbackoffice');
            } else {
                $this->addFlash('error', 'Une catégorie avec ce nom existe déjà.');
            }
        } elseif ($form->isSubmitted()) {
            $this->addFlash('error', 'Le formulaire n\'est pas valide.');
        }
    
        return $this->render('backoffice/pages/categorieadd.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

}

