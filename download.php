<?php
$type = $_POST['type'];
$format = $_POST['format'];
$title = $_POST['title'];
$posTimer = $_POST['posTimer']; //récupère les sections debut et fin
$timerSelectElements = $_POST['timerSelectElements']; //récupère les liens concernés
$rename = $_POST['rename'];
$ordre = $_POST['ordre'];

$whatTimer  = 0; //tracker pour les timers

if(sizeof($title)>1)
{
    if($rename)
    {
        mkdir( $rename );
        $renamedir = $rename;
    }
    else
    {
        mkdir( 'contenu' );
        $renamedir = 'contenu';
    }
}
else
{
    if($rename)
    {
        ?><a href="url.php"> retour </a><br><?php
        exit('impossible d\'avoir un renommage du dossier si un seul lien');
    }
}

switch ($type) {
    case 'mptrois':
        for ($i=0; $i < sizeof($title); $i++) 
        {
            if(sizeof($title)>1)
            {
                if ($format == 'best') 
                {
                    //exec('cd ' . $renamedir . '&& yt-dlp -x ' . $title[$i], $output, $retval); //telecharge uniquement l'audio 

                    //si i = timerSelectElements[whattimer/2]
                        //alors on telecharge la video avec debut posTimer[whattimer] et fin posTimer[whattimer + 1] 
                        //whattimer = whattimer+2
                }
                else
                {
                    exec('cd ' . $renamedir . '&& yt-dlp -x --audio-format ' . $format . ' ' . $title[$i], $output, $retval); //telecharge uniquement l'audio selon le format désiré
                }
            }
            else
            {
                if ($format == 'best') 
                {
                    //yt-dlp https://www.youtube.com/watch?v=7guNNC2QEKo --download-sections *0:15-0:42
                    //exec('yt-dlp -x ' . $title[$i] . ' --download-sections *0:01-0:41', $output, $retval); //telecharge uniquement l'audio
                    //exec('yt-dlp -x ' . $title[$i], $output, $retval); //telecharge uniquement l'audio
                    /*echo 'yt-dlp -x ' . $title[$i] . ' --download-sections *0:01-0:41';
                    echo '<br>';
                    print_r($output);
                    echo '<br>';
                    echo $retval;*/
                }
                else
                {
                    exec('yt-dlp -x --audio-format ' . $format . ' ' . $title[$i], $output, $retval); //telecharge uniquement l'audio selon le format désiré
                }
            }
            if($i == 0)
            {
                $slice = 5;
            }
            else
            {
                $slice = $slice + 7;
            }

            $input = array_slice($output, $slice, 1);  //récupère la partie de la réponse à la commande où se trouve le nom du fichier
            $rest = implode("','",$input); //la convertit en une chaîne
            $restarr[$i] = substr($rest, 28); //récupère uniquement le nom du fichier
            $restarrcp = $restarr[$i]; //on garde le nom original (nom du fichier)
            $find = 0; //0 = 3 caractères pour l'extension, 1=4, 2=6
            $pos = strpos($restarr[$i], 'flac');
            if ($pos === false) 
            {
                $pos = strpos($restarr[$i], 'opus');
                if ($pos === false) 
                {
                    $pos = strpos($restarr[$i], 'alac');
                    if ($pos === false) 
                    {
                        $pos = strpos($restarr[$i], 'vorbis');
                        if ($pos === true) 
                        {
                            $find = 2;
                        }
                    }
                    else
                    {
                        $find = 1;
                    }
                }
                else
                {
                    $find = 1;
                }
            }
            else
            {
                $find = 1;
            }
            switch ($find) 
            {
                case 0:
                    $extension = substr($restarr[$i], -4); //récupère l'extension du fichier
                    $restarr[$i] = substr($restarr[$i], 0, -18); //supprime les chaînes de caractère générées automatiquement par le logiciel
                    break; 
                case 1:
                    $extension = substr($restarr[$i], -5); //récupère l'extension du fichier
                    $restarr[$i] = substr($restarr[$i], 0, -19); //supprime les chaînes de caractère générées automatiquement par le logiciel
                    break; 
                case 2:
                    $extension = substr($restarr[$i], -7); //récupère l'extension du fichier
                    $restarr[$i] = substr($restarr[$i], 0, -21); //supprime les chaînes de caractère générées automatiquement par le logiciel
                    break; 
                default:
                    echo "find inconnu"; //erreur
                break;
            }
            
            $restarr[$i] = $restarr[$i] . $extension; //rajoute l'extension
            
            if(sizeof($title)>1 && isset($ordre)) //si plus d'un fichier et qu'on veut conserver l'ordre
            {
                if($i < 10)
                {
                    $j = '0' . strval($i); //pour les fichier 1 à 9 on met 00,01,02...
                }
                else
                {
                    $j = strval($i); //sinon juste 10,11,12...
                }
                $restarr[$i] = $j . ' - ' . $restarr[$i]; //on met au format "numero - nomfichier"

                rename ($renamedir . '/' . $restarrcp, $renamedir . '/' . $restarr[$i]); //on renomme sur le disque
            }
            elseif(sizeof($title)==1)  
            {
                rename ($restarrcp, $restarr[$i]); //on renomme sur le disque
            }
        }
        break;
    case 'mpquatre':
        for ($i=0; $i < sizeof($title); $i++) 
        { 
            $find = 0;
            if(sizeof($title)>1)
            {
                if ($format == 'best') 
                {
                    exec('cd ' . $renamedir . '&& yt-dlp ' . $title[$i], $output, $retval); //telecharge la video (et l'audio)
                }
                else
                {
                    exec('cd ' . $renamedir . '&& yt-dlp -f ' . $format . ' ' . $title[$i], $output, $retval); //telecharge la video (et l'audio)
                }
            }
            else
            {
                if ($format == 'best') 
                {
                    exec('yt-dlp ' . $title[$i], $output, $retval); //telecharge la video (et l'audio)
                }
                else
                {
                    exec('yt-dlp -f ' . $format . ' ' . $title[$i], $output, $retval); //telecharge la video (et l'audio)
                }
            }

            if ($format == 'best')
            {
                if($i == 0)
                {
                    $slice = 7;
                }
                else
                {
                    $slice = $slice + 10;
                }
                $input = array_slice($output, $slice, 1);  //récupère la partie de la réponse à la commande où se trouve le nom du fichier
                $rest = implode("','",$input); //la convertit en une chaîne
                $restarr[$i] = substr($rest, 31, -1); //récupère uniquement le nom du fichier
                $restarrcp = $restarr[$i]; //on garde le nom original (nom du fichier)
                $find = 0; //0 = 3 caractères pour l'extension, 1=4, 2=6*/
            }
            else
            {
                if($i == 0)
                {
                    $slice = 3;
                }
                else
                {
                    $slice = $slice + 5;
                }
                $input = array_slice($output, $slice, 1);  //récupère la partie de la réponse à la commande où se trouve le nom du fichier
                $rest = implode("','",$input); //la convertit en une chaîne
                $restarr[$i] = substr($rest, 24); //récupère uniquement le nom du fichier
                $restarrcp = $restarr[$i]; //on garde le nom original (nom du fichier)
                $find = 0; //0 = 3 caractères pour l'extension, 1=4 */
            }

            $pos = strpos($restarr[$i], 'webm');
            if ($pos === false) {
                $find = 0;
            } else {
                $find = 1;
            }
            
            switch ($find) 
            {
                case 0:
                    $extension = substr($restarr[$i], -4); //récupère l'extension du fichier
                    $restarr[$i] = substr($restarr[$i], 0, -18); //supprime les chaînes de caractère générées automatiquement par le logiciel
                    break; 
                case 1:
                    $extension = substr($restarr[$i], -5); //récupère l'extension du fichier
                    $restarr[$i] = substr($restarr[$i], 0, -19); //supprime les chaînes de caractère générées automatiquement par le logiciel
                    break;
                default:
                    echo "find inconnu"; //erreur
                break;
            }

            $restarr[$i] = $restarr[$i] . $extension; //rajoute l'extension
            
            if(sizeof($title)>1 && isset($ordre)) //si plus d'un fichier et qu'on veut conserver l'ordre
            {
                if($i < 10)
                {
                    $j = '0' . strval($i); //pour les fichier 1 à 9 on met 00,01,02...
                }
                else
                {
                    $j = strval($i); //sinon juste 10,11,12...
                }
                $restarr[$i] = $j . ' - ' . $restarr[$i]; //on met au format "numero - nomfichier"

                rename ($renamedir . '/' . $restarrcp, $renamedir . '/' . $restarr[$i]); //on renomme
            }
            elseif(sizeof($title)==1)  
            {
                rename ($restarrcp, $restarr[$i]); //on renomme
            }
        }
        break;
    default:
        echo "erreur : aucun bouton n'a été coché"; //erreur
        break;
}

if(sizeof($title)>1) //crée l'archive où seront stockés les fichiers téléchargés
{
    if($rename)
    {
        exec('tar -cvf ' . $rename . '.tar ' . $rename); //crée l'archive renommée par l'utilisateur
        $rest = $rename . '.tar';
    }
    else
    {
        exec('tar -cvf content.tar contenu'); //crée l'archive par défaut
        $rest = 'content.tar';
    }
}
else
{
    $rest = $restarr[0];
}


/*if (file_exists($rest)) { //télécharge le fichier
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($rest).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($rest));
    readfile($rest);
}
else //erreur
{
    echo "erreur, un bug est apparu, pas de chance :/";
}*/

$rest = str_replace(" ", "\ ", $rest); //supprime les espaces car ça fait buguer la suppression

exec('yes | rm ' . $rest); //supprime le fichier

if(sizeof($title)>1) //supprime l'archive si plusieurs fichiers
{
    if($rename)
    {
        exec('yes | rm -r ' . $rename); //supprime les fichiers
    }
    else
    {
        exec('yes | rm -r contenu'); //supprime les fichiers
    }
}
?> 
<a href="url.php"> retour </a>