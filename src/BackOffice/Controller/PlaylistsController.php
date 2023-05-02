<?php
namespace App\BackOffice\Controller;

use App\BackOffice\Repository\CategorieRepository;
use App\BackOffice\Repository\FormationRepository;
use App\BackOffice\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController
{
    
    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
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
    
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRespository
        ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("/backoffice/playlists", name="playlists")
     * @return Response
     */
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render("backoffice/pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/backoffice/playlists/tri/{champ}/{ordre}", name="playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response
    {
        switch($champ){
        case "name":
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        break;
        case "nbformations":
            $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
        break;
        }
            $categories = $this->categorieRepository->findAll();
        return $this->render("backoffice/pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
     }
    
    /**
     * @Route("/backoffice/playlists/recherche/{champ}", name="playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContain($champ, Request $request): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render("backoffice/pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
        ]);
    }
    
    /**
     * @Route("/backoffice/playlists/recherche/{champ}/{table}", name="playlists.findallcontainInTable")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainInTable($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValueInTable($champ, $valeur, $table);

        $categories = $this->categorieRepository->findAll();
        return $this->render("backoffice/pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * @Route("/backoffice/playlists/playlist/{id}", name="playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("backoffice/pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }

    /**
     * @Route("/backoffice/playlists/playlist/{id}", name="playlist.delete")
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $playlist = $this->playlistRepository->find($id);
        
        // Vérifie si la playlist contient des formations
        $nbFormations = count($playlist->getFormations());
        if ($nbFormations > 0) {
            // Redirige l'utilisateur vers la page des playlists sans supprimer la playlist
            return $this->redirectToRoute('playlistsbackoffice');
        }
        
        // Supprime la playlist
        $entityManager->remove($playlist);
        $entityManager->flush();
        
        // Redirige l'utilisateur vers la page des playlists après la suppression
        return $this->redirectToRoute('playlistsbackoffice');
    }

    /**
     * @Route("/backoffice/playlistadd", name="playlistsbackoffice.add")
     * @param Request $request
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function add(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $formations = $this->formationRepository->findAll();
        $playlist = $this->playlistRepository->find($id);
        $playlists = $this->playlistRepository->findAll();


        return $this->render('backoffice/pages/playlistadd.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
            'playlists' => $playlists,
            'playlist' => $playlist,
        ]);
    }


}

