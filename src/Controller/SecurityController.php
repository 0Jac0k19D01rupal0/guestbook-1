<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Entity\Verification;
use App\Form\PwdRecoveryEmailType;
use App\Form\PwdRecoveryNewPasswordType;
use App\Form\RegistrationType;
use App\Form\SortType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
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
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder,  \Swift_Mailer $mailer)
    {

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $result = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $user = $form->getData();
            $user->setCreatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $verification = new Verification();
            $verification->setUser($user);
            $verification->setEmail($user->getEmail());
            $verification->setToken(md5(microtime()));
            $verification->setType('confirm_email');
            $verification->setStatus(false);
            $verification->setCreatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($verification);
            $em->flush();

            /* SEND MAIL */
            $link = $_SERVER['SERVER_NAME'].'/confirm_email/?token='.$verification->getToken();

            $message = (new \Swift_Message('[GuestBook] Confirm Email'))
                ->setFrom('myktm-dev@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('email/confirm_email.html.twig', [
                        'name' => $user->getUsername(),
                        'link' => $link,
                        'domain' => $_SERVER['SERVER_NAME']
                    ]),
                    'text/html'
                )
            ;
            $mailer->send($message);

            $result = 'pages.register_success';
        }

        return $this->render(
            'security/register.html.twig', [
                'form' => $form->createView(),
                'result' => $result
        ]);
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
     * @IsGranted("IS_AUTHENTICATED_FULLY")
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

    /**
     * @Route("/confirm_email", name="confirm_email")
     */
    public function confirm(Request $request)
    {
        $verification = $this->getDoctrine()
            ->getRepository(Verification::class)
            ->findOneByToken($request->query->get('token'));

        if (is_null($verification) || $verification->getType() != 'confirm_email') {
            return $this->redirectToRoute('message');
        }
        if ($verification->getStatus() == false) {
            $verification->getUser()->setRoles(['ROLE_USER']);
            $verification->setStatus(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($verification);
            $em->flush();

            $result = 'pages.confirm_email_success';

            $token = new UsernamePasswordToken($verification->getUser(), null, 'main', $verification->getUser()->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
        }
        else {
            $result = 'pages.confirm_email_error';
        }

        return $this->render('security/confirm_email.html.twig', [
            'result' => $result
        ]);
    }

    /**
     * @Route("/recovery", name="user_pwd_recovery")
     */
    public function recovery(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(PwdRecoveryEmailType::class);
        $form->handleRequest($request);
        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneUserByEmail($form->getData()['email'])
            ;

            if (!is_null($user)) {
                $verification = new Verification();
                $verification->setUser($user);
                $verification->setEmail($user->getEmail());
                $verification->setToken(md5(microtime()));
                $verification->setType('pwd_recovery');
                $verification->setStatus(false);
                $verification->setCreatedAt(new \DateTime('now'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($verification);
                $em->flush();

                /* SEND MAIL */
                $link = $_SERVER['SERVER_NAME'].'/new_password/?token='.$verification->getToken();

                $message = (new \Swift_Message('[GuestBook] Recovery Password'))
                    ->setFrom('myktm-dev@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView('email/pwd_recovery.html.twig', [
                            'name' => $user->getUsername(),
                            'link' => $link,
                            'domain' => $_SERVER['SERVER_NAME']
                        ]),
                        'text/html'
                    )
                ;
                $mailer->send($message);

                $result = 'pages.recovery_success';
            }
            else {
                $result = 'pages.recovery_error';
            }

        }

        return $this->render('security/pwd_recovery.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }

    /**
     * @Route("/new_password", name="user_new_pwd")
     */
    public function new_password(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $verification = $this->getDoctrine()
            ->getRepository(Verification::class)
            ->findOneByToken($request->query->get('token'));

        if (is_null($verification) || $verification->getType() != 'pwd_recovery') {
            return $this->redirectToRoute('message');
        }

        $form = $this->createForm(PwdRecoveryNewPasswordType::class);
        $form->handleRequest($request);
        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {
            if ($verification->getStatus() == false) {
                $password = $passwordEncoder->encodePassword($verification->getUser(), $form->getData()['plainPassword']);
                $verification->getUser()->setpassword($password);
                $verification->setStatus(true);

                $em = $this->getDoctrine()->getManager();
                $em->persist($verification);
                $em->flush();

                $result = 'pages.new_password_success';
            }
            else {
                $result = 'pages.new_password_error';
            }
        }

        return $this->render('security/new_password.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }
}
