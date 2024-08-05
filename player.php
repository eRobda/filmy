<?php
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION["UID"]) || !isset($_SESSION["PID"])) {
    header("Location: index.php");
    exit();
}

// Get movie name from the query string
$movieName = isset($_GET["name"]) ? $_GET["name"] : '';

// Redirect to home if no movie name is provided
if (empty($movieName)) {
    header("Location: home.php");
    exit();
}

// Function to format number to two digits
function formatToTwoDigits($numberString) {
    return str_pad($numberString, 2, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="js/Movies.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-neutral-950">
<div id="video-link" class="mt-4 text-center text-white"></div>

<script>
    document.addEventListener("DOMContentLoaded", async () => {
        const movieName = "<?php echo htmlspecialchars($movieName, ENT_QUOTES, 'UTF-8'); ?>";
        const serie = "<?php echo htmlspecialchars($_GET['serie'], ENT_QUOTES, 'UTF-8'); ?>";
        const epizoda = "<?php echo htmlspecialchars($_GET['epizoda'], ENT_QUOTES, 'UTF-8'); ?>";

        // Fetch the current watch time
        const timeResponse = await fetch(`get_series_watch_time.php?serialId=<?php echo urlencode($_GET['serialId']); ?>&serie=${serie}&epizoda=${epizoda}`);
        const time = await timeResponse.text();
        console.log(time);

        if (movieName) {
            // Fetch the movie data
            const response = await fetch(`http://37.46.211.41:3000/getMovie?name=${encodeURIComponent(movieName)}%20s${formatToTwoDigits(serie)}e${formatToTwoDigits(epizoda)}`);
            var res = await response.json();
            if(res.error === "No movies found"){
                //nothing found try increment series insted of episode
                location.href = "player.php?name=" + movieName + "&typ=serial&serialId=<?php echo $_GET["serialId"] ?>&serie=" + formatToTwoDigits(parseInt(serie) + 1) + "&epizoda=" + formatToTwoDigits(1);
            }

            // Create video element
            const videoElement = document.createElement('video');
            videoElement.id = 'video';
            videoElement.classList.add('w-dvw', 'h-dvh', 'relative');
            videoElement.preload = 'auto';
            videoElement.autoplay = true;
            videoElement.controls = true;

            // Create source element
            const sourceElement = document.createElement('source');
            sourceElement.id = 'videoSource';
            sourceElement.src = res.videoSrc;
            sourceElement.type = 'video/mp4';

            // Append source to video
            videoElement.appendChild(sourceElement);

            // Create back link
            const backLink = document.createElement('a');
            backLink.href = 'home.php';
            const backImage = document.createElement('img');
            backImage.title = 'Zpět';
            backImage.src = 'media/icon_next.png';
            backImage.classList.add('absolute', 'top-10', 'h-8', 'rotate-180', 'left-10');
            backLink.appendChild(backImage);

            // Create next episode link
            const nextEpisodeLink = document.createElement('a');
            nextEpisodeLink.href = `player.php?name=${encodeURIComponent(movieName)}&typ=serial&serialId=<?php echo urlencode($_GET['serialId']); ?>&serie=${serie}&epizoda=${formatToTwoDigits(parseInt(epizoda) + 1)}`;
            const nextEpisodeImage = document.createElement('img');
            nextEpisodeImage.title = 'Další epizoda';
            nextEpisodeImage.src = 'media/icon_skip.png';
            nextEpisodeImage.classList.add('absolute', 'top-9', 'h-9', 'right-10');
            nextEpisodeLink.appendChild(nextEpisodeImage);

            // Append elements to the container
            const videoLinkDiv = document.getElementById('video-link');
            videoLinkDiv.appendChild(videoElement);
            videoLinkDiv.appendChild(backLink);
            videoLinkDiv.appendChild(nextEpisodeLink);

            // Set the current time if available
            if (time !== "") {
                videoElement.currentTime = time;
            }
        } else {
            console.error("No movie name!");
        }
    });

    // Update the watch time every 10 seconds
    setInterval(() => {
        const video = document.getElementById('video');
        if (!video.paused) {
            const url = `set_series_watch_time.php?serialId=<?php echo urlencode($_GET['serialId']); ?>&serie=${serie}&epizoda=${epizoda}&cas=${Math.floor(video.currentTime)}&celkovy_cas=${Math.floor(video.duration)}`;
            console.log("sent view request with url: " + url);
            fetch(url);
        }
    }, 10000);

    // Utility function to format numbers to two digits
    function formatToTwoDigits(numberString) {
        return numberString.toString().padStart(2, '0');
    }
</script>
</body>
</html>
