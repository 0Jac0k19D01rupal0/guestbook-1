<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Form\SortType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/", name="message")
     */
    public function index(Request $request)
    {
        /* SORT FORM */
        $sort_form = $this->createForm(SortType::class);
        $sort_form->handleRequest($request);
        if ($sort_form->isSubmitted() && $sort_form->isValid()) {

            $sort = $sort_form->getData()['sort'];
            $sort = explode('.', $sort, 2);

            $messages = $this->getDoctrine()
                ->getRepository(Message::class)
                ->findAllOrderedByCol($sort[0], $sort[1]);
        }
        else {
            $em = $this->getDoctrine()->getManager();
            $messages = $em->getRepository(Message::class)->findBy(array(), array('id' => 'DESC'));
        }

        /* MESSAGE FORM */
        $message_entity = new Message();
        $form = $this->createForm(MessageType::class, $message_entity, ['userdata'=>$this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                $filename = md5(uniqid()).'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('picture_dir'), $filename
                    );
                } catch (FileException $e) {

                }
                $message_entity->setPicture($filename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($message_entity);
            $em->flush();

            return $this->redirectToRoute('message');
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
    public function create(Request $request)
    {
        /* FORM */
        $message_entity = new Message();
        $form = $this->createForm(MessageType::class, $message_entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message_entity = $form->getData();
            $message_entity->setCreatedAt(new \DateTime('now'));
            $message_entity->setUserIp($request->getClientIp());

            $file = $form->get('picture')->getData();
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('picture_dir'), $filename
                );
            } catch (FileException $e) {

            }
            $message_entity->setPicture($filename);

            $em = $this->getDoctrine()->getManager();
            $em->persist($message_entity);
            $em->flush();

            return $this->redirectToRoute('message');
        }

        return $this->render('message/create.html.twig', [
            'form' => $form->createView(),
            'picture_dir' => $this->getParameter('picture_path'),
        ]);
    }

    /**
     * @Route("/message/{message}", name="single_message")
     * @IsGranted("ROLE_USER")
     */
    public function message(Message $message)
    {
        if (strlen($message->getHomepage()) > 35) {
            $linktitle = substr($message->getHomepage(), 0, 35);
            $linktitle = $linktitle.'...';
        }
        else {
            $linktitle = $message->getHomepage();
        }
        $title = substr($message->getText(), 0, 20);
        $title = '★'.$message->getUsername().'★ '.$title;

        return $this->render('message/single.html.twig', [
            'title' => $title,
            'link_title' => $linktitle,
            'message' => $message,
            'picture_dir' => $this->getParameter('picture_path'),
            'img_support' => true,
        ]);
    }
}
