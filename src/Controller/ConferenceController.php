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
     * @Route("/admin/conference", name="conference")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function create(Request $request, \Swift_Mailer $mailer, UserRepository $userRepository)
    {
        $conference = new Conference();
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
                $message = (new \Swift_Message('New conference'))
                    ->setFrom('zineb.elamrani96@gmail.com')
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
            $this->addFlash('green', 'Modify register !');
            return $this->redirectToRoute('home');
        }
        return $this->render('conference/editconf.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/conference/topDix", name="topDix")
     * @param VoteRepository $voteRepository
     */
    public function topDix(VoteRepository $voteRepository)
    {
        $conferences = $voteRepository->avgTopTen();

        return $this->render('conference/toptenconf.html.twig', [
            'conferences' => $conferences
        ]);
    }


    /**
     * @Route("/admin/conference/remove/{id}", name="remove_conference")
     * @ParamConverter("conference", options={"mapping"={"id"="id"}})
     * @param EntityManagerInterface $entityManager
     * @param Conference $conference
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(EntityManagerInterface $entityManager, Conference $conference)
    {
        $entityManager->remove($conference);
        $entityManager->flush();
        $this->addFlash('notice', 'Item delete !');
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
        return $this->render('conference/withoutvote.html.twig',[
            'vote' => $uservote
        ]);
    }
}