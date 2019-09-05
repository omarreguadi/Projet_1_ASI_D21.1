<?php
namespace App\Controller;
use App\Entity\Conference;
use App\Form\ConferenceType;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
class ConferenceController extends AbstractController
{
    /**
     * @Route("/conference", name="conference")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(Request $request, \Swift_Mailer $mailer, UserRepository $userRepository)
    {   $conference = new Conference();
        $form = $this->createForm(ConferenceType::class, $conference);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($conference);
            $em->flush();
            $this->addFlash('notice', 'You created a new conference !');
            // $this->redirectToRoute(‘register_sucess’);
            $user = $userRepository->findAll();
            foreach ($user as $value){
                $message = (new \Swift_Message('Hello Email'))
                    ->setFrom('zineb.elamrani96@gmail.com')
                    ->setTo($value->getEmail())
                    ->setBody('Check the profiler!');
                $mailer->send($message);
            }
            return $this->redirectToRoute('home');
        }
        return $this->render('conference/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/admin/conference/edit_conference/{id}", name="edit_conference")
     * @param Request $request
     * @param Conference|null $conference
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
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
            $this->addFlash('notice', 'Modify register !');
            return $this->redirectToRoute('home');
        }
        return $this->render('conference/edit_conference.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/admin/conference/topten", name="topten")
     * @param VoteRepository $voteRepository
     * @return Response
     */
    public function topTen(VoteRepository $voteRepository)
    {
        $conferences = $voteRepository->avgTopTen();
        return $this->render('conference/topTen.html.twig', [
            'conferences' => $conferences
        ]);
    }
    public function delete(EntityManagerInterface $em, Conference $conference)
    {
        $em->remove($conference);
        $em->flush();
        $this->addFlash('notice', 'Item deleted !');
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/user/conference/vote", name="conferenceVote")
     * @param VoteRepository $voteRepository
     * @return Response
     */
    public function conferenceVote(VoteRepository $voteRepository){
        $usercurrent = $this->getUser();
        $uservote = $voteRepository->avgByUser($usercurrent);
        return $this->render('conference/vote.html.twig',[
            'vote' => $uservote
        ]);
    }
    /**
     * @Route("/user/conference/withoutcote", name="conferenceWithoutVote")
     * @param VoteRepository $voteRepository
     * @return Response
     */
    public function conferenceWithoutVote(VoteRepository $voteRepository){
        $usercurrent = $this->getUser();
        $uservote = $voteRepository->avgWithoutUser($usercurrent);
        return $this->render('conference/without_vote.html.twig',[
            'vote' => $uservote
        ]);
    }
}