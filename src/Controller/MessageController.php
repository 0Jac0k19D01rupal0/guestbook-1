<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/", name="message")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository(Message::class)->findBy(array(), array('id' => 'DESC'));

        /* FORM */

        $message_entity = new Message();
        $form = $this->createForm(MessageType::class, $message_entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message_entity = $form->getData();
            $message_entity->setCreatedAt(new \DateTime('now'));
            $message_entity->setUserIp($request->getClientIp());
            $em = $this->getDoctrine()->getManager();
            $em->persist($message_entity);
            $em->flush();

            return $this->redirectToRoute('message');
        }

        return $this->render('message/index.html.twig', [
            'form' => $form->createView(),
            'messages' => $messages
        ]);
    }

    /**
     * @Route("/message/{message}", name="single_message")
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
            'message' => $message
        ]);
    }
//    /**
//     * @Route("/message/create/", name="create_message")
//     */
//    public function create(Request $request)
//    {
//        $message_entity = new Message();
//        $form = $this->createForm(MessageType::class, $message_entity);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $message_entity = $form->getData();
//            $message_entity->setCreatedAt(new \DateTime('now'));
//            $message_entity->setUserIp($request->getClientIp());
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($message_entity);
//            $em->flush();
//
//            return $this->redirectToRoute('message');
//        }
//
//        return $this->render('message/create.html.twig', [
//            'form' => $form->createView()
//        ]);
//    }
}
