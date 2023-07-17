<?php
$type = $_POST['type'];
$format = $_POST['format'];
$title = $_POST['title'];
$rename = $_POST['rename'];
$ordre = $_POST['ordre'];

$posTimer = $_POST['posTimer']; //récupère les sections debut et fin
$selectedElements = $_POST['selectedElements']; //récupère les liens concernés

$tabelements = [];
$j = 0;


/*Cette partie supprime les liens ayant été mis plusieurs fois*/
$uniqueValues = array();
foreach ($title as $key => $value) {
    // Check if the value has already occurred in the array
    if (in_array($value, $uniqueValues)) {
        // If the value is a duplicate, remove it from the array
        unset($title[$key]);
    } else {
        // If the value is unique, add it to the $uniqueValues array
        $uniqueValues[] = $value;
    }
}
// Re-index the array after removing duplicates
$title = array_values($title);


/*print_r($selectedElements);
echo "<br>";
print_r($posTimer);
echo "<br>";*/

for ($i=0; $i < sizeof($title); $i++) 
{
    if($i == $selectedElements[$j])
    {
        if(empty($posTimer[2*$j]) && empty($posTimer[2*$j+1]))
        {
            unset($selectedElements[$j]);
        }
        else if (empty($posTimer[2*$j])) { //à partir du début de la vidéo
            
            $tabelements[2*$i] = '00:00';
            $tabelements[2*$i+1] = $posTimer[2*$j+1];
        } 
        else if (empty($posTimer[2*$j+1])) { //jusqu'à la fin
            exec('yt-dlp ' . $title[$i] . ' --get-duration', $output);

            $pos = strpos($output[0], ':');

            if ($pos === false) {
                $output[0] = '00:' . $output[0];
            }

            $tabelements[2*$i] = $posTimer[2*$j];
            $tabelements[2*$i+1] = $output[0];
        }
        else
        {
            $tabelements[2*$i] = $posTimer[2*$j];
            $tabelements[2*$i+1] = $posTimer[2*$j+1];
        }
        $j++;
    }
    else
    {
        $tabelements[2*$i] = null;
        $tabelements[2*$i+1] = null;
    }
}

