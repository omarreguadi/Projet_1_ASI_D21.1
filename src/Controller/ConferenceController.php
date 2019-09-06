<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Form\ConferenceType;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/conference", name="conference")
     */
    public function create(Request $request, \Swift_Mailer $mailer, UserRepository $userRepository)
    {
        $conference = new Conference();
        $form = $this->createForm(ConferenceType::class, $conference);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conference);
            $entityManager->flush();
            $this->addFlash('success', 'la conference a ete cree !');

            $user = $userRepository->findAll();

            foreach ($user as $value){
                $message = (new \Swift_Message('Nouvelle conference'))
                    ->setFrom('omarreguadi@gmail.com')
                    ->setTo($value->getEmail())
                    ->setBody('You should see me from the profiler!');
                $mailer->send($message);
            }
            return $this->redirectToRoute('home');
        }

        return $this->render('conference/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/conference/edit/{id}", name="edit_conference")
     */
    public function edit(Request $request, Conference $conference = null)
    {
        if (!$conference) {
            $conference = new conference();
        }
        $form = $this->createForm(ConferenceType::class, $conference);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conference);
            $entityManager->flush();
            $this->addFlash('green', 'Modification enregistrer !');
            return $this->redirectToRoute('home');
        }

        return $this->render('conference/editconf.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/conference/topDix", name="topDix")
     */
    public function topDix(VoteRepository $voteRepository)
    {
        $conferences = $voteRepository->averageTopDix();

        return $this->render('conference/topDix.html.twig', [
            'conferences' => $conferences
        ]);
    }


    /**
     * @Route("/admin/conference/remove/{id}", name="remove_conference")
     * @ParamConverter("conference", options={"mapping"={"id"="id"}})
     */
    public function delete(EntityManagerInterface $entityManager, Conference $conference)
    {
        $entityManager->remove($conference);
        $entityManager->flush();
        $this->addFlash('notice', 'Element supprimer !');
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/user/conference/vote", name="conferenceVote")
     */
    public function conferenceVote(VoteRepository $voteRepository){
        $usercurrent = $this->getUser();
        $uservote = $voteRepository->averageByUser($usercurrent);
        return $this->render('conference/vote.html.twig',[
            'vote' => $uservote
        ]);
    }
    /**
     * @Route("/user/conference/withoutvote", name="conferenceWithoutVote")
     */
    public function conferenceWithoutVote(VoteRepository $voteRepository){
        $usercurrent = $this->getUser();
        $uservote = $voteRepository->averageWithoutUser($usercurrent);
        return $this->render('conference/withoutvote.html.twig',[
            'vote' => $uservote
        ]);

    }
}
