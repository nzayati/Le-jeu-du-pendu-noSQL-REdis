<?php

require "redis-functions.php";
require "redis-connexion.php";

//instanciation variables
$chosenWord = "";
$generalMessage = "";
$allowWordPick = "true";
$letterPicker = $redis->get('letterPicker');
$wordPicker = $redis->get('wordPicker');

$nbPlayers = $redis->get('nbplayers');
//Le jeu est-il lancé pour la première fois ?
if (!isset($_SESSION['isFirstGame'])) {

    //Si oui, on créée les players

    //-------- On crée 4 joueurs --------

    $redis->set('nbplayers', 4);
    $nbPlayers = $redis->get('nbplayers');

    $redis->HMSET("player1", array(
        "name" => "Sara",
        "points" => 0
    ));

    $redis->HMSET("player2", array(
        "name" => "Narjess",
        "points" => 0
    ));

    $redis->HMSET("player3", array(
        "name" => "Fatima",
        "points" => 0
    ));

    $redis->HMSET("player4", array(
        "name" => "Lorem Ipsum",
        "points" => 0
    ));

    $redis->set('nbAttempts', "Le jeu n'a pas commencé");
    //on déclare un joueur au hasard parmi les joueurs pour choisir un mot
    do {
        $randPick = rand(1, $nbPlayers);
        $userSelected = $redis->HGET("player" . $randPick . "", "name");
    } while ($userSelected == "");
    $redis->set('wordPicker', $randPick);
    $wordPicker = $redis->get('wordPicker');
    if ($wordPicker != 1) {
        $redis->set('letterpicker', 1);
    } else {
        $redis->set('letterpicker', 2);
    }
    $letterPicker = $redis->get('letterpicker');
    //On réinitialise les points des joueurs à 0
    for ($i = 1; $i <= $nbPlayers; $i++) {
        $redis->set("points:" . $i, 0);
    }

    $_SESSION['isFirstGame'] = false;

    $generalMessage = "";
    $chosenWord = "";

    $allowWordPick = "true";
}
//Si on vient de rentrer un nouveau joueur
if (isset($_POST['submittedPlayer'])) {
    $alreadyExist = false;
    for ($i = 1; $i <= $nbPlayers; $i++) {
        $playerName = $redis->HGET("player" . $i . "", "name");
        if ($_POST['submittedPlayer'] == $playerName) {
            $generalMessage = "Le joueur existe déja";
            $alreadyExist = true;
        }
    }
    if (!$alreadyExist) {
        //On réinitialise les points des joueurs à 0
        for ($i = 1; $i <= $nbPlayers; $i++) {
            $redis->set("points:" . $i, 0);
        }
        $redis->incr("nbplayers");

        $nbPlayers = $redis->get('nbplayers');
        $redis->HMSET("player" . $nbPlayers, array(
            "name" => $_POST['submittedPlayer'],
            "points" => 0
        ));
        $redis->set('nbAttempts', "Le jeu n'a pas commencé");
        //on déclare un joueur au hasard parmi les joueurs pour choisir un motdo{
        do {
            $randPick = rand(1, $nbPlayers);
            $userSelected = $redis->HGET("player" . $randPick . "", "name");
        } while ($userSelected == "");
        $redis->set('wordPicker', $randPick);
        $wordPicker = $redis->get('wordPicker');
        if ($wordPicker != 1) {
            $redis->set('letterpicker', 1);
        } else {
            $redis->set('letterpicker', 2);
        }
        $letterpicker = $redis->get('letterpicker');
    }
}