/*print_r($tabelements);
echo "<br>";*/

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
        exit('impossible d\'avoir un renommage si un seul lien');
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
                    if(!empty($tabelements[2*$i]))
                    {
                        /*echo "i inside timer: " . $i;
                        echo "<br>";*/
                        exec('cd ' . $renamedir . ' && yt-dlp -x ' . $title[$i] . ' --download-sections *' . $tabelements[2*$i] . '-' . $tabelements[2*$i+1] . ' --force-keyframes-at-cuts', $output, $retval);
                        /*echo 'cd ' . $renamedir . ' && yt-dlp -x ' . $title[$i] . ' --download-sections *' . $tabelements[2*$i] . '-' . $tabelements[2*$i+1] . ' --force-keyframes-at-cuts';
                        echo "<br>";*/
                    }
                    else
                    {
                        /*echo "i inside no timer: " . $i;
                        echo "<br>";*/
                        exec('cd ' . $renamedir . ' && yt-dlp -x ' . $title[$i], $output, $retval); //telecharge uniquement l'audio 
                        /*echo 'cd ' . $renamedir . ' && yt-dlp -x ' . $title[$i];
                        echo "<br>";
                        echo "<br>";
                        print_r($output);
                        echo "<br>";
                        echo "__________________________________________________________________________";
                        echo "<br>";*/
                    }
                }
                else
                {
                    if(!empty($tabelements[2*$i]))
                    {
                        exec('cd ' . $renamedir . ' && yt-dlp -x --audio-format ' . $format . ' ' . $title[$i] . ' --download-sections *' . $tabelements[2*$i] . '-' . $tabelements[2*$i+1] . ' --force-keyframes-at-cuts', $output, $retval); 
                    }
                    else
                    {
                        exec('cd ' . $renamedir . ' && yt-dlp -x --audio-format ' . $format . ' ' . $title[$i], $output, $retval); //telecharge uniquement l'audio
                        //echo 'cd ' . $renamedir . ' && yt-dlp -x --audio-format ' . $format . ' ' . $title[$i];
                    }
                }
            }
            else
            {
                if ($format == 'best') 
                {
                    if(!empty($tabelements[2*$i]))
                    {
                        exec('yt-dlp -x ' . $title[$i] . ' --download-sections *' . $tabelements[2*$i] . '-' . $tabelements[2*$i+1] . ' --force-keyframes-at-cuts', $output, $retval); 
                    }
                    else
                    {
                        exec('yt-dlp -x ' . $title[$i], $output, $retval); //telecharge uniquement l'audio
                    }
                }
                else
                {
                    if(!empty($tabelements[2*$i]))
                    {
                        exec('yt-dlp -x --audio-format ' . $format . ' ' . $title[$i] . ' --download-sections *' . $tabelements[2*$i] . '-' . $tabelements[2*$i+1] . ' --force-keyframes-at-cuts', $output, $retval); 
                    }
                    else
                    {
                        exec('yt-dlp -x --audio-format ' . $format . ' ' . $title[$i], $output, $retval); //telecharge uniquement l'audio
                    }
                    
                }
            }
            if($i == 0)
            {
                if(!empty($tabelements[2*$i]))
                {
                    if (sizeof($title)==1) {
                        $slice = 7;
                    }
                    else
                    {
                        $slice = 8;
                    }
                }
                else
                {
                   $slice = 8;
                }
            }
            else
            {
                if(!empty($tabelements[2*$i]))
                {
                    $slice = $slice + 10;
                }
                else
                {
                   $slice = $slice + 10;
                }
            }
            $input = array_slice($output, $slice, 1);  //récupère la partie de la réponse à la commande où se trouve le nom du fichier

            $rest = implode("','",$input); //la convertit en une chaîne
            $restarr[$i] = substr($rest, 28); //récupère uniquement le nom du fichier
            /*print_r($output);
            echo '<br>';
            echo "<br>";
            echo "slice : " . $slice;
            echo "<br>";
            echo "rest : " . $rest;
            echo "<br>";
            echo $restarr[$i];
            echo '<br>';
            echo "__________________________________________________________";
            echo '<br>';*/
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
            /*echo "extension : " . $extension;
            echo "<br>";
            echo "apres modifs : " . $restarr[$i];
            echo "<br>";
            echo "unification : " . $restarr[$i];
            echo "<br>";
            echo "_______________________________________";
            echo "<br>";*/
            
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
                /*echo "i : ";
                echo $i;
                echo "<br>";
                echo "j :";
                echo $j;
                echo "<br>";
                echo "restarr i :";
                echo "<br>";
                echo $renamedir . '/' . $restarrcp;
                echo "<br>";
                echo $renamedir . '/' . $restarr[$i];
                echo "<br>";
                echo "------------------------------------------------------------------------------------------------------------------------------------------------";
                echo "<br>";*/
            }
            elseif(sizeof($title)==1)  
            {
                rename ($restarrcp, $restarr[$i]); //on renomme
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
                    if(!empty($tabelements[2*$i]))
                    {
                        exec('cd ' . $renamedir . ' && yt-dlp ' . $title[$i] . ' --download-sections *' . $tabelements[2*$i] . '-' . $tabelements[2*$i+1] . ' --force-keyframes-at-cuts', $output, $retval); 
                    }
                    else
                    {
                        exec('cd ' . $renamedir . ' && yt-dlp ' . $title[$i], $output, $retval); //telecharge la video (et l'audio)
                    }
                }
                else
                {
                    if(!empty($tabelements[2*$i]))
                    {
                        exec('cd ' . $renamedir . ' && yt-dlp -f ' . $format . ' ' . $title[$i] . ' --download-sections *' . $tabelements[2*$i] . '-' . $tabelements[2*$i+1] . ' --force-keyframes-at-cuts', $output, $retval); 
                    }
                    else
                    {
                        exec('cd ' . $renamedir . ' && yt-dlp -f ' . $format . ' ' . $title[$i], $output, $retval); //telecharge la video (et l'audio)
                    }
                }
            }
            else
            {
                if ($format == 'best') 
                {
                    if(!empty($tabelements[2*$i]))
                    {
                        exec('yt-dlp ' . $title[$i] . ' --download-sections *' . $tabelements[2*$i] . '-' . $tabelements[2*$i+1] . ' --force-keyframes-at-cuts', $output, $retval); 
                    }
                    else
                    {
                        exec('yt-dlp ' . $title[$i], $output, $retval); //telecharge la video (et l'audio)
                    }
                }
                else
                {
                    if(!empty($tabelements[2*$i]))
                    {
                        exec('yt-dlp -f ' . $format . ' ' . $title[$i] . ' --download-sections *' . $tabelements[2*$i] . '-' . $tabelements[2*$i+1] . ' --force-keyframes-at-cuts', $output, $retval); 
                    }
                    else
                    {
                        exec('yt-dlp -f ' . $format . ' ' . $title[$i], $output, $retval); //telecharge la video (et l'audio)
                    }
                }
            }

            if ($format == 'best')
            {
                if($i == 0)
                {
                    $slice = 10;
                }
                else
                {
                    $slice = $slice + 13;
                }
                $input = array_slice($output, $slice, 1);  //récupère la partie de la réponse à la commande où se trouve le nom du fichier
                $rest = implode("','",$input); //la convertit en une chaîne
                $restarr[$i] = substr($rest, 31, -1); //récupère uniquement le nom du fichier
                /*print_r($output);
                echo '<br>';
                echo "<br>";
                echo "slice : " . $slice;
                echo "<br>";
                echo "rest : " . $rest;
                echo "<br>";
                echo $restarr[$i];
                echo '<br>';*/
                $restarrcp = $restarr[$i]; //on garde le nom original (nom du fichier)
                $find = 0; //0 = 3 caractères pour l'extension, 1=4, 2=6
                
            }
            else
            {
                if($i == 0)
                {
                    $slice = 6;
                }
                else
                {
                    $slice = $slice + 8;
                }
                $input = array_slice($output, $slice, 1);  //récupère la partie de la réponse à la commande où se trouve le nom du fichier
                $rest = implode("','",$input); //la convertit en une chaîne
                $restarr[$i] = substr($rest, 24); //récupère uniquement le nom du fichier
                /*print_r($output);
                echo '<br>';
                echo "<br>";
                echo "slice : " . $slice;
                echo "<br>";
                echo "rest : " . $rest;
                echo "<br>";
                echo $restarr[$i];
                echo '<br>';*/
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
            /*echo $restarr[$i];
            echo "<br>";
            echo "__________________________________________________________";
            echo '<br>';*/
            
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
            else if(sizeof($title)==1)  
            {
                rename ($restarrcp, $restarr[$i]); //on renomme
            }
        }
        break;
    default:
        echo "erreur : aucun bouton n'a été coché"; //erreur
        break;
}

if(sizeof($title)>1)
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

//echo 'filename : ' . $rest;
if (file_exists($rest)) {
    ob_start();
    header('Content-Description: File Transfer');
    header('Content-Type: application/x-tar-gz');
    header('Content-Disposition: attachment; filename="'.basename($rest).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($rest));

    // Clean the output buffer and flush before reading the file
    ob_clean();
    flush();

    readfile($rest);
    ob_end_flush();
    // Terminate script execution
    //exit;
} else {
    echo "erreur, un bug est apparu, pas de chance :/";
}


$rest = str_replace(" ", "\ ", $rest); //supprime les espaces car ça fait buguer la suppression

exec('yes | rm ' . $rest); //supprime le fichier

if(sizeof($title)>1)
{
    if($rename)
    {
        exec('yes | rm -R ' . $rename); //supprime les fichiers
    }
    else
    {
        exec('yes | rm -R contenu'); //supprime les fichiers
    }
}
// Terminate script execution
exit;
?>
<a href="url.php"> retour </a>