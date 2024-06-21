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
    <!--<link rel="stylesheet" href="@fortawesome/fontawesome-free/css/all.min.css">-->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="newurl.css">
</head>

<body>
    <header class="header">
        <div class="left">onchetube</div>
        <div class="right">FAQ</div>
    </header>
    <div class="main">
        <button type="button" class="btn btn-danger btn-floating btn-lg" id="btn-back-to-top">
            <i class="fas fa-arrow-up"></i>
        </button>
        <h1> Bienvenue sur OncheTube</h1>
        <div class="defaultformat">
            <h4>Modifier tous les formats</h4>
            <select id="main-dropdown" style="width: 150px;">
                <option value="besta" data-icon="fa-solid fa-music">automatique</option>
                <option value="aac" data-icon="fa-solid fa-music">aac</option>
                <option value="flac" data-icon="fa-solid fa-music">flac</option>
                <option value="mp3" data-icon="fa-solid fa-music">mp3</option>
                <option value="m4a" data-icon="fa-solid fa-music">m4a</option>
                <option value="opus" data-icon="fa-solid fa-music">opus</option>
                <option value="vorbis" data-icon="fa-solid fa-music">vorbis</option>
                <option value="wav" data-icon="fa-solid fa-music">wav</option>
                <option value="bestv" data-icon="fa-solid fa-video">automatique</option>
                <option value="3gp" data-icon="fa-solid fa-video">3gp</option>
                <option value="aac" data-icon="fa-solid fa-video">aac</option>
                <option value="flv" data-icon="fa-solid fa-video">flv</option>
                <option value="mp4" data-icon="fa-solid fa-video">mp4</option>
                <option value="ogg" data-icon="fa-solid fa-video">ogg</option>
                <option value="webm" data-icon="fa-solid fa-video">webm</option>
            </select>
        </div>
        <form id="form" action="newdownload.php" method="post" style="margin-top: 15px; margin-left: 15px;">
            <div id="inputFormRowOff">
                <div class="input-group mb-3">
                    <input type="text" name="title[]" class="form-control m-input" placeholder="Copiez un lien ici" autocomplete="off" required>
                    <select class="format-dropdown" name="format[]" style="width: 150px;">
                        <option value="besta" data-icon="fa-solid fa-music">automatique</option>
                        <option value="aac" data-icon="fa-solid fa-music">aac</option>
                        <option value="flac" data-icon="fa-solid fa-music">flac</option>
                        <option value="mp3" data-icon="fa-solid fa-music">mp3</option>
                        <option value="m4a" data-icon="fa-solid fa-music">m4a</option>
                        <option value="opus" data-icon="fa-solid fa-music">opus</option>
                        <option value="vorbis" data-icon="fa-solid fa-music">vorbis</option>
                        <option value="wav" data-icon="fa-solid fa-music">wav</option>
                        <option value="bestv" data-icon="fa-solid fa-video">automatique</option>
                        <option value="3gp" data-icon="fa-solid fa-video">3gp</option>
                        <option value="aac" data-icon="fa-solid fa-video">aac</option>
                        <option value="flv" data-icon="fa-solid fa-video">flv</option>
                        <option value="mp4" data-icon="fa-solid fa-video">mp4</option>
                        <option value="ogg" data-icon="fa-solid fa-video">ogg</option>
                        <option value="webm" data-icon="fa-solid fa-video">webm</option>
                    </select>
                    <div class="timerSelect">
                        <img class="timer" src="img/timer.jpg" alt="Timer">
                    </div>
                    <button id="addRow" type="button" class="btn btn-info">Ajouter un lien</button>
                </div>
            </div>
            <div id="newRow"></div>
            <div id="formOptions">
                <div id="renamediv">
                    <h4>(optionnel) Renommez le dossier qui contiendra vos téléchargements</h4>
                    <input type="text" id="rename" name="rename" class="form-control" autocomplete="off">
                </div>
                <div id="ordrediv">
                    <fieldset>
                        <input type="checkbox" id="ordre" name="ordre">
                        <legend>(optionnel) Cochez pour conserver l'ordre (ordre alphabétique par défaut)</legend>
                    </fieldset>
                </div>
            </div>
            <div id="buttons">
                <input type="submit" name="submit" value="TELECHARGER" class="btnsubmit download-btn">
                <input type="button" name="reinit" id="reinit" value="REINITIALISER" class="btnreset reset-btn">
            </div>
        </form>
        <div style="margin-top: 30px; margin-left: 15px;">
            <h4> Note : pour télécharger seulement une partie d'une vidéo, cliquez sur la pendule puis renseignez le début et la fin de la section que vous souhaitez télécharger. Si la section commence au début de la vidéo, ou termine à la fin de la vidéo, ne mettez rien dans les champs Debut/Fin (en fonction de votre situation) </h4>
            <h4 style="background-color: red; display: inline;"> Format pour les champs "debut" et "fin" : HH:MM:SS OU MM:SS OU SS </h4>
            <h4> Exemples : 01:05:22 pour 1 heure 5 minutes 22 secondes, 05:25 pour 5 minutes 25, 04 pour 4 secondes </h4>
        </div>
        <h3 style="margin-left: 2%; margin-top: 50px;"> A noter </h3>
        <div>
            <p style="width: 80%; margin-left: 2%;">
                Soyez patient, le téléchargement n'est pas instantané. D'abord les fichiers sont téléchargés sur le serveur (période où vous devez attendre) puis ils vous sont transmis.
            </p>
            <p style="width: 80%; margin-left: 2%;">
                Si une erreur apparaît ou que un ou plusieurs fichiers sont manquants, cela est très probablement dû au fait que le ou les fichiers souhaités n'est ou ne sont pas disponibles dans votre pays, ou bien que le format souhaité n'est pas disponible.
            </p>
        </div>
    </div>
