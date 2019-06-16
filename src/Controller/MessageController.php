<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Component\HttpFoundation\Request;
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
        $messages = $em->getRepository(Message::class)->findAll();

        /* FORM */

        $message_entity = new Message();
        $form = $this->createForm(MessageType::class, $message_entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message_entity = $form->getData();
            $message_entity->setCreatedAt(new \DateTime('now'));

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

//    /**
//     * @Route("/message/{message}", name="single_message")
//     */
//    public function message(Message $message)
//    {
//        return $this->render('message/single.html.twig', [
//            'controller_name' => 'MessageController',
//            'message' => $message
//        ]);
//    }
//    /**
//     * @Route("/message/create/", name="create_message")
//     */
//    public function create(Request $request)
//    {
//        $message = new Message();
//
//        $form = $this->createForm(MessageType::class, $message);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $message = $form->getData();
//            $message->setCreatedAt(new \DateTime('now'));
//
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($message);
//            $em->flush();
//
//            return $this->redirectToRoute('message');
//        }
//
//        return $this->render('message/create.html.twig', [
//            'form' => $form->createView()
//        ]);
//    }
//
//    /**
//     * @Route("/test", name="test")
//     */
//    public function message()
//    {
//        return $this->render('basepage.html.twig', [
//            'controller_name' => 'MessageController',
//        ]);
//    }

}
