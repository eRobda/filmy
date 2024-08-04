<?php
// home.php
session_start();

if (!isset($_SESSION["UID"]) || !isset($_SESSION["PID"])) {
    header("Location: index.php");
    exit();
}

require_once 'backend/db.php';

// Get the single instance of the Database class
$db = Database::getInstance();

// Fetch the profile details for the selected profile
$profileId = $_SESSION["PID"];
$stmt = $db->getConnection()->prepare("SELECT * FROM profily WHERE id = ?");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if (!$profile) {
    echo "Profile not found.";
    exit();
}

$serialy = $db->getSerialy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <script src="js/Movies.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-rounded {
            font-variation-settings:
                    'FILL' 0,
                    'wght' 300,
                    'GRAD' 0,
                    'opsz' 20
        }
    </style>
    <style>
        .fade-out {
            transition: opacity 1s ease;
            opacity: 0;
        }
        /* width */
        ::-webkit-scrollbar {
            width: 5px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: rgb(10,10,10);
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: rgb(50,50,50);
            border-radius: 1rem;
        }
    </style>
</head>
<body class="overflow-x-hidden bg-neutral-950">

<section id="wellcome-screen" class="z-50 fixed h-dvh gap-4 w-dvw flex flex-col items-center justify-center" style="background: radial-gradient(circle, rgba(20,20,20,1) 0%, rgba(10,10,10,1) 100%)">
    <h1 class="text-white text-5xl font-semibold">Vítej,</h1>
    <p class="text-white text-4xl font-semibold"><?php echo htmlspecialchars($profile['jmeno_profilu']); ?></p>
</section>

<section class="w-dvw z-10 h-[100dvh] relative" >
    <div class="absolute w-full h-full" style="background: linear-gradient(-90deg, rgba(39,39,39,0) 60%, rgba(26,26,26,0.6503851540616247) 85%, rgba(21,21,21,0.8128501400560224) 100%);"></div>
    <img class=" w-full h-full object-cover object-bottom" src="https://image.tmdb.org/t/p/original/56v2KjBlU4XaOv9rVYEQypROD7P.jpg" alt="">
    <div class="absolute bottom-10 left-10 flex flex-col gap-4">
        <h3 class="text-5xl  font-bold leading-[3rem] text-white">Stranger Things </h3>
        <p class="w-1/3 leading-tight left-10 text-white mb-20">Chlapec se ztratí neznámo kde a město začne odhalovat svoje záhady, mezi které patří i tajné experimenty, děsivě nadpřirozené síly a jedna malá podivná holka.</p>
        <div class="absolute bottom-0">
            <a href="player.php?name=stranger things&typ=serial&serialId=1&serie=1&epizoda=1" class="flex items-center py-2 px-2 pr-4 rounded-md bg-white cursor-pointer">
                <img class="h-6" src="media/play.png" alt="">
                <p class="">Přehrát</p>
            </a>
        </div>
    </div>
</section>



<!--header-->
<section class="fixed top-0 z-10 w-dvw flex justify-between items-center p-4" style="background: linear-gradient(0, rgba(0,0,0,0) 0%, rgba(0,0,0,0.4) 30%, rgba(10,10,10,0.8) 80%);">
    <div class="text-white text-xl pl-6 font-semibold">Filmy</div>

    <div class="flex gap-2 items-center pr-6">
        <p class="text-white font-semibold"><?php echo htmlspecialchars($profile['jmeno_profilu']); ?></p>
        <a href="profile_select.php" class="cursor-pointer rounded-md flex items-center gap-1 hover:bg-[rgba(255,255,255,.3)] group px-2 py-1 transition-all duration-300">
            <span class="material-symbols-rounded text-white group-hover:text-black transition-colors duration-300">group</span>
            <p class="text-white group-hover:text-black transition-colors duration-300">Změnit profil</p>
        </a>

        <a href="logout.php" class="cursor-pointer rounded-md flex items-center gap-1 hover:bg-white group px-2 py-1 transition-all duration-300">
            <span class="material-symbols-rounded text-white group-hover:text-black transition-colors duration-300">logout</span>
            <p class="text-white group-hover:text-black transition-colors duration-300">Odhlásit se</p>
        </a>
    </div>
</section>

<section class="px-10 pt-5 text-2xl font-semibold">
    <h1 class="text-white">Rozkoukané seriály</h1>
    <div class="w-full mt-4 flex gap-6 h-60">
        <?php
        function formatToTwoDigits($numberString) {
            return str_pad($numberString, 2, '0', STR_PAD_LEFT);
        }
        function convertSecondsToMinutes($seconds) {
            return ceil($seconds / 60);
        }

        //var_dump($db->getRozdivaneSerialy($_SESSION["PID"]));
        foreach ($db->getRozdivaneSerialy($_SESSION["PID"]) as $serial){
            $postup = $db->getPostupSerialu($_SESSION["PID"], $serial["id"]);
            $serie = formatToTwoDigits($postup["serie"]);
            $epizoda = formatToTwoDigits($postup["epizoda"]);
            $postupString = "s" . $serie . "e" . $epizoda;
            $serialId = $serial["id"];
            $internalName = $serial["internal_name"];
            $coverUrl = $serial["cover_url"];
            $cas = convertSecondsToMinutes(intval($postup["cas"]));
            $celkovyCas = convertSecondsToMinutes(intval($postup["celkovy_cas"]));
            $postupProcenta = strval(intval(($cas / $celkovyCas) * 100)) . "%";
            echo "
                <a href='player.php?name=$internalName&typ=serial&serialId=$serialId&serie=$serie&epizoda=$epizoda' class='h-full relative cursor-pointer hover:scale-105 transition transition-duration-300'>
                    <img class='h-full' src='$coverUrl'>
                    <div class='w-full h-1/3 absolute top-0' style='background: linear-gradient(0, rgba(0,0,0,0) 20%, rgba(10,10,10,.9) 100%);'></div>
                    <p class='absolute top-2 text-sm text-white w-full text-center'>S$serie E$epizoda   </p>
                    
                    <div class='text-white text-sm flex gap-2 justify-center items-center mt-2'>       
                        <div class='h-1 w-5/6 relative '>
                            <div class='w-full rounded h-full absolute bg-white'></div>
                            <div class='w-[$postupProcenta] rounded h-full absolute bg-red-500'></div>
                        </div>
                    </div>
                </a>
                
            ";
        }
        ?>
    </div>
