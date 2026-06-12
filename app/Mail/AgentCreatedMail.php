<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AgentCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public  $user;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Vos identifiants de connexion - ISI Scolarité')
            ->html("
                <h2>Bienvenue dans l'equipe du groupe ISI</h2>
                <p>Votre compte a été créé par l'administrateur.</p>
                <p>Voici vos identifiants pour vous connecter à votre espace (<strong>".ucfirst($this->user->role)." </strong>) :</p>
                <ul>
                    <li><strong>Identifiant (Email) :</strong>". $this->user->email. "</li>
                    <li><strong>Mot de passe temporaire :</strong>". $this->password. "</li>
                </ul>
                <p>Par sécurité, nous vous recommandons de modifier ce mot de passe dès votre première connexion.</p>
            ");
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Agent Created Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
