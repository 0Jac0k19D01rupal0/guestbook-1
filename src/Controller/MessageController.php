<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Form\SortType;
use App\Helper\Captcha;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageController extends AbstractController
{
    /**
     * @Route("/", name="message")
     */
    public function index(Request $request, Captcha $captcha, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $messages_query = $em->getRepository(Message::class)->findBy(array('is_enabled' => true), array('id' => 'DESC'));

        /* PAGINATION */

        $messages = $paginator->paginate(
            $messages_query,
            $request->query->getInt('page', 1),
            5
        );

        /* MESSAGE FORM */
        $message_entity = new Message();
        $form = $this->createForm(MessageType::class, $message_entity, [
            'userdata' => $this->getUser(),
            'picture' => null
        ]);
        $form->handleRequest($request);

        $recaptcha = $request->get('g-recaptcha-response');
        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($recaptcha) && $captcha->request($this->getParameter('captcha_secret_key'), $recaptcha)->success == true) {
                $message_entity = $form->getData();
                $message_entity->setCreatedAt(new \DateTime('now'));
                $message_entity->setUserIp($request->getClientIp());

                $homepage = $form->get('homepage')->getData();
                if (strripos($homepage, 'http') !== false) {
                    $hp = str_replace("http://", "", $homepage);
                    $hp = str_replace("https://", "", $hp);
                    $message_entity->setHomepage($hp);
                }

                $file = $form->get('picture')->getData();
                if (isset($file) || !is_null($file)) {
                    $filename = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move(
                        $this->getParameter('picture_dir'), $filename
                    );

                    $message_entity->setPicture($filename);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($message_entity);
                $em->flush();

                return $this->redirectToRoute('message', ['send_message' => true]);
            }
            else {
                $form->get('text')->addError(new FormError('Are you exactly a human? Please, confirm captcha!'));
            }
        }


        return $this->render('message/index.html.twig', [
            'form' => $form->createView(),
            'messages' => $messages,
            'picture_dir' => $this->getParameter('picture_path'),
            'img_support' => true,
            'captcha_key' => $this->getParameter('captcha_public_key'),
            'send_message' => $request->get('send_message') ? true : false
        ]);
    }

    /**
     * @Route("/message/create", name="create_message")
     */
    public function create(Request $request, Captcha $captcha)
    {
        /* FORM */
        $message_entity = new Message();
        $form = $this->createForm(MessageType::class, $message_entity, [
            'userdata' => $this->getUser(),
            'picture' => null
        ]);
        $form->handleRequest($request);

        $recaptcha = $request->get('g-recaptcha-response');
        if ($form->isSubmitted() && $form->isValid() && !empty($recaptcha)) {
            if ($captcha->request($this->getParameter('captcha_secret_key') ,$recaptcha)->success == true) {
                $message_entity = $form->getData();
                $message_entity->setCreatedAt(new \DateTime('now'));
                $message_entity->setUserIp($request->getClientIp());

                $file = $form->get('picture')->getData();
                if (isset($file) || !is_null($file)) {
                    $filename = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move(
                        $this->getParameter('picture_dir'), $filename
                    );

                    $message_entity->setPicture($filename);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($message_entity);
                $em->flush();

                return $this->redirectToRoute('message', ['send_message' => true]);
            }
        }
        return $this->render('message/create.html.twig', [
            'form' => $form->createView(),
            'picture_dir' => $this->getParameter('picture_path'),
            'captcha_key' => $this->getParameter('captcha_public_key')
        ]);
    }

    /**
     * @Route("/message/update/{message}", name="update_message")
     * IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function update(Request $request, Message $message, Captcha $captcha)
    {
        $message = $this->getDoctrine()
            ->getRepository(Message::class)
            ->findOneBy(array('id' => $message));

        if ($message->getUsername() == $this->getUser()->getUsername()) {

            $picture = $this->getParameter('picture_dir').'/'.$message->getPicture();
            if (is_file($picture)) {
                $picture = new File($picture);
            }
            else {
                $picture = null;
            }

            $form = $this->createForm(MessageType::class, $message, [
                'action' => $this->generateUrl('update_message', [
                    'message' => $message->getId()
                ]),
                'userdata' => $this->getUser(),
                'picture' => $picture
            ]);
            $form->handleRequest($request);

            $recaptcha = $request->get('g-recaptcha-response');
            if ($form->isSubmitted() && $form->isValid() && !empty($recaptcha)) {
                if ($captcha->request($this->getParameter('captcha_secret_key') ,$recaptcha)->success == true) {
                    $message = $form->getData();
                    $message->setUpdatedAt(new \DateTime('now'));

                    $file = $form->get('picture')->getData();
                    if (isset($file) || !is_null($file)) {
                        $filename = md5(uniqid()) . '.' . $file->guessExtension();
                        $file->move(
                            $this->getParameter('picture_dir'), $filename
                        );

                        $message->setPicture($filename);
                    }

                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    return $this->redirectToRoute('message', ['send_message' => true]);
                }
            }
        }
        else {
            return $this->redirectToRoute('message');
        }

        return $this->render('message/create.html.twig', [
            'form' => $form->createView(),
            'form_title' => 'pages.update_message',
            'captcha_key' => $this->getParameter('captcha_public_key')
        ]);
    }

    /**
     * @Route("/message/{message}", name="single_message")
     */
    public function message(Message $message)
    {
        return $this->render('message/single.html.twig', [
            'message' => $message,
            'picture_dir' => $this->getParameter('picture_path'),
            'img_support' => true,
        ]);
    }

    /**
     * @Route("/message/delete/{message}", name="delete_message")
     * IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function delete(Message $message)
    {
        $message = $this->getDoctrine()
            ->getRepository(Message::class)
            ->findOneBy(array('id' => $message));

        if ($message->getUsername() == $this->getUser()->getUsername()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();
        }
        return $this->redirectToRoute('user_profile');
    }
}
