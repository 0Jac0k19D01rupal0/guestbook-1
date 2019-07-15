<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Form\SortType;
use App\Helper\Captcha;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    public function index(Request $request, Captcha $captcha)
    {
        /* SORT FORM */
        $sort_form = $this->createForm(SortType::class);
        $sort_form->handleRequest($request);
        if ($sort_form->isSubmitted() && $sort_form->isValid()) {

            $sort = $sort_form->getData()['sort'];
            $sort = explode('.', $sort, 2);

            $messages = $this->getDoctrine()
                ->getRepository(Message::class)
                ->findAllOrderedByCol($sort[0], $sort[1], 25);
        }
        else {
            $em = $this->getDoctrine()->getManager();
            $messages = $em->getRepository(Message::class)->findBy(array(), array('id' => 'DESC'));
        }

        /* MESSAGE FORM */
        $message_entity = new Message();
        $form = $this->createForm(MessageType::class, $message_entity, [
            'userdata' => $this->getUser(),
            'picture' => null
        ]);
        $form->handleRequest($request);

        $recaptcha = $request->get('g-recaptcha-response');
        if ($form->isSubmitted() && $form->isValid() && !empty($recaptcha)) {
            if ($captcha->request($recaptcha)->success == true) {
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

                return $this->redirectToRoute('message');
            }
        }


        return $this->render('message/index.html.twig', [
            'sort_form' => $sort_form->createView(),
            'form' => $form->createView(),
            'messages' => $messages,
            'picture_dir' => $this->getParameter('picture_path'),
            'img_support' => true,
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
            if ($captcha->request($recaptcha)->success == true) {
                $message_entity = $form->getData();
                $message_entity->setCreatedAt(new \DateTime('now'));
                $message_entity->setUserIp($request->getClientIp());

                $file = $form->get('picture')->getData();
                $filename = md5(uniqid()).'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('picture_dir'), $filename
                );

                $message_entity->setPicture($filename);

                $em = $this->getDoctrine()->getManager();
                $em->persist($message_entity);
                $em->flush();

                return $this->redirectToRoute('message');
            }
        }
        return $this->render('message/create.html.twig', [
            'form' => $form->createView(),
            'picture_dir' => $this->getParameter('picture_path'),
        ]);
    }

    /**
     * @Route("/message/update/{message}", name="update_message")
     * IsGranted("ROLE_USER")
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
                if ($captcha->request($recaptcha)->success == true) {
                    $message = $form->getData();
                    $message->setUpdatedAt(new \DateTime('now'));

                    $file = $form->get('picture')->getData();
                    $filename = md5(uniqid()).'.'.$file->guessExtension();
                    $file->move(
                        $this->getParameter('picture_dir'), $filename
                    );

                    $message->setPicture($filename);

                    $em = $this->getDoctrine()->getManager();
                    $em->flush();

                    return $this->redirectToRoute('user_profile');
                }
            }
        }
        else {
            return $this->redirectToRoute('message');
        }

        return $this->render('message/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/message/{message}", name="single_message")
     */
    public function message(Message $message)
    {
        $title = substr($message->getText(), 0, 20);
        $title = '★'.$message->getUsername().'★ '.$title;

        return $this->render('message/single.html.twig', [
            'title' => $title,
            'message' => $message,
            'picture_dir' => $this->getParameter('picture_path'),
            'img_support' => true,
        ]);
    }

    /**
     * @Route("/message/delete/{message}", name="delete_message")
     * IsGranted("ROLE_USER")
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
