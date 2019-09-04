<?php
  namespace App\Controller;

  use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Route("/adminManager", name="adminManager")
 */
class AdminManagerController
{
   public function getAllUsers(UserRepository $userRepository)
   {
      $userRepository = $this->getDoctrine()->getManager()->getRepository(User::class);

      $allUsers = $userRepository->findAll();
       
      return $this->render('adminManager/adminManager.html.twig', [
          'allUsers' => $allUsers
      ]);
   }
}
?>