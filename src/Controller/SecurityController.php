<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\SortType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="user_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // 1) постройте форму
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        // 2) обработайте отправку (произойдёт только в POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Зашифруйте пароль (вы также можете сделать это через слушатель Doctrine)
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $user = $form->getData();
            $user->setCreatedAt(new \DateTime('now'));

            // 4) сохраните Пользователя!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... сделайте любую другую работу - вроде отправки письма и др
            // может, установите "флеш" сообщение об успешном выполнении для пользователя

            return $this->redirectToRoute('user_login');
        }

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/logout", name="user_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('message');
    }

    /**
     * @Route("/profile", name="user_profile")
     * @IsGranted("ROLE_USER")
     */
    public function profile(Request $request, UserInterface $user)
    {

        /* SORT FORM */
        $sort_form = $this->createForm(SortType::class);
        $sort_form->handleRequest($request);
        if ($sort_form->isSubmitted() && $sort_form->isValid()) {

            $sort = $sort_form->getData()['sort'];
            $sort = explode('.', $sort, 2);

            $messages = $this->getDoctrine()
                ->getRepository(Message::class)
                ->findAllUserMessages($sort[0], $sort[1], $user->getUsername());
        }
        else {
            $em = $this->getDoctrine()->getManager();
            $messages = $em->getRepository(Message::class)->findBy(array('username' => $user->getUsername()), array('id' => 'DESC'));
        }

        return $this->render('security/profile.html.twig', [
            'sort_form' => $sort_form->createView(),
            'messages' => $messages,
            'picture_dir' => $this->getParameter('picture_path'),
            'img_support' => true,
        ]);
    }
}
