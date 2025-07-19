<?php

namespace App\Contract;

interface MessagePublisherInterface
{
    /**
     * Publie un message de progression via Mercure
     *
     * @param bool $progression Indique si c'est un message de progression
     * @param string $message Le message à publier
     * @param string $status Le statut du processus
     */
    public function publishProgress(bool $progression = false, string $message = '', string $status = ''): void;
} 