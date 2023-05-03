<?php
namespace App\BackOffice\Controller;

use App\BackOffice\Repository\CategorieRepository;
use App\BackOffice\Repository\FormationRepository;
use App\BackOffice\Repository\PlaylistRepository;
use App\BackOffice\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Controleur des formations
 *
 * @author emds
 */
class FormationsController extends AbstractController
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
     * @Route("/backoffice/formations", name="formationsbackoffice")
     * @return Response
     */
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        $playlists  = $this->playlistRepository->findAll();
        return $this->render("backoffice/pages/formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'playlists'  => $playlists
        ]);
    }
    
    /**
     * @Route("/backoffice/formations/tri/{champ}/{ordre}", name="formationsbackoffice.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort($champ, $ordre): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre);
        $categories = $this->categorieRepository->findAll();
        return $this->render("backoffice/pages/formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/backoffice/formations/tri/{champ}/{ordre}/{table}", name="formationsbackoffice.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sortInTable($champ, $ordre, $table=""): Response
    {
        $formations = $this->formationRepository->findAllOrderByInTable($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render("backoffice/pages/formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    
    /**
     * @Route("/formations/recherche/{champ}", name="formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request): Response
    {
        $valeur = $request->get("recherche");

        $formations = $this->formationRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render("backoffice/pages/formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
        ]);
    }
 
        /**
     * @Route("/formations/recherche/{champ}/{table}", name="formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainInTable($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");

        $formations = $this->formationRepository->findByContainValueInTable($champ, $valeur, $table);

        $categories = $this->categorieRepository->findAll();
        return $this->render("backoffice/pages/formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table,
        ]);
    }
 

    /**
     * @Route("/backoffice/formations/formation/{id}", name="formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render("backoffice/pages/formation.html.twig", [
            'formation' => $formation
        ]);
    }
    
    /**
     * @Route("/backoffice/formations/formation/{id}", name="formations.delete")
     * @param type $id
     * @return Response
     */
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $formation = $this->formationRepository->find($id);
        $playlist = $formation->getPlaylist();

        if ($playlist) {
            $playlist->removeFormation($formation);
        }
        
        $entityManager->remove($formation);
        $entityManager->flush();

        return $this->redirectToRoute('formationsbackoffice');
    }


    /**
     * @Route("/backoffice/formations/formation/edit/{id}", name="formations.edit")
     * @param Request $request
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $formations = $this->formationRepository->findAll();
        $formation = $this->formationRepository->find($id);
        $categories = $this->categorieRepository->findAll();
        $formation_categorie = $this->formationRepository->find($id);
        $playlists = $this->playlistRepository->findAll();

        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée');
        }

        $form = $this->createForm(Formation::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Formation mise à jour avec succès');
            return $this->redirectToRoute('formations.list');
        }

        return $this->render('backoffice/pages/formationedit.html.twig', [
            'form' => $form->createView(),
            'formations' => $formations,
            'categories' => $categories,
            'playlists' => $playlists,
            'formation' => $formation,
            'formation_categorie' => $formation_categorie
        ]);
    }

    /**
     * @Route("/backoffice/formationadd", name="playlistsbackoffice.add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formations = $this->formationRepository->findAll();
        $playlists = $this->playlistRepository->findAll();
        $categories = $this->categorieRepository->findAll();


        return $this->render('backoffice/pages/formationadd.html.twig', [
            'formations' => $formations,
            'playlists' => $playlists,
            'categories'    =>$categories
        ]);
    }
}
