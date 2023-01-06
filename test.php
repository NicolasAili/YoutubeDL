<?php
$rest = 'content.tar';
if (file_exists($rest)) { //télécharge le fichier
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
}
?>