<?php
namespace App\BackOffice\Controller;

use App\BackOffice\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur de l'accueil
 *
 * @author emds
 */
class AccueilController extends AbstractController
{
      
    /**
     * @var FormationRepository
     */
    private $repository;
    
    /**
     *
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * @Route("/backoffice", name="backoffice")
     * @return Response
     */
    public function index(): Response
    {
        $formations = $this->repository->findAllLasted(2);
        return $this->render("backoffice/pages/accueil.html.twig", [
            'formations' => $formations
        ]);
    }
    
    /**
     * @Route("/backoffice/cgu", name="backoffice/cgu")
     * @return Response
     */
    public function cgu(): Response
    {
        return $this->render("backoffice/pages/cgu.html.twig");
    }
}
