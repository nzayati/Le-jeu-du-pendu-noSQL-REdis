<?php

require "redis-functions.php";
require "redis-connexion.php";

//instanciation variables
$chosenWord = "";
$generalMessage = "";
$allowWordPick = "true";

//-------- On crée 4 joueurs --------
$nbPlayers = 4;
//Le jeu est-il lancé pour la première fois ?
if (!isset($_SESSION['isFirstGame'])) {

    //Si oui, on créée les players

    $redis->HMSET("player1", array(
        "name" => "Sara Mekedem",
        "points" => 0
    ));

    $redis->HMSET("player2", array(
        "name" => "Narjess Zayati",
        "points" => 0
    ));

    $redis->HMSET("player3", array(
        "name" => "Fatima Zahra Bricha",
        "points" => 0
    ));

    $redis->HMSET("player4", array(
        "name" => "Vincent Poupet",
        "points" => 0
    ));
    $redis->set('nbAttempts', "Le jeu n'a pas commencé");
    //on déclare un joueur au hasard parmi les joueurs pour choisir un mot
    $_SESSION['wordPicker'] = rand(1, $nbPlayers);
    if ($_SESSION['wordPicker'] != 1) {
        $_SESSION['letterPicker'] = 1;
    } else {
        $_SESSION['letterPicker'] = 2;
    }

    //On réinitialise les points des joueurs à 0
    for ($i = 1; $i <= $nbPlayers; $i++) {
        $redis->set("points:" . $i, 0);
    }

    $_SESSION['isFirstGame'] = false;

    $generalMessage = "";
    $chosenWord = "";

    $allowWordPick = "true";
}
//Si on vient de rentrer un mot
if (isset($_POST['submittedWord'])) {
    //on réinitialise les lettres proposees
    $letters = $redis->sMembers('letters');
    $lenSet = $redis->sCard('letters');
    for ($i = 0; $i < $lenSet; $i++) {
        $redis->sRem('letters', $letters[$i]);
    }
    //si le mot n'est pas une chaine de caractere
    if (!ctype_alpha($_POST['submittedWord']) ||$_POST['submittedWord']=="" ) {
        $generalMessage=("Le mot n'est pas valide, réessayez");
        
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

if (isset($_POST['submittedLetter'])) {

    if (!isLetter($_POST['submittedLetter'])) {
        $chosenWord = getDisplay(changeLetters(".", $redis));
        echo '<br/>';
        $generalMessage = "Ceci n'est pas une lettre";
        $allowWordPick = "false";
    } else {

        $redis->set('newLetter', strtolower($_POST['submittedLetter']));
        $letterVar = $redis->get('newLetter');


        //on s'assure que le TTL est encore bon 
        if ($redis->TTL('WordToFind') > 0) {

            //on vérifie que la lettre n'a pas déjà été proposée
            if (!isLetterInSet($letterVar, $redis) == false) {
                //On decremente le nb dessais uniquemnt si la lettre n'est pas dans le mot
                if(!isLetterInWord($letterVar, $redis)){
                    $redis->decrby('nbAttempts', 1);
                }
                $nbAttempts = $redis->get('nbAttempts');
                
                if ($nbAttempts == 0) {
                    //Si il n'y a plus d'essais on attribue 10pts au wordPicker
                    $redis->incrBy('points:' . $_SESSION['wordPicker'], 10);

                    $_SESSION['wordPicker']++;
                    if ($_SESSION['wordPicker'] > $nbPlayers) {
                        $_SESSION['wordPicker'] = 1;
                    }
                    $redis->set('nbAttempts', "Le jeu n'a pas commencé");
                    $generalMessage= "Perdu ! Vous avez utilisé vos 10 essais !";
                    $allowWordPick = "true";
                } else {
                    //on ajoute la lettre à la liste redis des lettres proposées
                    $redis->sAdd('letters', $letterVar); //de type Set           
                    if (isLetterInWord($letterVar, $redis)) {
                        
                        //on remplace la lettre aux bons endroits
                        $chosenWord = getDisplay(changeLetters($letterVar, $redis));
                        if(isAllLettersDiscovered($chosenWord, $redis)){
                            
                            //On peut afficher le mot
                            $chosenWord = getDisplay($redis->get('WordToFind'));
                            $generalMessage=("Vous avez trouvé le mot avec ses lettres, bravo !");
                            $allowWordPick = "true";
                
                            //On change le joueur qui propose
                            $_SESSION['wordPicker']++;
                            if ($_SESSION['wordPicker'] > $nbPlayers) {
                                $_SESSION['wordPicker'] = 1;
                            }
                        }else{
                            //on remplace la lettre aux bons endroits
                            $chosenWord = getDisplay(changeLetters($letterVar, $redis));
                            $allowWordPick = "false";
                        }
                    } else {
                        $chosenWord = getDisplay(changeLetters(".", $redis));
                        $generalMessage = "la lettre n'est pas dans le mot";
                        $allowWordPick = "false";
                    }

                    $_SESSION['letterPicker']++;
                    if ($_SESSION['letterPicker'] == $_SESSION['wordPicker']) {
                        $_SESSION['letterPicker']++;
                    }
                    if ($_SESSION['letterPicker'] > $nbPlayers) {
                        if ($_SESSION['wordPicker'] != 1) {
                            $_SESSION['letterPicker'] = 1;
                        } else {
                            $_SESSION['letterPicker'] = 2;
                        }
                    }
                }
            } else {
                $chosenWord = getDisplay(changeLetters(".", $redis));
                $generalMessage = "Cette lettre a déjà été proposée";
                $allowWordPick = "false";
            }
        } 
        else {
            $generalMessage ="Le temps est écoulé !";
            //Si le ttl est ecoulé on attribue 10pts au wordPicker
            $redis->incrBy('points:' . $_SESSION['wordPicker'], 10);

            //On change le joueur qui va proposer le mot
            $_SESSION['wordPicker']++;
            if ($_SESSION['wordPicker'] > $nbPlayers) {
                $_SESSION['wordPicker'] = 1;
            }
            $redis->set('nbAttempts', "Le jeu n'a pas commencé");
            $allowWordPick = "true";
        }
    }
}

if (isset($_POST['foundWord'])) {
    $redis->set('foundWord', strtolower($_POST['foundWord']));
    $wordValue = $redis->get('foundWord');

    //on s'assure que le TTL est encore bon 
    if ($redis->TTL('WordToFind') > 0) {
        //On décrémente le nombres d'essais restants

        $redis->decrby('nbAttempts', 1);
        $nbAttempts = $redis->get('nbAttempts');
        
        if ($nbAttempts == 0) {
            //Si il ne reste plus d'essais, on ajoute dix points au joueur qui a proposé le mot
            $redis->incrBy('points:' . $_SESSION['wordPicker'], 10);

            $_SESSION['wordPicker']++;
            if ($_SESSION['wordPicker'] > $nbPlayers) {
                $_SESSION['wordPicker'] = 1;
            }
            $redis->set('nbAttempts', "Le jeu n'a pas commencé");
            $allowWordPick = "true";
        }

        //on ajoute la lettre dans le set des lettres testées
        $redis->sAdd('letters', $wordValue); //de type Set           
        if (strcmp($wordValue, $redis->get('WordToFind')) == 0) {
            //on affiche le mot en entier
            $chosenWord = getDisplay($redis->get('WordToFind'));
            $generalMessage=("Vous avez trouvé le mot !");
            $allowWordPick = "true";

            //On change de joueur qui va proposer le mot, on prend le joueur suivant
            $_SESSION['wordPicker']++;
            if ($_SESSION['wordPicker'] > $nbPlayers) {
                $_SESSION['wordPicker'] = 1;
            }
        } else {
            $chosenWord = getDisplay(changeLetters(".", $redis));
            $generalMessage = "Ce n'est pas le bon mot";
            $allowWordPick = "false";
        }

        $_SESSION['letterPicker']++;
        //le joueur proposant le mot ne peut pas proposer de lettres, on le saute donc
        if ($_SESSION['letterPicker'] == $_SESSION['wordPicker']) {
            $_SESSION['letterPicker']++;
        }
        if ($_SESSION['letterPicker'] > $nbPlayers) {
            if ($_SESSION['wordPicker'] != 1) {
                $_SESSION['letterPicker'] = 1;
            } else {
                $_SESSION['letterPicker'] = 2;
            }
        }
    } else {
        $generalMessage=("Le temps est écoulé !");
        //On ajoute dix points au joueur qui a proposé le mot
        $redis->incrBy('points:' . $_SESSION['wordPicker'], 10);

        //On change de joueur qui va proposer le mot, on prend le joueur suivant
        $_SESSION['wordPicker']++;
        if ($_SESSION['wordPicker'] > $nbPlayers) {
            $_SESSION['wordPicker'] = 1;
        }
        $redis->set('nbAttempts', "Le jeu n'a pas commencé");
        $allowWordPick = "true";
    }
}
?>