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
        #btn-back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
        }

        .timer,
        .isOpened {
            width: 38px;
            height: 38px;
            border: 2px solid #ff1f00;
            transition: transform 0.2s;
        }

        .timer:hover,
        .isOpened:hover {
            cursor: pointer;
            transform: translateY(-5px);
        }

        .btn {
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-5px);
        }

        #inputFormRow,
        #inputFormRowOff {
            margin-right: 15px;
        }

        .timerSelect {
            display: flex;
        }

        .timerSelectInput {
            border: 1px solid gray;
            padding: 5px;
        }


        .timerInputFin {
            float: right;
        }


        .timerInputInput {
            width: 96px;
            padding-left: 4px;
            padding-right: 4px;
        }
    </style>
</head>

<body>
    <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>
    <div style="margin-top: 30px; margin-left: 15px;">
        <h1> Outil de telechargement de vidéos youtube</h1>
        <h4> Note : pour télécharger seulement une partie d'une vidéo, cliquez sur la pendule puis renseignez le début et la fin de la section que vous souhaitez télécharger. Si la section commence au début de la vidéo, ou termine à la fin de la vidéo, ne mettez rien dans les champs Debut/Fin (en fonction de votre situation) </h4>
        <h4 style="background-color: red; display: inline;"> Format pour les champs "debut" et "fin" : HH:MM:SS OU MM:SS OU SS </h4>
        <h4> Exemples : 01:05:22 pour 1 heure 5 minutes 22 secondes, 05:25 pour 5 minutes 25, 04 pour 4 secondes </h4>
    </div>
    <select id="main-dropdown">
        <option value="besta">best (a)</option>
        <option value="aac">aac (a)</option>
        <option value="flac">flac (a)</option>
        <option value="mp3">mp3 (a)</option>
        <option value="m4a">m4a (a)</option>
        <option value="opus">opus (a)</option>
        <option value="vorbis">vorbis (a)</option>
        <option value="wav">wav (a)</option>
        <option value="bestv">best (v)</option>
        <option value="3gp">3gp (v)</option>
        <option value="aac">aac (v)</option>
        <option value="flv">flv (v)</option>
        <option value="mp4">mp4 (v)</option>
        <option value="ogg">ogg (v)</option>
        <option value="webm">webm (v)</option>
    </select>
    <form id="form" action="newdownload.php" method="post" style="margin-top: 15px; margin-left: 15px;">
        <div id="inputFormRowOff">
            <div class="input-group mb-3">
                <input type="text" name="title[]" class="form-control m-input" placeholder="Entrer URL" autocomplete="off" required>
                <select class="format-dropdown" name="format[]">
                    <option value="besta">best (a)</option>
                    <option value="aac">aac (a)</option>
                    <option value="flac">flac (a)</option>
                    <option value="mp3">mp3 (a)</option>
                    <option value="m4a">m4a (a)</option>
                    <option value="opus">opus (a)</option>
                    <option value="vorbis">vorbis (a)</option>
                    <option value="wav">wav (a)</option>
                    <option value="bestv">best (v)</option>
                    <option value="3gp">3gp (v)</option>
                    <option value="aac">aac (v)</option>
                    <option value="flv">flv (v)</option>
                    <option value="mp4">mp4 (v)</option>
                    <option value="ogg">ogg (v)</option>
                    <option value="webm">webm (v)</option>
                </select>
                <div class="timerSelect">
                    <img class="timer" src="img/timer.jpg" alt="Timer">
                </div>
                <button id="addRow" type="button" class="btn btn-info">Ajouter un lien</button>
            </div>
        </div>
        <div id="newRow"></div>
        <h4> Renommez si vous le souhaitez le dossier qui contiendra vos téléchargements (uniquement si plusieurs liens) </h4>
        <input type="text" id="rename" name="rename" class="form-control" autocomplete="off" disabled>
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
    // Get references to the main dropdown and the other dropdowns
    const mainDropdown = document.getElementById('main-dropdown');

    // Function to update other dropdowns based on the main dropdown's value
    function updateOtherDropdowns(selectedValue) {
        const commonDropdowns = document.querySelectorAll('.format-dropdown');
        commonDropdowns.forEach(dropdown => {
            dropdown.value = selectedValue;
        });
    }

    // Add an event listener to the main dropdown
    mainDropdown.addEventListener('change', function() {
        // Get the selected value from the main dropdown
        const selectedValue = mainDropdown.value;

        // Update the other dropdowns
        updateOtherDropdowns(selectedValue);
    });

    function detectPlaylist() {
        const inputElements = document.querySelectorAll('.m-input');

        if (inputElements.length > 0) {
            const inputElement = inputElements[inputElements.length - 1];
            // Now you can work with the last element with class 'm-input'
            inputElement.addEventListener('input', function() {
                const triggeredElement = event.target; // This is the element that triggered the event

                // Rest of your code...

                // Example: Log the triggered element to the console
                console.log('Event triggered by:', triggeredElement);
                const inputValue = inputElement.value.toLowerCase();
                const containsList = inputValue.includes('list=');
                const closest = inputElement.closest('.input-group');

                if (containsList) {
                    console.log("'list=' detected in the input:", inputValue);

                    // Create the checkbox element
                    const checkboxElement = document.createElement('input');
                    checkboxElement.type = 'checkbox';
                    checkboxElement.id = 'playlistcheckbox';
                    checkboxElement.name = 'playlistcheckbox';
                    checkboxElement.checked = true;

                    // Insert the checkbox before the second child of the closest element
                    closest.insertBefore(checkboxElement, closest.children[1]);
                } else {
                    console.log("not detected");
                    // Check if there's already a checkbox present
                    const existingCheckbox = closest.querySelector('#playlistcheckbox');
                    if (existingCheckbox) {
                        existingCheckbox.remove(); // Remove the existing checkbox
                    }
                }
            });
        }
    }

    detectPlaylist();

    // add row
    $("#addRow").click(function() {
        var html = '';
        html += '<div id="inputFormRow">';
        html += '<div class="input-group mb-3">';
        html += '<input type="text" name="title[]" class="form-control m-input" placeholder="Entrer URL" autocomplete="off">';

        html += '<select class="format-dropdown" name="format[]">' + '<option value="besta">best (a)</option>' + '<option value="aac">aac (a)</option>' +
            '<option value="flac">flac (a)</option>' + '<option value="mp3">mp3 (a)</option>' + '<option value="m4a">m4a (a)</option>' +
            '<option value="opus">opus (a)</option>' + '<option value="vorbis">vorbis (a)</option>' + '<option value="wav">wav (a)</option>' +
            '<option value="bestv">best (v)</option>' + '<option value="3gp">3gp (v)</option>' + '<option value="aac">aac (v)</option>' +
            '<option value="flv">flv (v)</option>' + '<option value="mp4">mp4 (v)</option>' + '<option value="ogg">ogg (v)</option>' +
            '<option value="webm">webm (v)</option>' + '</select>';
        html += '<div class="timerSelect"><img class="timer" src="img/timer.jpg" alt="Timer"></div>';
        html += '<div class="input-group-append">';

        html += '<button id="removeRow" type="button" class="btn btn-danger">Supprimer</button>';
        html += '</div>';
        html += '</div>';
        $('#newRow').append(html);
        $("#rename").prop('disabled', false);
        $("#rename").attr("placeholder", "Renommez");
        $("#ordre").prop('disabled', false);
        detectPlaylist();
    });

    // remove row
    $(document).on('click', '#removeRow', function() {
        $(this).closest('#inputFormRow').remove();
        if ($("#inputFormRow").length == 0) {
            $("#rename").prop('disabled', true);
            $("#rename").attr("placeholder", "");
            $("#rename").val('');
            $("#ordre").prop('disabled', true);
        }
    });

    $("#reinit").click(function() {
        while ($("#inputFormRow").length != 0) {
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
            } else {
                timerSelectElements.push(-1);
            }
        });
        for (var i = 0; i < timerSelectElements.length; i++) {
            $('<input>').attr({
                type: 'hidden',
                name: 'selectedElements[]',
                value: timerSelectElements[i]
            }).appendTo('#form');
        }
        // Get a reference to the form element
        const elementInputs = document.querySelectorAll('.m-input');

        // Loop through each input element and perform an action
        elementInputs.forEach((input, index) => {
            if (input.value.includes('list=')) {
                const checkboxElement = input.nextElementSibling;
                // Check if the checkbox is checked
                const isCheckboxChecked = checkboxElement.checked;

                if (isCheckboxChecked) {
                    // Given YouTube video link
                    const videoLink = input.value;

                    // Extract the value of the 'list' parameter from the URL
                    const match = videoLink.match(/list=([^&]+)/);
                    const playlistId = match ? match[1] : null;
                    const playlistLink = `https://www.youtube.com/playlist?list=${playlistId}`;
                    input.value = playlistLink;
                }
            }
        });
    });


    //Get the button
    let mybutton = document.getElementById("btn-back-to-top");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {
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