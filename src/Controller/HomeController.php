<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Models\UserDtoModel;
use App\Form\Models\UsersDtoModel;
use App\Form\UsersFormType;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('home/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/users/action", name="homeForm")
     * @IsGranted("ROLE_USER")
     */
    public function usersBlock(LoggerInterface $logger, Request $request, UserRepository $userRepository)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        foreach ($users as $user)
        {
            if ($request->request->get("user".$user->getId()) != null) {
                switch ($request->request->get("action")) {
                    case "block":
                        $userRepository->blockUser($user);
                        break;
                    case "unblock":
                        $userRepository->unblockUser($user);
                        break;
                    case "remove":
                        $userRepository->removeUser($user);
                        break;
                }
            }


        }

        return $this->redirectToRoute("home");
    }
}