</body>

</html>

<script type="text/javascript">
    $(document).ready(function() {
        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }
            var $option = $(
                '<span>' + option.text + ' <i class="' + $(option.element).data('icon') + '"></i></span>'
            );
            return $option;
        }

        $('#main-dropdown').select2({
            templateResult: formatOption,
            templateSelection: formatOption
        });
        $('.format-dropdown').select2({
            templateResult: formatOption,
            templateSelection: formatOption
        });
        // Add event listener to the main dropdown
        $('#main-dropdown').on('select2:select', function(e) {
            // Get the selected value from the main dropdown
            const selectedValue = e.params.data.id;

            // Update other dropdowns
            updateOtherDropdowns(selectedValue);
        });
    });
    // Function to update other dropdowns based on the main dropdown's value
    function updateOtherDropdowns(selectedValue) {
        const commonDropdowns = document.querySelectorAll('.format-dropdown');
        commonDropdowns.forEach(dropdown => {
            $(dropdown).val(selectedValue).trigger('change'); // Update value and trigger change event for Select2
        });
    }

    function isPlaylistActive() {
        var anyChecked = false;
        $('.playlistcheckbox').each(function() {
            if ($(this).prop('checked')) {
                anyChecked = true;
                return false; // Exit the loop early if at least one is checked
            }
        });
        if ($("#inputFormRow").length == 0 && anyChecked == true) {

        }
        console.log(anyChecked);
        return anyChecked;
    }

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
                    checkboxElement.className = 'playlistcheckbox';
                    checkboxElement.name = 'playlistcheckbox';
                    checkboxElement.checked = true;
                    $("#ordrediv").css("display", "block");

                    // Step 2: Attach an event listener
                    checkboxElement.addEventListener('change', function() {
                        // Step 3: Define the function to be called when the checkbox changes
                        if (checkboxElement.checked) {
                            $("#ordrediv").css("display", "block");

                            // Do something when the checkbox is checked
                        } else {
                            if (!isPlaylistActive()) {
                                console.log("unchecking");
                                $("#ordrediv").css("display", "none");
                            }
                            // Do something when the checkbox is unchecked
                        }
                    });

                    // Insert the checkbox before the second child of the closest element
                    closest.insertBefore(checkboxElement, closest.children[1]);
                } else {
                    console.log("not detected");
                    // Check if there's already a checkbox present
                    const existingCheckbox = closest.querySelector('.playlistcheckbox');
                    if (existingCheckbox) {
                        existingCheckbox.remove(); // Remove the existing checkbox
                    }
                    if (!isPlaylistActive()) {
                        console.log("replacing with non playlist url");
                        $("#ordrediv").css("display", "none");
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
        html += '<input type="text" name="title[]" class="form-control m-input" placeholder="Copiez un lien ici" autocomplete="off">';

        html += '<select class="format-dropdown" name="format[]" style="width: 150px">' +
            '<option value="besta" data-icon="fa-solid fa-music">automatique</option>' +
            '<option value="aac" data-icon="fa-solid fa-music">aac (a)</option>' +
            '<option value="flac" data-icon="fa-solid fa-music">flac (a)</option>' +
            '<option value="mp3" data-icon="fa-solid fa-music">mp3 (a)</option>' +
            '<option value="m4a" data-icon="fa-solid fa-music">m4a (a)</option>' +
            '<option value="opus" data-icon="fa-solid fa-music">opus (a)</option>' +
            '<option value="vorbis" data-icon="fa-solid fa-music">vorbis (a)</option>' +
            '<option value="wav" data-icon="fa-solid fa-music">wav (a)</option>' +
            '<option value="bestv" data-icon="fa-solid fa-video">automatique</option>' +
            '<option value="3gp" data-icon="fa-solid fa-video">3gp (v)</option>' +
            '<option value="aac" data-icon="fa-solid fa-video">aac (v)</option>' +
            '<option value="flv" data-icon="fa-solid fa-video">flv (v)</option>' +
            '<option value="mp4" data-icon="fa-solid fa-video">mp4 (v)</option>' +
            '<option value="ogg" data-icon="fa-solid fa-video">ogg (v)</option>' +
            '<option value="webm" data-icon="fa-solid fa-video">webm (v)</option>' +
            '</select>';
        html += '<div class="timerSelect"><img class="timer" src="img/timer.jpg" alt="Timer"></div>';
        html += '<div class="input-group-append">';

        html += '<button id="removeRow" type="button" class="btn btn-danger">Supprimer</button>';
        html += '</div>';
        html += '</div>';
        $('#newRow').append(html);

        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }
            var $option = $(
                '<span>' + option.text + ' <i class="' + $(option.element).data('icon') + '"></i></span>'
            );
            return $option;
        }

        // Reinitialize Select2 for new elements
        $('.format-dropdown').select2({
            templateResult: formatOption,
            templateSelection: formatOption,
            width: 'style'
        });

        $("#renamediv").css("display", "block");
        $("#rename").attr("placeholder", "Renommez le dossier qui contiendra les téléchargements");
        $("#ordrediv").css("display", "block");
        detectPlaylist();
    });

    // remove row
    $(document).on('click', '#removeRow', function() {
        $(this).closest('#inputFormRow').remove();
        if ($("#inputFormRow").length == 0) {
            $("#renamediv").css("display", "none");
            $("#rename").attr("placeholder", "");
            $("#rename").val('');
            if (!isPlaylistActive()) {
                console.log("deleting last row");
                $("#ordrediv").css("display", "none");
            }
        }
    });

    $("#reinit").click(function() {
        while ($("#inputFormRow").length != 0) {
            $('#inputFormRow').remove();
        }
        $('.form-control').val('');
        $("#renamediv").css("display", "none");
        console.log("reinit");
        $("#ordrediv").css("display", "none");
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