<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\CreateUserType;
use App\Form\EditUserType;
use App\Form\RegisterUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

   /**
     * @Route("/editedUser/{id}", name="edited_user")
     */
    public function editedVideo(Request $request, $id)
    {
      $user = new User();
      $user = $this->getDoctrine()->getRepository(User::class)->find($id);
      $form = $this->createForm(EditUserType::class, $user);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) 
      { 
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('usersManager');   
      }
      return $this->render('editedUser/editedUser.html.twig', [
        'form' => $form->createView()   
      ]);
    }

    /**
     * @Route("/admin/user/remove/{id}", name="removed_user")
     */
    public function delete(EntityManagerInterface $entityManager, User $id)
    {
        $entityManager->remove($id);
        $entityManager->flush();
        $this->addFlash('notice', 'Utilisateurs a été supprimé !');
        return $this->redirectToRoute('usersManager');
    }

    /**
     * @Route("/createdUser", name="createdUser")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('usersManager');
        }
        return $this->render('createdUser/createdUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
?>