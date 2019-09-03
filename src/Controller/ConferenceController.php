<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\User;
use App\Form\ConferenceType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/conference", name="conference")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {   $conference = new Conference();
        $form = $this->createForm(ConferenceType::class, $conference);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($conference);
            $em->flush();
            // $this->redirectToRoute(‘register_sucess’);
        }

        return $this->render('conference/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
