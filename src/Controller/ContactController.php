<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $emailUserAddress = $request->request->get('email'); // ⚡ string uniquement
            $message = $request->request->get('message');

            // 👉 tu peux tester avec un dd ici pour debug
            // dd($request->request->all());

            // ✅ Email de confirmation pour l’utilisateur
            $emailToUser = (new Email())
                ->from('terence.mayombo@gmail.com')   // doit être ton vrai Gmail
                ->to($emailUserAddress)               // adresse entrée dans le formulaire
                ->subject('Confirmation de contact - Club Molkky')
                ->html("
                    <p>Bonjour {$prenom} {$nom},</p>
                    <p>Merci pour votre message :</p>
                    <blockquote>{$message}</blockquote>
                    <p>Nous vous répondrons rapidement.</p>
                    <p>Cordialement,<br>L'équipe du Club Molkky</p>
                ");
            $mailer->send($emailToUser);

            // ✅ Notification à l’admin
            $emailToAdmin = (new Email())
                ->from('terence.mayombo@gmail.com')   // toujours ton Gmail
                ->to('terence.mayombo@gmail.com')     // ton adresse admin
                ->subject('Nouveau message de contact')
                ->html("
                    <p><strong>Nom :</strong> {$nom}</p>
                    <p><strong>Prénom :</strong> {$prenom}</p>
                    <p><strong>Email :</strong> {$emailUserAddress}</p>
                    <p><strong>Message :</strong></p>
                    <blockquote>{$message}</blockquote>
                ");
            $mailer->send($emailToAdmin);

            $this->addFlash('success', 'Votre message a bien été envoyé !');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('main/contact.html.twig');
    }
}