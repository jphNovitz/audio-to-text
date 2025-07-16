# Audio-To-Text

## 🇫🇷 Présentation

J’ai vu ma compagne passer des heures à écouter des audios pour les transcrire en texte. La programmation et l’IA doivent servir à ça : faciliter le travail des gens.

Audio-To-Text est une application développée avec **Symfony 7** qui utilise l’API **Whisper** de OpenAI pour automatiser la transcription de fichiers audio.

### Principe de fonctionnement

1. **Téléchargement**  
   L’utilisateur télécharge un fichier audio via l’interface web.
2. **Conversion**  
   Le fichier est converti en MP3 (si besoin) pour garantir la compatibilité.
3. **Découpage**  
   L’audio est découpé en segments de durée configurable.
4. **Transcription asynchrone**  
   Chaque segment est envoyé en tâche asynchrone (Messenger) à l’API Whisper de OpenAI.
5. **Notification en temps réel**  
   L’avancement du process et le texte transcrit sont poussés à l’utilisateur via un hub Mercure.

---

## 🚀 Installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/votre-org/Audio-To-Text.git
   cd Audio-To-Text

    Installer les dépendances

composer install

Configurer l’environnement
Copier le fichier d’exemple et ajuster les variables :

cp .env .env.local

Modifier dans .env.local :

APP_ENV=prod
APP_SECRET=...

# OpenAI
OPENAI_API_KEY=sk-...

# Mercure
MERCURE_PUBLISH_URL=https://mercure.example.com/.well-known/mercure
MERCURE_JWT_TOKEN=...

# Messenger (Redis/RabbitMQ…)
MESSENGER_TRANSPORT_DSN=doctrine://default

Préparer la base de données

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

Lancer le hub Mercure (si en local)

docker run -p 3000:80 -e JWT_KEY='!ChangeMe!' -e ALLOW_ANONYMOUS=1 dunglas/mercure

Démarrer le worker Messenger

php bin/console messenger:consume async -vv

Servir l’application

    symfony server:start
    # ou
    php -S localhost:8000 -t public

🛠️ Configuration

    Segment duration (config/packages/Audio-To-Text.yaml)

    Audio-To-Text:
      segment_duration_seconds: 60

    Formats audio supportés
    MP3, WAV, OGG

    Processus de découpage
    Basé sur FFmpeg (automatiquement utilisé en CLI, veillez à l’avoir installé).

📦 Architecture

[ Frontend Web ] → [ Controller Upload ] → [ Service AudioConverter (FFmpeg) ]
↓
[ Messenger / async ] → [ WhisperApiService → OpenAI Whisper ]
↓
[ Hub Mercure ] → [ Frontend Web (EventSource) ]

🖥️ Utilisation

    Dans l’interface web, choisissez un fichier audio et cliquez sur Transcrire.

    Suivez l’avancement en temps réel via la fenêtre de notification.

    Récupérez le texte complet une fois le traitement terminé.


MIT — voir le fichier LICENSE.