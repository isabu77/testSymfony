<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MailService
{
    protected $session;
    protected $mailer;

    /**
     * 
     */
    public function __construct(SessionInterface $session, \Swift_Mailer $mailer)
    {
        $this->session = $session;
        $this->mailer = $mailer;
    }
    /**
     * 
     */
    public function send($objet, $template)
    {
        $message = (new \Swift_Message($objet))
            ->setTo('isabu77@gmail.com')
            ->setFrom('montluconaformac2019@gmail.com')
            ->setBody(
                $template,
                'text/html'
            )
            /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'emails/confirm.txt.twig',
                array('name' => $name)
            ),
            'text/plain'
        )
        */;

        return $this->mailer->send($message);
    }

}
