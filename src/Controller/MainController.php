<?php

namespace App\Controller;

use App\Entity\Agents;
use App\Entity\Comptes;
use App\Form\DemandeCodeType;
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
        
        //On envoie l'agent dans la bdd
        $em ->persist($agent);
        $em->flush();
        //Envoi de l'email à hotline et dpi pour la traçabilité
        // $email = (new Email())
        //    ->from('dpi@ch-calais.fr')
        //    ->to('dpi@ch-calais.fr', 'hotline@ch-calais.fr')
        //    ->subject('Code temporaire utilisé')
        //    ->text('Email de test pour tester la config');
        // $mailer->send($email);



        return $this->render('Main/affichageCode.html.twig',['Agents' => $agent, 'Comptes' => $compteagent]);

        }

        return $this->render('Main/demandeCode.html.twig', [
            'form' => $form->createView()
            ]);
    }
    
}
