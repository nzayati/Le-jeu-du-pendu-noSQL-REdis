<?php
//permet de voir si la lettre proposée est dans le mot
function isLetterInWord($letter, $redis)
{
    $word = $redis->get('WordToFind');
    $len = strlen($word);
    for ($i = 0; $i < $len; $i++) {
        if (strcmp($word[$i], $letter) == 0) {
            return true;
        }
    }
    return false;
}

//permet uniquement d'afficher des lettre // rejette tous les caract != de lettres 
function isLetter($character)
{
    if (preg_match("/[a-zA-Z]/", $character) && strlen($character) == 1) {
        return TRUE;
    } else {
        return FALSE;
    }
}
//vérifie s'il reste des lettres à découvrir
function isAllLettersDiscovered($displayedWord, $redis)
{
    $word = "";
    for ($i = 0; $i < strlen($displayedWord); $i++) {
        $word .= ($displayedWord[$i]);
    }
    //si $word==notre mot
    if (strcmp($word, $redis->get('WordToFind')) == 0) {
        return true;
    }

    return false;
}

//vérifie si la lettre est déjà stockée/proposée  
function isLetterInSet($newLetter, $redis)
{
    if ($redis->sismember('letters', $newLetter)) {
        return false;
    } else {
        return true;
    }
}


//affiche le mot mis à jour avec la bonne lettre trouvée
function changeLetters($newLetter, $redis)
{

    $wordToFind = $redis->get('WordToFind');
    $longueurMot = strlen($wordToFind);

    $wordShown = $redis->get('wordShown');


    for ($i = 0; $i < $longueurMot; $i++) {
        if ((strcmp($wordToFind[$i], $newLetter)) == 0) {
            $wordShown[$i] = $newLetter;
        }
    }
    $redis->set('wordShown', $wordShown);

    return $wordShown;
}


//met des espaces entre les lettres

function getDisplay($wordShown)
{
    $word = "";
    for ($i = 0; $i < strlen($wordShown); $i++) {

        $word[$i] = $wordShown[$i] . " ";
    }
    return $word;
}

//incrémente le wordPicker tant qu'il n'existe pas
function changeWordPicker($redis)
{
    $nbPlayers = $redis->get('nbPlayers');

    do {
        $redis->incr('wordPicker');
        $wordPicker = $redis->get('wordPicker');
        $playerSelected = $redis->HGET("player" . $wordPicker . "", "name");
        if ($wordPicker > $nbPlayers) {
            $redis->set('wordPicker', 1);
            $wordPicker = $redis->get('wordPicker');
        }
    } while ($playerSelected == "");
}
//incremente le letterPicker tant qu'il n'existe pas
function changeLetterPicker($redis)
{
    $nbPlayers = $redis->get('nbPlayers');

    $wordPicker = $redis->get('wordPicker');
    do {
        $redis->incr('letterPicker');
        $letterPicker = $redis->get('letterPicker');
        $letterPickerName = $redis->HGET("player" . $letterPicker . "", "name");
        if ($letterPicker == $wordPicker) {
            $redis->incr('letterPicker');
        }
        if ($letterPicker > $nbPlayers) {
            if ($wordPicker != 1) {
                $redis->set('letterPicker', 1);
            } else {
                $redis->set('letterPicker', 2);
            }
        }
    } while ($letterPickerName == "");
}
