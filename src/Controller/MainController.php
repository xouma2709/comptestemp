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
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


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
        date_default_timezone_set('UTC');
        $jour = date('l');
        $heure = date('H:i');
        $user = $this->getUser();
        $extrafields = $user->getextraFields();
        $maildemandeur = $extrafields['mail'][0];
        $comptedemandeur = $user->getUsername();
        $identitedemandeur = $extrafields['displayName'][0];
        $demandeur = "le compte a été demandé par ".$identitedemandeur." avec le compte ".$comptedemandeur.". Mail : ".$maildemandeur;
        




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
        $prenomagent = ucwords($prenomagent);


        //On vérifie sur vision que l'agent n'est pas connu
        $link = mysqli_connect('10.250.1.252:3306', 'userReadOnly', 'Applicatif$1', 'gestioncomptes') or die("Erreur de connexion"); 
        $req = mysqli_query($link, "SELECT * FROM agents WHERE nom = '.$nomagent.' AND prenom = '.$prenomagent.'");
        $count = mysqli_num_rows($req);
        //Si l'agent est connu des services informatique
            if ($count > 0 ){
                switch ($jour){
                    case "Monday":
                    case "Tuesday":
                    case "Wednesday":
                    case "Thursday":
                    case "Friday":
                        if ($heure > '06:00' && $heure < '15:00'){
                                    return $this->render('Main/infoouvert.html.twig',['Agents' => $agent]);

                        }
                        else{
                            return $this->render('Main/astreintetech.html.twig',['Agents' => $agent]);

                        }
                        break;
                    
                    case "Saturday":
                    case "Sunday":
                        if ($heure > '06:00' && $heure < '16:00'){
                            return $this->render('Main/astreinteappli.html.twig',['Agents' => $agent]);

                        }
                        else{
                            echo 'Astreinte technique';
                            return $this->render('Main/astreintetech.html.twig',['Agents' => $agent]);

                        }
                        break;

                }
            }else{

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
        $agent -> setDemandeur($demandeur);
        $agent->setIsPJ(0);
        //On envoie l'agent dans la bdd
        $em ->persist($agent);
        $em->flush();
        



        return $this->render('Main/demandePieceJointe.html.twig',['Agents' => $agent]);

            }

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
    public function ajoutPJ($id, Request $request, EntityManagerInterface $em, AgentsRepository $repo, MailerInterface $mailer,MailerInterface $mailer_mdp, ComptesRepository $repocomptes)
    {
        $agent = $repo->find($id);
        $compte = $agent->getCompte();
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

           $email = (new TemplatedEmail())
           -> from('dpi@ch-calais.fr')
           -> to('m.houzet@ch-calais.fr')
           -> subject('Une demande de code temporaire a été intitiée')
           -> htmlTemplate('Emails/demandecodeaboutie.html.twig')
           -> context([
                'Agents' => $agent,
                'Comptes' => $compte
           ]);
           $mailer->send($email);


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
