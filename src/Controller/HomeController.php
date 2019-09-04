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
