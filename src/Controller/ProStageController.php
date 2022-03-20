<?php

namespace App\Controller;

use App\Entity\Stage;
use App\Entity\Entreprise;
use App\Entity\Formation;
use App\Entity\User;
use App\Repository\EntrepriseRepository;
use App\Repository\FormationRepository;
use App\Repository\StageRepository;
use App\Form\StageType;
use App\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EntrepriseType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProStageController extends AbstractController
{
    /**
     * @Route("/", name="ProStage_accueil")
     */
    public function index(StageRepository $reposStage): Response
    {
        $stages = $reposStage->findStagesAvecEntreprises();

        return $this->render(
            'pro_stage/index.html.twig',
            ['stages' => $stages]
        );
    }

    /**
     * @Route("/entreprises", name="ProStage_entreprises")
     */
    public function afficherEntreprises(EntrepriseRepository $reposEntrep): Response
    {
        $entreprises = $reposEntrep->findAll();

        return $this->render(
            'pro_stage/afficherEntreprises.html.twig',
            ['entreprises' => $entreprises]
        );
    }

    /**
     * @Route("/inscription", name="ProStage_inscription")
     */
    public function ajouterUser(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $formulaireUser = $this->createForm(UserType::class, $user);

        $formulaireUser->handleRequest($request);

        if ($formulaireUser->isSubmitted() && $formulaireUser->isValid()){
            $user->setRoles(['ROLE_USER']);
            $encodagePassword = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodagePassword);
            $manager->persist($user);
            $manager->flush(); 

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/formulaireInscription.html.twig',
            [
                'vueFormulaireUser' => $formulaireUser->createView()
            ]
        );
    }

    /**
     * @Route("profile/stages/ajout", name="ProStage_ajout_stage")
     */
    public function ajouterStage(Request $request, EntityManagerInterface $manager)
    {
        $stage = new Stage();

        $formulaireStage = $this->createForm(StageType::class, $stage);

        $formulaireStage->handleRequest($request);

        if ($formulaireStage->isSubmitted() && $formulaireStage->isValid()){
            $manager->persist($stage);
            $manager->flush();

            return $this->redirectToRoute('ProStage_accueil');
        }

        return $this->render(
            'pro_stage/formulaireAjoutStage.html.twig',
            [
                'vueFormulaireStage' => $formulaireStage->createView(),
                'action' => "ajouter"
            ]
        );
    }
    
    /**
     * @Route("/stages/{id}", name="ProStage_stage")
     */
    public function afficherDetailStage(Stage $stage): Response
    {

        return $this->render(
            'pro_stage/affichageDetailStage.html.twig',
            ['stage' => $stage]
        );
    }

    

    /**
     * @Route("admin/entreprises/ajout", name="Prostage_ajout_entreprise")
     */
    public function ajouterEntreprise(Request $request, EntityManagerInterface $manager)
    {
        //Utilisation du EntityManagerInterface car ObjectManager ne voulait pas fonctionner
        $entreprise = new Entreprise();

        $formulaireEntreprise = $this->createForm(EntrepriseType::class, $entreprise);
            

            $formulaireEntreprise->handleRequest($request);
            
            if($formulaireEntreprise->isSubmitted() && $formulaireEntreprise->isValid())
            {
                $manager->persist($entreprise);
                $manager->flush();
                return $this->redirectToRoute('ProStage_entreprises');
            }

        return $this->render(
            'pro_stage/formulaireAjoutEntreprise.html.twig',
            [
                'vueFormulaireEntreprise' => $formulaireEntreprise->createView(), 'action'=>"ajouter"
            ]
        );
    }

    /**
     * @Route("/entreprises/modifier/{id}", name="Prostage_modif_entreprise")
     */
    public function modifierEntreprise(Request $request, EntityManagerInterface $manager, Entreprise $entreprise)
    {
    
    
        $formulaireEntreprise = $this->createForm(EntrepriseType::class, $entreprise);

            $formulaireEntreprise->handleRequest($request);
            
            if($formulaireEntreprise->isSubmitted())
            {
                $manager->persist($entreprise);
                $manager->flush();
                return $this->redirectToRoute('ProStage_entreprises');
            }

        return $this->render(
            'pro_stage/formulaireAjoutEntreprise.html.twig',
            [
                'vueFormulaireEntreprise' => $formulaireEntreprise->createView(), 'action'=>"modifier"
            ]
        );
    }

    /**
     * @Route("/entreprises/{id}", name="ProStage_detail_entreprise")
     */
    public function afficherDetailEntreprise(Entreprise $entreprise, StageRepository $reposStage): Response
    {
        // $stages = $reposStage->findByEntreprise($entreprise);
        $stages = $reposStage->findStagesPourUneEntreprise($entreprise->getNom());

        return $this->render(
            'pro_stage/affichageDetailEntreprise.html.twig',
            [
                'entreprise' => $entreprise,
                'stages'     => $stages
            ]
        );
    }

    
}
