<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnseignantCredentialsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;
    public $backoff = 60;

    public function __construct(
        public string $prenom,
        public string $email,
        public string $motDePasse,
        public string $annexeNom,
    ) {}

    public function build()
    {
        return $this->subject('Vos identifiants de connexion — ' . $this->annexeNom)
            ->view('emails.enseignant-credentials');
    }

    /**
     * Appelé automatiquement si le job échoue définitivement après ses tentatives.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error("Échec définitif de l'envoi des identifiants à {$this->email} : " . $exception->getMessage());
    }
}
