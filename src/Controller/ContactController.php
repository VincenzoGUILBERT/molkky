<?php
namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['POST', 'GET'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $emailUser = $request->request->get('email');
            $message = $request->request->get('message');

            // ✅ Email de confirmation
            $email = (new Email())
                ->from('no-reply@tonsite.com')   // adresse fictive de ton site
                ->to($emailUser)                // adresse de l’utilisateur
                ->subject('Confirmation de contact - Club Molkky')
                ->html("
                    <p>Bonjour {$prenom} {$nom},</p>
                    <p>Merci pour votre message :</p>
                    <blockquote>{$message}</blockquote>
                    <p>Nous vous répondrons rapidement.</p>
                    <p>Cordialement,<br>L'équipe du Club Rhodanien de Molkky</p>
                ");

            $mailer->send($email);

            // ✅ Notification flash
            $this->addFlash('success', 'Votre message a bien été envoyé. Un email de confirmation vous a été adressé.');


            return $this->redirectToRoute('app_home'); 
        
        }

        return $this->render('main/contact.html.twig');
    }
}
