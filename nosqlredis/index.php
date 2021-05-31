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

    <?php include("page.php"); ?>
    <nav class="navbar navbar-light bg-light">
        <span class="navbar-brand mb-0 h1">Le PeNdU</span>
        <span class="navbar-text">
            <?php
            $name = $redis->HGET("player" . $letterPicker . "", "name");
            $points = $redis->get("points:" . $letterPicker);
            $mainPlayer = $redis->HGET("player" . $wordPicker . "", "name");
            $pointsPlayer = $redis->get("points:" . $wordPicker);
            if ($allowWordPick == "true") {
                //on affiche ici les informations du joueur qui doit choisir le mot
                echo ("Bonjour " . $mainPlayer . " ton score est " . $pointsPlayer . " points !");
            } else {
                //on affiche ici les informations du joueur qui doit choisir la lettre
                echo ("Bonjour " . $name . " ton score est " . $points . " points !");
            }
            ?>
        </span>
    </nav>

    <div style="width:1300px; margin:0 auto;" class="container">

        <div class="row">
            <div class="col-sm-3">
                <h2>Liste des joueurs</h2>
                <ul>
                    <?php

                    //Itère sur les joueurs existants et les affiche
                    echo $nbPlayers;
                    for ($i = 1; $i <= $nbPlayers; $i++) {
                        $playerName = $redis->HGET("player" . $i . "", "name");
                        if ($playerName != "") {
                            echo ("<li class='list-group-item d-flex justify-content-between align-items-center'>");
                            echo ($playerName);
                            $value = 'deleteUser' . $i;
                            echo "<form method='post' action='index.php'><span><input type='submit' name=$value class='glyphicon glyphicon-remove-sign' value='x' ></span></form>";
                            echo ("</li>");
                        }
                    }

                    ?>


                </ul><?php if ($allowWordPick == "true") : ?>
                    <div class="form-group w-75">
                        <form method="post" action="index.php">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" class="form-control" name="submittedPlayer" placeholder="Ajouter un joueur">
                            <input type="submit" />
                        </form>
                    </div> <?php endif; ?>
            </div>
            <div class="col-sm-6">
                <h2>Mot à trouver
                    <?php

                    echo ("<br>proposé par : " . $mainPlayer . "<br>");
                    ?>
                </h2><span>
                    <?php

                    for ($i = 0; $i < strlen($chosenWord); $i++) {
                        echo ($chosenWord[$i] . " ");
                    }

                    ?></span>
                <span style="color : #FF0000;">
                    <?php echo "<br><b>" . ($generalMessage) . "</b><br>";
                    ?></span><span>
                    <img src="images/pendu_<?php
                                            $nbAttempts = $redis->get('nbAttempts');
                                            if ($nbAttempts == "Le jeu n'a pas commencé") {
                                                echo ("0");
                                            } else {
                                                echo ("$nbAttempts");
                                            }
                                            ?>.gif" alt="Image pendu correspondant à l'essai : <?php echo ($nbAttempts); ?>"></span>
            </div>
            <div class="col-sm-3">
                <h2>Propositions</h2>
                <ul>
                    <?php foreach ($redis->sMembers('letters') as $letter) {  ?>
                        <li> <?php echo ($letter) ?> </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
        </br></br>
        <div class="row">
            <div class="col-sm-6">
                <h2>Temps restant</h2>
                <span> <?php
                        $ttl = $redis->TTL('WordToFind');
                        $ttl = -2 ? 60 : $redis->TTL('WordToFind');
                        echo $ttl . " secondes"; ?> </span>
            </div>
            <div class="col-sm-6">
                <h2>Nombre d'essais restant</h2>
                <span><?php
                        if ($nbAttempts == "Le jeu n'a pas commencé") {
                            echo ($nbAttempts);
                        } else {
                            echo ($nbAttempts . " essais");
                        }
                        ?></span>
            </div>
        </div>
        </br></br>
        <?php if ($allowWordPick == "false") : ?>
            <div class="row">
                <h2><?php
                    echo ($name . " : <br>");
                    ?>
                </h2></br>
            </div>

            <div class="row">

                <div class="col-sm-6">
                    <h2>

                        Proposer une lettre</h2>
                    <span>
                        <form method="post" action="index.php">
                            <input type="text" size="3" name="submittedLetter" />
                            <input type="submit" />
                        </form>
                    </span>
                </div>

                <div class="col-sm-6">
                    <h2>Proposer un mot</h2>
                    <span>
                        <form method="post" action="index.php">
                            <input type="text" size="3" name="foundWord" />
                            <input type="submit" />
                        </form>
                    </span>
                </div>
                </br></br></br></br></br></br></br>
            </div>
        <?php else : ?>
            <div class="row">
                <h2><?php
                    echo ($mainPlayer . " : <br>");
                    ?>
                </h2></br>
                <div class="col-sm-6">
                    <h2>Proposer un mot à trouver </h2>
                    <span>
                        <form method="post" action="index.php">
                            <input type="text" name="submittedWord" />

                            <input type="submit" />
                        </form>
                    </span>
                </div>

            </div>
        <?php endif; ?>
</body>

</html>