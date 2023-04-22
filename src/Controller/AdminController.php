<?php

namespace App\Controller;

use App\Entity\Comptes;
use App\Entity\Fonctions;
use App\Entity\Softs;
use App\Form\AddFonctionType;
use App\Form\AddSoftType;
use App\Form\GenererComptesType;
use App\Repository\ComptesRepository;
use App\Repository\FonctionsRepository;
use App\Repository\SoftsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/Admin", name="Admin")
     */
    public function index()
    {
        
            return $this->render('Admin/index.html.twig');
        
    }

    /**
     * @Route("/Admin/showFonctions", name="showFonctions")
     */
    public function showFonctions(FonctionsRepository $repo) : Response
    {
        $fonctions = $repo->findBy([],['LibelleFonction' => 'ASC']);
        return $this->render('Admin/showFonctions.html.twig', ['Fonctions' => $fonctions]);

    }


    /**
     * @Route("/Admin/addFonction", name="addFonction")
     */
    public function addFonction(Request $request, EntityManagerInterface $em, FonctionsRepository $repo): Response
    {
        
        $fonction= new Fonctions();
        $form = $this->createForm(AddFonctionType::class, $fonction);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {


        $em ->persist($fonction);
        $em->flush();

        return $this->redirectToRoute('Admin') ;

        }

        return $this->render('Admin/addFonction.html.twig', [
            'form' => $form->createView()
            ]);
    }

    /**
     * @Route("Admin/deleteFonction/{id}", name="deleteFonction")
     */
    public function deleteFonction($id, Request $request, EntityManagerInterface $em, FonctionsRepository $repo)
    {
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
        $Softs = $repo->findBy([],['LibelleSoft' => 'ASC']);
        return $this->render('Admin/showSofts.html.twig', ['Softs' => $Softs]);

    }


    /**
     * @Route("/Admin/addSoft", name="addSoft")
     */
    public function addSoft(Request $request, EntityManagerInterface $em, SoftsRepository $repo): Response
    {
        
        $Soft= new Softs();
        $form = $this->createForm(AddSoftType::class, $Soft);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {


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


        $compte = new Comptes();

        function passgen2($nbChar){
            return substr(str_shuffle('abcdefghijkmnpqrstuvwxyzABCEFGHJKMNPQRSTUVWXYZ23456789'),1, $nbChar);
            }
        
        $form = $this->createForm(GenererComptesType::class, $compte);

        $form->handleRequest($request);
        $lastcompte = $repo->findOneBy([],['id'=>'desc']);
        $idlastcompte = $lastcompte->getId();
        $i = $idlastcompte + 1;
        if ($form->isSubmitted() && $form->isValid())
        {

        
    
        $fonctioncode = $compte->getFonction()->getCode();
        $soft = $compte -> getSoft();
        
        $login = $fonctioncode.'temp'.$i;

        $pwd = passgen2(12);
        $compte -> setLogin($login);

        $compte -> setPassword($pwd);
        $compte -> setIsUsed(0);

        $em -> persist($compte);
        $em ->flush();



        

        return $this->redirectToRoute('Admin') ;


       }

        return $this->render('Admin/genererComptes.html.twig', [
            'form' => $form->createView()
            ]);


    }


    /**
     * @Route("Admin/showComptes", name="showComptes")
     */
    public function showComptes(ComptesRepository $repo){
        $comptes = $repo->findAll();
        return $this->render('Admin/showComptes.html.twig', ['Comptes' => $comptes]);

    }


}
