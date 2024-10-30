<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Download Progress</title>
    <style>
        .progress-bar {
            width: 100%;
            background-color: #f3f3f3;
        }

        .progress-bar-fill {
            height: 20px;
            width: 0%;
            background-color: #4caf50;
            text-align: center;
            color: white;
        }
    </style>
</head>

<body>
    <div>
        Title
        <div class="progress-bar">
            <div class="progress-bar-fill" id="progress-bar-fill">0%</div>
        </div>
    </div>


    <script>
        function updateProgressBar(percentage) {
            const progressBar = document.getElementById('progress-bar-fill');
            progressBar.style.width = percentage + '%';
            progressBar.textContent = percentage + '%';
        }

        setTimeout(() => {
            updateProgressBar(20);
        }, 1500); // Update to 20% after 1.5 seconds

        setTimeout(() => {
            updateProgressBar(70);
        }, 3000); // Update to 70% after 3 seconds
    </script>
</body>


</html>