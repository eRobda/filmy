<?php
session_start();

if (!isset($_SESSION["UID"]) || !isset($_SESSION["PID"])) {
    header("Location: index.php");
    exit();
}

$movieName = isset($_GET["name"]) ? $_GET["name"] : '';

if($movieName == ''){
    header("Location: home.php");
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
    function formatToTwoDigits(numberString) {
        return numberString.padStart(2, '0');
    }

    document.addEventListener("DOMContentLoaded", async () => {
        const movieName = "<?php echo $movieName ?>";
        const serie = "<?php echo $_GET["serie"] ?>";
        const epizoda = "<?php echo $_GET["epizoda"] ?>";
        const time = await (await fetch("get_series_watch_time.php?serialId=<?php echo $_GET["serialId"] ?>&serie=<?php echo $_GET["serie"] ?>&epizoda=<?php echo $_GET["epizoda"] ?>")).text();

        if (movieName) {
            const res = await (await fetch("http://37.46.211.41/getMovie?name=" + movieName + " s" + formatToTwoDigits(serie) + "e" + formatToTwoDigits(epizoda))).json();

            document.getElementById("video-link").innerHTML = "<video id='video' class='w-dvw h-dvh' preload='auto' data-setup='{}' autoplay controls><source id='videoSource' src=" + res.videoSrc + " type='video/mp4'></video>"
            if(time !== ""){
                document.getElementById("video").currentTime = time;
            }
        }
        else{
            console.error("No movie name!");
        }
    });

    setInterval(() => {
        if(!document.getElementById("video").paused){
            const url = "set_series_watch_time.php?serialId=<?php echo strval(intval($_GET["serialId"])) ?>&serie=<?php echo strval(intval($_GET["serie"])) ?>&epizoda=<?php echo strval(intval($_GET["epizoda"])) ?>&cas=" + Math.floor(document.getElementById("video").currentTime) + "&celkovy_cas=" + Math.floor(document.getElementById("video").duration);
            console.log("sent view request with url: " + url)
            fetch(url);
        }
    }, 10000)
</script>
</body>
</html>

