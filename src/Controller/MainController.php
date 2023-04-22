<?php

namespace App\Controller;

use App\Entity\Agents;
use App\Form\DemandeCodeType;
use App\Repository\AgentsRepository;
use App\Repository\ComptesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/Main", name="demandeCode")
     */
    public function demandeCode(Request $request, EntityManagerInterface $em, AgentsRepository $repo, ComptesRepository $repocomptes)
    {
        $agent= new Agents();
        $form = $this->createForm(DemandeCodeType::class, $agent);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {

        $fonction = $agent->getFonction();
        $soft = $agent->getSofts();

        $em ->persist($agent);
        $em->flush();

        $compte = $repocomptes->findBy(['Fonction' => $fonction, 'Soft' => $soft, 'IsUsed' => 0],['id' => 'ASC'],1,[]);
        dd($compte);


        }

        return $this->render('Main/demandeCode.html.twig', [
            'form' => $form->createView()
            ]);
    }
    
}
