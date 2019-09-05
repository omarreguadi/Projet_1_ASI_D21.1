<?php

namespace App\Controller;


use App\Entity\Conference;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use App\Repository\ConferenceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
use Symfony\Component\OptionsResolver\OptionsResolver;
class HomeController extends Controller
{


    /**
     * @Route("/", name="home")
     */
    public function showConf(ConferenceRepository $conferenceRepository, VoteRepository $voteRepository ,Request $request)
    {
        $conf = $conferenceRepository->findAll();
        $vote = $voteRepository->avg();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $conf, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
            'avg' => $vote
        ]);
    }
    /**
     * @Route("/conference/{id}/{note}", name="vote")
     */
    public function vote(Conference $conference, int $note, EntityManagerInterface $entityManager)
    {
        if($this->getUser() !== null){
            if($this->getUser()->getRoles()[0] == "ROLE_USER"){
                if(in_array("ROLE_ADMIN", $this->getUser()->getRoles()) == "ROLE_ADMIN") {
                    throw new Exception("Admin can't modified");
                }else {
                    $userVote = $this->getUser()->getUserVote()->toArray();
                    foreach ($userVote as $value) {
                        if ($value->getConference()->getId() == $conference->getId()) {
                            throw new Exception("User has already vote ");
                        }
                    }
                    $votes = new Vote();
                    $votes->setUser($this->getUser());
                    $votes->setConference($conference);
                    $votes->setScore($note);

                    $entityManager->persist($votes);
                    $entityManager->flush();
                    return $this->redirectToRoute('home');
                }
            }
        } else{
            throw new Exception("User not connected");
        }
    }
    /**
     * @Route("/conference/{id}", name="conferenceId")
     */
    public function conferenceId(Conference $conference)
    {
        return $this->render('home/ConferenceId.html.twig', [
            'conference' => $conference
        ]);
    }
    /**
     * @Route("/search", name="search")
     */
    public function searchAction(Request $request){
        $data = $request->request->get('search');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT d FROM App\Entity\Conference d
            WHERE d.title LIKE :data')
            ->setParameter('data','%'.$data.'%');
        $res = $query->getResult();
        return $this->render('home/search.html.twig', array(
            'res' => $res,
            'data' => $data,
        ));
    }
}
