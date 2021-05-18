<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Le PeNdU</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>

<nav class="navbar navbar-light bg-light">
    <span class="navbar-brand mb-0 h1">Le PeNdU</span>
    <span class="navbar-text">
      Bonjour Joe, ton score est 10293 points !
    </span>
</nav>
<?php include("index.php"); ?>

<div class="container">

    <div class="row">
        <div class="col-sm-3">
            <h2>Liste des joueurs</h2>
            <ul>
                <?php
                foreach ($players as $p){
                    echo "<li>$p</li>";
                }
                ?>
            </ul>
        </div>
        <div class="col-sm-6">
            <h2>Mot à trouver</h2>
            <span>
                <?php 
                    echo $myWord;
                ?>
            </span>
            <span>_ &nbsp; _ &nbsp _ &nbsp E &nbsp _ &nbsp _ &nbsp _ &nbsp E &nbsp _ </span>
        </div>
        <div class="col-sm-3">
            <h2>Propositions</h2>
            <ul><?php
                foreach ($letters as $l){
                    echo "<li>$l</li>";
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <h2>Temps restant</h2>
            <span><?php echo $time; ?> secondes</span>
        </div>
        <div class="col-sm-6">
            <h2>Nombre d'essais restant</h2>
            <span>3 essais</span>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <h2>Proposer une lettre</h2>
            <form method="post">
            <span><input type="text" name="letter" size="3"/><button>Valider</button></span>
            </form>
        </div>
        <div value="my_word" class="col-sm-6">
            <h2>Proposer un mot</h2>
            <form method="post">
            <span><input type="text" name="htmlWord" size="20"/><button type="submit" value="setWord">Valider</button></span>
            </form>
        </div>
    </div>
</div>
</body>
</html>