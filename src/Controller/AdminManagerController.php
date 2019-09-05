<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;


class AdminManagerController extends AbstractController
{

   /**
    * @Route("/usersManager", name="usersManager")
    */
   public function getAllUsers()
   {
      $userRepository = $this->getDoctrine()->getManager()->getRepository(User::class);

      $allUsers = $userRepository->findBy(array('roles' => array('ROLE_USER')));
       
      return $this->render('usersManager/index.html.twig', [
          'allUsers' => $allUsers
      ]);
   }
}
?>