<?php

namespace App\Controller;

use App\Entity\Comptes;
use App\Entity\Fonctions;
use App\Entity\Softs;
use App\Form\AddFonctionType;
use App\Form\AddSoftType;
use App\Form\GenererComptesType;
use App\Repository\ComptesRepository;
use App\Repository\AgentsRepository;
use App\Repository\FonctionsRepository;
use App\Repository\SoftsRepository;
use App\Repository\DocumentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/Admin", name="Admin")
     */
    public function index()
    {
        //Page d'accueil de l'administration de l'appli
            return $this->render('Admin/index.html.twig');
        
    }

    /**
     * @Route("/Admin/showFonctions", name="showFonctions")
     */
    public function showFonctions(FonctionsRepository $repo) : Response
    {
        //Récupération de toutes les fonctions existantes
        $fonctions = $repo->findBy([],['LibelleFonction' => 'ASC']);
        return $this->render('Admin/showFonctions.html.twig', ['Fonctions' => $fonctions]);

    }


    /**
     * @Route("/Admin/addFonction", name="addFonction")
     */
    public function addFonction(Request $request, EntityManagerInterface $em, FonctionsRepository $repo): Response
    {
        
        //création d'une fonction
        $fonction= new Fonctions();
        $form = $this->createForm(AddFonctionType::class, $fonction);

        $form->handleRequest($request);
        //Si formulaire rempli correctement
        if ($form->isSubmitted() && $form->isValid())
        {

        //ajout de la fonction en base
        $em ->persist($fonction);
        $em->flush();

        return $this->redirectToRoute('Admin') ;

        }
        //Affichage du formulaire
        return $this->render('Admin/addFonction.html.twig', [
            'form' => $form->createView()
            ]);
    }

    /**
     * @Route("Admin/deleteFonction/{id}", name="deleteFonction")
     */
    public function deleteFonction($id, Request $request, EntityManagerInterface $em, FonctionsRepository $repo)
    {
        //Suppression de la fonction
        $fonction = $repo->find($id);
        $em -> remove($fonction);
        $em -> flush();
        return $this->redirectToRoute('showFonctions') ;

    }



    /**
     * @Route("/Admin/showSofts", name="showSofts")
     */
    public function showSofts(SoftsRepository $repo) : Response
    {
        //Récupération de tous les softs existantes
        $Softs = $repo->findBy([],['LibelleSoft' => 'ASC']);
        return $this->render('Admin/showSofts.html.twig', ['Softs' => $Softs]);

    }


    /**
     * @Route("/Admin/addSoft", name="addSoft")
     */
    public function addSoft(Request $request, EntityManagerInterface $em, SoftsRepository $repo): Response
    {
        //création d'un soft
        $Soft= new Softs();
        $form = $this->createForm(AddSoftType::class, $Soft);

        $form->handleRequest($request);   

        //Si formulaire rempli correctement

        if ($form->isSubmitted() && $form->isValid())
        {

        //On ajoute le soft en base
        $em ->persist($Soft);
        $em->flush();

        return $this->redirectToRoute('Admin') ;

        }

        return $this->render('Admin/addSoft.html.twig', [
            'form' => $form->createView()
            ]);
    }

    /**
     * @Route("Admin/deleteSoft/{id}", name="deleteSoft")
     */
    public function deleteSoft($id, Request $request, EntityManagerInterface $em, SoftsRepository $repo)
    {
        //Suppression du soft en base
        $Soft = $repo->find($id);
        $em -> remove($Soft);
        $em -> flush();
        return $this->redirectToRoute('showSofts') ;

    }

    /**
     * @Route("Admin/genererComptes", name="genererComptes")
     */
    public function genererComptes(Request $request, EntityManagerInterface $em, ComptesRepository $repo)
    {
        //Fonction de génération de mot de passe aléatoire sans caractères similaires
        function passgen2($nbChar){
            return substr(str_shuffle('abcdefghijkmnpqrstuvwxyzABCEFGHJKMNPQRSTUVWXYZ23456789'),1, $nbChar);
            }

        //On cherche l'id du dernier compté temporaire créé
        $lastcompte = $repo->findOneBy([],['id'=>'desc']);
        $idlastcompte = $lastcompte->getId();

        //définition des variables pour la boucle de création des comptes
        $i = $idlastcompte + 1;
        $j = $i + 10;

        //Récupération des données du formulaire rempli
        $compte = new Comptes();
        $form = $this->createForm(GenererComptesType::class, $compte);
    
        $form->handleRequest($request);
        //Si le formulaire est rempli
        if ($form->isSubmitted() && $form->isValid())
        {
            //Récupération des données saisies
            $fonctioncode = $compte->getFonction()->getCode();
            $soft = $compte -> getSoft();
            $fonction = $compte->getFonction();
            //Boucle pour la création de 10 comptes d'un coup
            for ($i; $i < $j; $i++)
            {
            //Création du compte à l'interieur de la boucle
            $comptes = new Comptes();
            //Définition de l'identifiant de connexion sous la forme "CodeFonctionTempId"
            $login = $fonctioncode.'temp'.$i;
            //Génération du mot de passe
            $pwd = passgen2(12);
            //Attitrbution des valeurs du formulaire au compte
            $comptes -> setLogin($login);
            $comptes -> setFonction($fonction);
            $comptes -> setSoft($soft);
            $comptes -> setPassword($pwd);
            $comptes -> setIsUsed(0);
            //envoi du compte en base
            $em -> persist($comptes);
            $em ->flush();

            }

        

        return $this->redirectToRoute('showLastComptes') ;


       }
       //Afffichage du formulaire
        return $this->render('Admin/genererComptes.html.twig', [
            'form' => $form->createView()
            ]);


    }


    /**
     * @Route("Admin/showComptes", name="showComptes")
     */
    public function showComptes(ComptesRepository $repo, AgentsRepository $repoagents){
        //Requete pour recuperer les comptes deja créés
        $comptes = $repo->findAll();
        $agents = $repoagents->findAll();
        //Affichage de la page
        return $this->render('Admin/showComptes.html.twig', ['Comptes' => $comptes, 'Agents' => $agents]);

    }

    /**
     * @Route("Admin/showLastComptes", name="showLastComptes")
     */
    public function showLastComptes(ComptesRepository $repo, AgentsRepository $repoagents){
        //Requete pour recuperer les comptes deja créés
        $comptes = $repo->findBy([],['id'=>'desc'],10);;
        $agents = $repoagents->findAll();
        //Affichage de la page
        return $this->render('Admin/showLastComptes.html.twig', ['Comptes' => $comptes, 'Agents' => $agents]);

    }
    /**
    * @Route("/Admin/voirDocument/{doc}", name="voirDocument")
    */
    public function voirDocument($doc, Request $request, EntityManagerInterface $em, DocumentsRepository $repodoc): Response
    {
            $document = $repodoc->find($doc);
            $nomdoc = $document->getNomDocument(); 
            $file = new File($this->getParameter('kernel.project_dir').'/public/documents/'.$nomdoc);

            return $this->file($file, 'my_invoice.pdf', ResponseHeaderBag::DISPOSITION_INLINE);
    }


}
