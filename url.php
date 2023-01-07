<?php

/* * TODO :
-titre page
-(uniquement si plusieurs liens) > fonction javascript
- améliorer dans download la manière dont sont exécutées les commandes + prendre en compte les timers
- écrire note en haut de page sur comme se servir du timer
*/
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <style> 
            #btn-back-to-top 
            {
                position: fixed;
                bottom: 20px;
                right: 20px;
                display: none;
            }

            .timer, .isOpened
            {
                width: 38px;
                height: 38px;
                border: 2px solid #ff1f00;
                transition: transform 0.2s;
            }
            .timer:hover, .isOpened:hover
            {
                cursor: pointer;
                transform: translateY(-5px);
            }
            .btn
            {
                transition: transform 0.2s;
            }
            .btn:hover
            {
                transform: translateY(-5px);
            }

            #inputFormRow, #inputFormRowOff
            {
                margin-right: 15px;
            }
            .timerSelect
            {
                display: flex;
            }

            .timerSelectInput
            {
                border: 1px solid gray;
                padding: 5px;
            }


            .timerInputFin
            {
                float: right;
            }
            

            .timerInputInput
            {
                width: 96px;
                padding-left: 4px;
                padding-right: 4px;
            }


        </style>
    </head>
    <body>
        <button
            type="button"
            class="btn btn-danger btn-floating btn-lg"
            id="btn-back-to-top"
            >
        <i class="fas fa-arrow-up"></i>
        </button>
        <div style="margin-top: 30px; margin-left: 15px;">
            <h1> Outil de telechargement de vidéos youtube</h1>
            <h4> Note : pour télécharger seulement une partie d'une vidéo, cliquez sur la pendule puis renseignez le début et la fin de la section que vous souhaitez télécharger. Si la section commence au début de la vidéo, ou termine à la fin de la vidéo, ne mettez rien dans les champs Debut/Fin (en fonction de votre situation) </h4>
            <h4 style="background-color: red; display: inline;"> Format pour les champs "debut" et "fin" : HH:MM:SS OU MM:SS OU SS </h4>
            <h4> Exemples : 01:05:22 pour 1 heure 5 minutes 22 secondes, 05:25 pour 5 minutes 25, 04 pour 4 secondes </h4>
        </div>
        <form id="form" action="download.php" method="post" style="margin-top: 15px; margin-left: 15px;">
            <div id="inputFormRowOff">
                <div class="input-group mb-3">
                    <input type="text" name="title[]" class="form-control m-input" placeholder="Entrer URL" autocomplete="off" required>
                    <div class="timerSelect">
                        <img class="timer" src="img/timer.jpg" alt="Timer">
                    </div>
                    <button id="addRow" type="button" class="btn btn-info">Ajouter un lien</button>
                </div>
            </div>
            <div id="newRow"></div>
            <div>
                <input type="radio" id="mptrois" name="type" value="mptrois"
                     checked>
                <label for="mptrois">Audio</label>
                <div id="formataudio" style="margin-left: 50px;">
                    <div id="xx">
                        <legend>Format audio</legend>
                        <input type="radio" id="firstaud" name="format" value="best"
                         checked>
                        <label for="format">par défaut (le meilleur possible)</label>
                        <input type="radio" id="formataud" name="format" value="aac">
                        <label for="mptrois">aac</label>
                        <input type="radio" id="formataud" name="format" value="flac">
                        <label for="mptrois">flac</label>
                        <input type="radio" id="formataud" name="format" value="mp3">
                        <label for="mptrois">mp3</label>
                        <input type="radio" id="formataud" name="format" value="m4a">
                        <label for="mptrois">m4a</label>
                        <input type="radio" id="formataud" name="format" value="opus">
                        <label for="mptrois">opus</label>
                        <input type="radio" id="formataud" name="format" value="vorbis">
                        <label for="mptrois">vorbis</label>
                        <input type="radio" id="formataud" name="format" value="wav">
                        <label for="mptrois">wav</label>
                        <input type="radio" id="formataud" name="format" value="alac">
                        <label for="mptrois">alac</label>
                    </div>
                </div>
            </div>
            <div>
              <input type="radio" id="mpquatre" name="type" value="mpquatre">
              <label for="mpquatre">Video</label>
              <div id="formatvideo" style="visibility: hidden; display: none;">
                    <div id="xy">
                        <legend>Format video</legend>
                        <input type="radio" id="firstvid" name="format" value="best">
                        <label for="format">par défaut (le meilleur possible)</label>
                        <input type="radio" id="format" name="format" value="3gp">
                        <label for="mptrois">3gp</label>
                        <input type="radio" id="format" name="format" value="aac">
                        <label for="mptrois">aac</label>
                        <input type="radio" id="format" name="format" value="flv">
                        <label for="mptrois">flv</label>
                        <input type="radio" id="format" name="format" value="mp4">
                        <label for="mptrois">mp4</label>
                        <input type="radio" id="format" name="format" value="ogg">
                        <label for="mptrois">ogg</label>
                        <input type="radio" id="format" name="format" value="webm">
                        <label for="mptrois">webm</label>
                    </div>
                </div>
            </div>
            <h4> Renommez si vous le souhaitez le dossier qui contiendra vos téléchargements (uniquement si plusieurs liens) </h4>
            <input type="text" id="rename" name="rename" class="form-control m-input" autocomplete="off" disabled>
            <fieldset>
                <legend>Cochez cette case pour garder l'ordre des liens lors du téléchargement tels que vous les avez renseignés (uniquement si plusieurs liens)</legend>
                <div>
                    <input type="checkbox" id="ordre" name="ordre" checked disabled>
                    <label for="ordre">Coché pour oui, décoché pour non. Si case décochée (non) les fichiers seront dans l'ordre alphabétique.</label>
                </div>
            </fieldset>
            <input type="submit" name="submit" value="TELECHARGER" style="background-color: #1a53ff; color: white; border-radius: 6px; padding: 5px; padding-left: 12px; padding-right: 12px; cursor: pointer;">
        </form>
        <input type="button" name="reinit" id="reinit" value="REINITIALISER" style="margin-left: 15px; background-color: #f90d0d; color: white; border-radius: 6px; padding: 5px; padding-left: 12px; padding-right: 12px; cursor: pointer;">
        <h3 style="margin-left: 2%; margin-top: 50px;"> A noter </h3>
        <div>
            <p style="width: 80%; margin-left: 2%;"> 
                Soyez patient, le téléchargement n'est pas instantané. D'abord les fichiers sont téléchargés sur le serveur (période où vous devez attendre) puis ils vous sont transmis.
            </p>
            <p style="width: 80%; margin-left: 2%;"> 
                Si une erreur apparaît ou que un ou plusieurs fichiers sont manquants, cela est très probablement dû au fait que le ou les fichiers souhaités n'est ou ne sont pas disponibles dans votre pays, ou bien que le format souhaité n'est pas disponible.
            </p>
        </div>
    </body>