</section>

<section class="pt-10 px-10 text-2xl font-semibold">
    <h1 class="text-white">Americké TV pořady</h1>
    <div class="w-full mt-4 flex gap-6 h-60">
        <?php
        foreach ($serialy as $serial) {
            if ($serial["category"] == "Americké TV pořady") {
                $postup = $db->getPostupSerialu($_SESSION["PID"], $serial["id"]);
                $internalName = $serial["internal_name"];
                $serialId = $serial['id'];
                $coverUrl = $serial["cover_url"];
                $serie = formatToTwoDigits(isset($postup["serie"]) ? $postup["serie"] : "01"); // Default to "01" if $postup is null
                $epizoda = formatToTwoDigits(isset($postup["epizoda"]) ? $postup["epizoda"] : "01"); // Default to "01" if $postup is null

                echo "
                        <a href='player.php?name=$internalName&typ=serial&serialId=$serialId&serie=$serie&epizoda=$epizoda' class='w-[12%] cursor-pointer hover:scale-105 transition transition-duration-300'>
                            <img class='h-full' src='$coverUrl'>
                        </a>
                    ";
            }
        }
        ?>
    </div>
</section>

<section class="pt-10 px-10 pb-10 text-2xl font-semibold">
    <h1 class="text-white">Dnešní filmový žebříček</h1>
    <div class="w-full mt-4 flex gap-28 h-60">
        <?php
        $top5filmy = $db->getTop5Filmy();

        ?>
        <a href='' class='w-[12%] relative h-full '>
            <div class="absolute z-1 h-full grid place-items-center">
                <h1 class="z-1 font-extrabold text-neutral-800 text-[300px] mt-[-2rem]" style="-webkit-text-stroke: 4px rgb(50,50,50);">1</h1>
            </div>
            <img class='relative z-2 h-full ml-[6rem] cursor-pointer hover:scale-105 transition transition-duration-300' src='<?php echo $top5filmy[0]["cover_url"] ?>'>
        </a>

        <a href='' class='w-[12%] relative h-full '>
            <div class="absolute z-1 h-full grid place-items-center">
                <h1 class="z-1 font-extrabold text-neutral-800 text-[300px] mt-[-2rem]" style="-webkit-text-stroke: 4px rgb(50,50,50);">2</h1>
            </div>
            <img class='relative z-2 h-full ml-28 cursor-pointer hover:scale-105 transition transition-duration-300' src='<?php echo $top5filmy[1]["cover_url"] ?>'>
        </a>

        <a href='' class='w-[12%] relative h-full '>
            <div class="absolute z-1 h-full grid place-items-center">
                <h1 class="z-1 font-extrabold text-neutral-800 text-[300px] mt-[-2rem]" style="-webkit-text-stroke: 4px rgb(50,50,50);">3</h1>
            </div>
            <img class='relative z-2 h-full ml-28 cursor-pointer hover:scale-105 transition transition-duration-300' src='<?php echo $top5filmy[2]["cover_url"] ?>'>
        </a>

        <a href='' class='w-[12%] relative h-full pl-2'>
            <div class="absolute z-1 h-full grid place-items-center">
                <h1 class="z-1 font-extrabold text-neutral-800 text-[300px] mt-[-2rem]" style="-webkit-text-stroke: 4px rgb(50,50,50);">4</h1>
            </div>
            <img class='relative z-2 h-full ml-[7.5rem] cursor-pointer hover:scale-105 transition transition-duration-300' src='<?php echo $top5filmy[3]["cover_url"] ?>'>
        </a>

        <a href='' class='w-[12%] relative h-full pl-7'>
            <div class="absolute z-1 h-full grid place-items-center">
                <h1 class="z-1 font-extrabold text-neutral-800 text-[300px] mt-[-2rem]" style="-webkit-text-stroke: 4px rgb(50,50,50);">5</h1>
            </div>
            <img class='relative z-2 h-full ml-28 cursor-pointer hover:scale-105 transition transition-duration-300' src='<?php echo $top5filmy[4]["cover_url"] ?>'>
        </a>
    </div>
</section>

<script>
    Movies.GetByName('mimoni').then(links => {
        console.log(links);
    }).catch(error => {
        console.error('Error:', error);
    });
</script>
<script>
    // JavaScript to hide the div after 1 second
    setTimeout(() => {
        document.getElementById('wellcome-screen').classList.add('fade-out');
    }, 1500);

    // Optionally, you can add the hidden class after the transition ends
    document.getElementById('wellcome-screen').addEventListener('transitionend', () => {
        document.getElementById('wellcome-screen').classList.add('hidden');
    });
</script>
</body>
</html>