for ($i = 1; $i <= $nbPlayers; $i++) {

    if (isset($_POST['deleteUser' . $i])) {

        $userToDelete = $redis->HGET("player" . $i . "", "name");
        $generalMessage = $userToDelete . " est parti &#x1F613;";
        $redis->hdel("player" . $i . "", "name");
        $redis->hdel("player" . $i . "", "points");
        //on déclare un joueur au hasard parmi les joueurs pour choisir un mot
        do {
            $randPick = rand(1, $nbPlayers);
            $userSelected = $redis->HGET("player" . $randPick . "", "name");
        } while ($userSelected == "");
        $redis->set('wordPicker', $randPick);
        $wordPicker = $redis->get('wordPicker');
        if ($wordPicker != 1) {
            $redis->set('letterpicker', 1);
        } else {
            $redis->set('letterpicker', 2);
        }
        $letterPicker = $redis->get('letterpicker');
    }
}

//Si on vient de rentrer un mot à trouver
if (isset($_POST['submittedWord'])) {
    //on réinitialise les lettres proposees
    $letters = $redis->sMembers('letters');
    $lenSet = $redis->sCard('letters');
    for ($i = 0; $i < $lenSet; $i++) {
        $redis->sRem('letters', $letters[$i]);
    }
    //si le mot n'est pas une chaine de caractere
    if (!ctype_alpha($_POST['submittedWord']) || $_POST['submittedWord'] == "") {
        $generalMessage = ("Le mot n'est pas valide, réessayez");
    } else {
        $redis->set('WordToFind', strtolower($_POST['submittedWord']));
        $redis->expire('WordToFind', 60);
        $thisWord = $redis->get('WordToFind');
        $lengthWord = strlen($thisWord);

        $wordShown = "";

        for ($i = 1; $i <= $lengthWord; $i++) {

            $wordShown = $wordShown . "_";
        }

        $redis->set('wordShown', $wordShown);
        $wordDisplay = $redis->get('wordShown');
        $chosenWord = getDisplay($wordDisplay);
        $redis->set('nbAttempts', 10);
        $allowWordPick = "false";
    }
}
//Si on vient de rentrer une lettre
if (isset($_POST['submittedLetter'])) {

    if (!isLetter($_POST['submittedLetter'])) {
        $chosenWord = getDisplay(changeLetters(".", $redis));
        echo '<br/>';
        $generalMessage = "Ceci n'est pas une lettre";
        $allowWordPick = "false";
    } else {
        $wordPicker = $redis->get('wordPicker');
        $letterPicker = $redis->get('letterPicker');
        $redis->set('newLetter', strtolower($_POST['submittedLetter']));
        $letterVar = $redis->get('newLetter');


        //on s'assure que le TTL est encore bon 
        if ($redis->TTL('WordToFind') > 0) {

            //on vérifie que la lettre n'a pas déjà été proposée
            if (!isLetterInSet($letterVar, $redis) == false) {
                //On decremente le nb dessais uniquemnt si la lettre n'est pas dans le mot
                if (!isLetterInWord($letterVar, $redis)) {
                    $redis->decrby('nbAttempts', 1);
                }
                $nbAttempts = $redis->get('nbAttempts');

                if ($nbAttempts == 0) {
                    //Si il n'y a plus d'essais on attribue 10pts au wordPicker
                    $redis->incrBy('points:' . $wordPicker, 10);
                    changeWordPicker($redis);
                    $redis->set('nbAttempts', "Le jeu n'a pas commencé");
                    $generalMessage = "Perdu ! Vous avez utilisé vos 10 essais !";
                    $allowWordPick = "true";
                } else {
                    //on ajoute la lettre à la liste redis des lettres proposées
                    $redis->sAdd('letters', $letterVar); //de type Set           
                    if (isLetterInWord($letterVar, $redis)) {
                        $wordPicker = $redis->get('wordPicker');
                        //on remplace la lettre aux bons endroits
                        $chosenWord = getDisplay(changeLetters($letterVar, $redis));
                        if (isAllLettersDiscovered($chosenWord, $redis)) {

                            //On peut afficher le mot
                            $chosenWord = getDisplay($redis->get('WordToFind'));
                            $generalMessage = ("Vous avez trouvé le mot avec ses lettres, bravo !");
                            //on donne 10 points à tous les joueurs sauf le wordPicker
                            for ($i = 1; $i <= $nbPlayers; $i++) {
                                if ($i != $wordPicker) {
                                    $redis->incrBy("points:" . $i, 10);
                                }
                            }
                            $allowWordPick = "true";

                            //On change le joueur qui propose
                            changeWordPicker($redis);
                            $wordPicker = $redis->get('wordPicker');
                        } else {
                            //on remplace la lettre aux bons endroits
                            $chosenWord = getDisplay(changeLetters($letterVar, $redis));
                            $allowWordPick = "false";
                        }
                    } else {
                        $chosenWord = getDisplay(changeLetters(".", $redis));
                        $generalMessage = "la lettre n'est pas dans le mot";
                        $allowWordPick = "false";
                    }
                    changeLetterPicker($redis);
                    $letterPicker = $redis->get('letterPicker');
                    $wordPicker = $redis->get('wordPicker');
                }
            } else {
                $chosenWord = getDisplay(changeLetters(".", $redis));
                $generalMessage = "Cette lettre a déjà été proposée";
                $allowWordPick = "false";
            }
        } else {
            $generalMessage = "Le temps est écoulé !";
            //Si le ttl est ecoulé on attribue 10pts au wordPicker
            $redis->incrBy('points:' . $wordPicker, 10);

            //On change le joueur qui va proposer le mot
            changeWordPicker($redis);
            $wordPicker = $redis->get('wordPicker');

            $redis->set('nbAttempts', "Le jeu n'a pas commencé");
            $allowWordPick = "true";
        }
    }
}
//Si on vient de rentrer un mot
if (isset($_POST['foundWord'])) {
    $redis->set('foundWord', strtolower($_POST['foundWord']));
    $wordValue = $redis->get('foundWord');
    $wordPicker = $redis->get('wordPicker');
    //on s'assure que le TTL est encore bon 
    if ($redis->TTL('WordToFind') > 0) {
        //On décrémente le nombres d'essais restants

        $redis->decrby('nbAttempts', 1);
        $nbAttempts = $redis->get('nbAttempts');

        if ($nbAttempts == 0) {
            //Si il ne reste plus d'essais, on ajoute dix points au wordPicker
            $redis->incrBy('points:' . $wordPicker, 10);

            changeWordPicker($redis);
            $wordPicker = $redis->get('wordPicker');

            $redis->set('nbAttempts', "Le jeu n'a pas commencé");
            $allowWordPick = "true";
        }

        //on ajoute la lettre dans le set des lettres testées
        $redis->sAdd('letters', $wordValue); //de type Set           
        if (strcmp($wordValue, $redis->get('WordToFind')) == 0) {
            //on affiche le mot en entier
            $chosenWord = getDisplay($redis->get('WordToFind'));
            $generalMessage = ("Vous avez trouvé le mot !");
            //on donne 10 points à tous les joueurs sauf le wordPicker
            $wordPicker = $redis->get('wordPicker');
            for ($i = 1; $i <= $nbPlayers; $i++) {
                if ($i != $wordPicker) {
                    $redis->incrBy("points:" . $i, 10);
                }
            }
            $allowWordPick = "true";

            //On change de joueur qui va proposer le mot, on prend le joueur suivant

            changeWordPicker($redis);
            $wordPicker = $redis->get('wordPicker');
        } else {
            $chosenWord = getDisplay(changeLetters(".", $redis));
            $generalMessage = "Ce n'est pas le bon mot";
            $allowWordPick = "false";
        }

        changeLetterPicker($redis);
        $letterPicker = $redis->get('letterPicker');
    } else {
        $generalMessage = ("Le temps est écoulé !");
        //On ajoute dix points au wordPicker
        $redis->incrBy('points:' . $wordPicker, 10);
        //On change de joueur qui va proposer le mot, on prend le joueur suivant
        changeWordPicker($redis);
        $wordPicker = $redis->get('wordPicker');

        $redis->set('nbAttempts', "Le jeu n'a pas commencé");
        $allowWordPick = "true";
    }
}
