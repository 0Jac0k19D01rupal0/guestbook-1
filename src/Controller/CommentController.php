<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Message;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/create/{message}", name="comment_create_form")
     */
    public function create(Request $request, Message $message)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_create_form', [
                'message' => $message->getId()
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setUsername('Oscarr');
            $comment->setCreatedAt(new \DateTime('now'));
            $comment->setMessage($message);

            $em = $this->getDoctrine()->getManager();

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('message', ['message' => $message->getId()]);
        }

        return $this->render('comment/form.html.twig', [
            'comment_form' => $form->createView(),
            'message' => $message
        ]);
    }
}