</html>


<script type="text/javascript">
    // add row
    $("#addRow").click(function () {
        var html = '';
        html += '<div id="inputFormRow">';
        html += '<div class="input-group mb-3">';
        html += '<input type="text" name="title[]" class="form-control m-input" placeholder="Entrer URL" autocomplete="off">';
        html += '<div class="timerSelect"><img class="timer" src="img/timer.jpg" alt="Timer"></div>';
        html += '<div class="input-group-append">';
        
        html += '<button id="removeRow" type="button" class="btn btn-danger">Supprimer</button>';
        html += '</div>';
        html += '</div>';


        $('#newRow').append(html);
        $("#rename").prop('disabled', false);
        $("#rename").attr("placeholder", "Renommez");
        $("#ordre").prop('disabled', false);
    });

    // remove row
    $(document).on('click', '#removeRow', function () {
        $(this).closest('#inputFormRow').remove();
        if($("#inputFormRow").length == 0)
        {
            $("#rename").prop('disabled', true);
            $("#rename").attr("placeholder", "");
            $("#rename").val('');
            $("#ordre").prop('disabled', true);
        }
    });

    $("#mptrois").click(function () 
    {
        $("#formatvideo").css('visibility', 'hidden');
        $("#formatvideo").css('display', 'none');
        $("#formataudio").css('visibility', 'visible');
        $("#formataudio").css('display', 'contents');
        $("#xx").css('margin-left', '50px');
        $("#firstvid").prop('checked', false);
        $("#firstaud").prop('checked', true);   
    });
    $("#mpquatre").click(function () 
    {
        $("#formataudio").css('visibility', 'hidden');
        $("#formataudio").css('display', 'none');
        $("#formatvideo").css('visibility', 'visible');
        $("#formatvideo").css('display', 'contents');
        $("#xy").css('margin-left', '50px');
        $("#firstaud").prop('checked', false);
        $("#firstvid").prop('checked', true);
    });

    $("#reinit").click(function () {
        while($("#inputFormRow").length != 0) 
        {
            $('#inputFormRow').remove();
        }
        $('.form-control').val('');
        $("#rename").prop('disabled', true);
        $("#ordre").prop('disabled', true);
    });

    //timer
    $(document).on('click', '.timer', function() {

        const $closest = $(this).closest('.timerSelect');
        var setTimer = '';
        $closest.empty();
        setTimer += '<div class="timerSelectInput">';
            setTimer += '<div class="timerInput">';
                setTimer += 'Debut ';
                setTimer += '<input type="text" class="timerInputInput" name="posTimer[]" placeholder="" autocomplete="off">';
            setTimer += '</div>';
            setTimer += '<div class="timerInput">';
                setTimer += 'Fin ';
                setTimer += '<input type="text" class="timerInputInput timerInputFin" name="posTimer[]" placeholder="" autocomplete="off">';
            setTimer += '</div>';
        setTimer += '</div>';
        setTimer += '<img class="isOpened" src="img/timerCloseEdit.png" alt="Timer">';

        $closest.append(setTimer);
    });

        //timer close
        $(document).on('click', '.isOpened', function() {
            const $closest = $(this).closest('.timerSelect');
            var setTimer = '';
            $closest.empty();
            setTimer += '<img class="timer" src="img/timer.jpg" alt="Timer">';
            $closest.append(setTimer);
        });

    $('#form').submit(function() {
        $("[name='selectedElements[]']").remove();
        var timerSelectElements = []; //créé le tableau
        $('.timerSelect').each(function(index) { //parcours tous les timers
          if ($(this).children('.timerSelectInput').length > 0) { //si on a renseigné des temps alors on les ajoute
            timerSelectElements.push(index);
          }
        });
        for (var i = 0; i < timerSelectElements.length; i++) {
          $('<input>').attr({
            type: 'hidden',
            name: 'selectedElements[]',
            value: timerSelectElements[i]
          }).appendTo('#form');
        }
    });


    //Get the button
    let mybutton = document.getElementById("btn-back-to-top");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function () {
    scrollFunction();
    };

    function scrollFunction() {
    if (
        document.body.scrollTop > 20 ||
        document.documentElement.scrollTop > 20
    ) {
        mybutton.style.display = "block";
    } else {
        mybutton.style.display = "none";
    }
    }
    // When the user clicks on the button, scroll to the top of the document
    mybutton.addEventListener("click", backToTop);

    function backToTop() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
    }
</script>