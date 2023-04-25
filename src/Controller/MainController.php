<?php

namespace App\Controller;

use App\Entity\Agents;
use App\Entity\Comptes;
use App\Entity\Documents;
use App\Form\DemandeCodeType;
use App\Form\AddDocumentType;
use App\Repository\AgentsRepository;
use App\Repository\ComptesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MainController extends AbstractController
{
    /**
     * @Route("/Main", name="demandeCode")
     */
    public function demandeCode(Request $request, EntityManagerInterface $em, AgentsRepository $repo, ComptesRepository $repocomptes, MailerInterface $mailer)
    {
        $agent= new Agents();
        $form = $this->createForm(DemandeCodeType::class, $agent);
        $today = date('Y-m-d');



        //Si le formulaire est rempli correctement
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
        //réucpération des données
        $fonction = $agent->getFonction();
        $soft = $agent->getSofts();
        $nomagent = $agent->getNom();
        $nomagent=strtoupper($nomagent);
        $prenomagent = $agent->getPrenom();
        $prenomagents = ucwords($prenomagent);
        //On vérifie sur vision que l'agent n'est pas connu
        

        //Si l'agent est connu des services informatique


        //S'il n'est pas connu
        

        //On cherche un compte qui correspond aux critères
        $compte = $repocomptes->findBy(['Fonction' => $fonction, 'Soft' => $soft, 'IsUsed' => 0],['id' => 'ASC'],1,[]);
        //On récupere le compte
        $compteagent = $compte[0];
        //On le définit comme utilisé avec la date
        $compteagent -> setIsUsed(1);
        $compteagent -> setDateAttribution(\DateTime::createFromFormat ('Y-m-d', $today));
        $em->persist($compteagent);

        //On attribut le compte à l'agent
        $agent -> setCompte($compteagent);
        $agent -> setDateDemande(\DateTime::createFromFormat ('Y-m-d', $today));        
        $agent->setIsPJ(0);
        //On envoie l'agent dans la bdd
        $em ->persist($agent);
        $em->flush();
        



        return $this->render('Main/demandePieceJointe.html.twig',['Agents' => $agent]);

        }

        return $this->render('Main/demandeCode.html.twig', [
            'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/Main/{id}/affichercode", name="affichercode")
     */
    public function afficherCode($id, AgentsRepository $repo)
    {
        $agent = $repo->find($id);
        return $this->render('Main/affichageCode.html.twig',['Agents' => $agent]);
    }

    /**
     * @Route("/Main/{id}/ajoutpj", name="ajoutpj")
     */
    public function ajoutPJ($id, Request $request, EntityManagerInterface $em, AgentsRepository $repo, MailerInterface $mailer)
    {
        $agent = $repo->find($id);
        $doc= new Documents();
        $form = $this->createForm(AddDocumentType::class, $doc);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {          

            $uploadedFile = $form['Attachment']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/documents/';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = 'Piece_Identité'.$id.'.pdf';

            $uploadedFile->move(
               $destination,
               $newFilename
           );

           $doc->setNomDocument($newFilename);
           $doc->setAttachment($destination);
           $doc->setagent($agent);
           $em->persist($doc);
           $em->flush();

           $agent->setIsPJ(1);
           $em->persist($agent);
           $em->flush();
        return $this->render('Main/affichageCode.html.twig',['Agents' => $agent]);


        //Envoi de l'email à hotline et dpi pour la traçabilité
        // $email = (new Email())
        //    ->from('dpi@ch-calais.fr')
        //    ->to('dpi@ch-calais.fr', 'hotline@ch-calais.fr')
        //    ->subject('Code temporaire utilisé')
        //    ->text('Email de test pour tester la config');
        // $mailer->send($email);

        }
        return $this->render('Main/ajoutPieceJointe.html.twig', [
            'form' => $form->createView()
            ]);

    }
    
}
