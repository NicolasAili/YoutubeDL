# YoutubeDL

Obligatoire pour que l'outil fonctionne :

- Apache2
- PHP7 ou supérieur
- yt-dlp ----> https://github.com/yt-dlp/yt-dlp#installation
- ffmpeg et ffprobe (généralement déjà installés par défaut)

Placez le dossier YoutubeDL dans le répertoire racine de votre serveur apache (par exemple /var/www/html/ pour ubuntu et quelques autres) en exécutant la commande : git clone https://github.com/NicolasAili/YoutubeDL 

Vous pouvez ensuite vous connecter à l'outil depuis n'importe où sur votre réseau local en tapant dans votre barre d'URL : <ip_adresse_où_l'outil_est_installé>/youtubedl/url.php ou http://localhost/youtubedl/url.php si vous vous connectez directement depuis la machine sur laquelle l'outil est installé.

Vous pouvez également accéder à l'outil à distance depuis l'extérieur de votre réseau local si apache est configuré pour que cela soit possible.

IMPORTANT : Il faut donner les droits adéquats à votre dossier où est installé l'outil, sinon ça ne fonctionnera pas. Pour cela, placez-vous dans le dossier parent à "YoutubeDL" puis exécutez : chmod -R 755 YoutubeDL/

Vous pouvez m'envoyer un mail si besoin contact@arpenid.com
___________________________________________________________________________________________________________________________________________________

Mandatory for the tool to work :

- Apache2
- PHP7 or above
- yt-dlp ----> https://github.com/yt-dlp/yt-dlp#installation
- ffmpeg and ffprobe (generally already installed)

Put the folder YoutubeDL at the root directory of your apache server (e.g /var/www/html/ for ubuntu and some others) by running the command git clone https://github.com/NicolasAili/YoutubeDL

You can then connect to the tool from anywhere in your local network by typing in your URL bar : <ip_adress_of_the_machine_where_the_tool_is_installed>/youtubedl/url.php or http://localhost/youtubedl/url.php if you connect directly from the machine where the tool is installed.

You can also access the tool remotely from outside your local network if apache is configured so.

IMPORTANT : You need to give rights to the script. In order to do that, go at the parent folder of "YoutubeDL" and run : chmod -R 755 YoutubeDL/

You can send me an email if necessary contact@arpenid.com

Enjoy.
