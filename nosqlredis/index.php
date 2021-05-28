<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Le PeNdU</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>

<body>

<?php include("page.php");?>
    <nav class="navbar navbar-light bg-light">
        <span class="navbar-brand mb-0 h1">Le PeNdU</span>
        <span class="navbar-text">
            <?php
            //on affiche ici les informations du joueur qui doit choisir la lettre
            $name = $redis->HGET("player" . $_SESSION['letterPicker'] . "", "name");
            $points = $redis->get("points:" . $_SESSION['letterPicker']);
            echo("Bonjour " . $name . " ton score est " . $points . " points !"); ?>
        </span>
    </nav>

    <div class="container">

        <div class="row">
            <div class="col-sm-3">
                <h2>Liste des joueurs</h2>
                <ul>
                    <?php

                    //Itère sur les joueurs existants et les affiche
                    for ($i = 1; $i <= $nbPlayers; $i++) {
                        echo ("<li>");
                        $playerName = $redis->HGET("player" . $i . "", "name");
                        echo ($playerName);
                        echo ("</li>");
                    }
                    ?>
                </ul>
            </div>
            <div class="col-sm-6">
                <h2>Mot à trouver
                    <?php 
                    $mainPlayer = $redis->HGET("player" . $_SESSION['wordPicker'] . "", "name");
                    echo ("<br>proposé par : ".$mainPlayer."<br>");
                    ?>
                </h2><span>
                <?php
                
                for ($i = 0; $i < strlen($chosenWord); $i++) {
                    echo ($chosenWord[$i] . " ");
                }
                
?></span>
<span>
                <?php echo "<br><b>".($generalMessage)."</b>";
                ?></span>
            </div>
            <div class="col-sm-3">
                <h2>Propositions</h2>
                <ul>
                    <?php foreach ($redis->sMembers('letters') as $letter) {  ?>
                        <li> <?php echo($letter) ?> </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <h2>Temps restant</h2>
                <span> <?php 
                $ttl=$redis->TTL('WordToFind');
                $ttl=-2 ? 60 : $redis->TTL('WordToFind');
                echo $ttl." secondes"; ?> </span>
            </div>
            <div class="col-sm-6">
                <h2>Nombre d'essais restant</h2>
                <span><?php $nbAttempts = $redis->get('nbAttempts');
                        if ($nbAttempts == "Le jeu n'a pas commencé") {
                            echo($nbAttempts);
                        } else {
                            echo($nbAttempts . " essais");
                        }
                        ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <h2>
                    <?php 
                    echo ($name." : <br>");
                    ?>
                    Proposer une lettre</h2>
                <span>
                    <form method="post" action="index.php">
                        <input type="text" size="3" name="submittedLetter" />
                        <input type="submit" <?php if ($allowSuggests == "false") {
                                                    echo ("disabled");
                                                }  ?> />
                    </form>
                </span>
            </div>

            <div class="col-sm-6">
                <h2>Proposer un mot</h2>
                <span>
                    <form method="post" action="index.php">
                        <input type="text" size="3" name="foundWord" />
                        <input type="submit" <?php if ($allowSuggests == "false") {
                                                    echo ("disabled");
                                                }  ?> />
                    </form>
                </span>
            </div>
            </br></br></br></br></br></br></br></br></br></br></br>
            <div class="col-sm-6">
                <h2>Proposer un mot à trouver </h2>
                <?php
                
                echo ("C'est au tour de " . $mainPlayer . " de proposer un mot");
                ?>
                <span>
                    <form method="post" action="index.php">
                        <input type="text" name="submittedWord" />

                        <input type="submit" <?php if ($allowWordPick == "false") {
                                                    echo ("disabled");
                                                }  ?> />
                    </form>
                </span>
            </div>
        </div>
    </div>
</body>

</html>