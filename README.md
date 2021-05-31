# TP 6 NoSQL REDIS 

### _Le jeu du pendu_

##### Auteurs :
- Zayati Narjess
- Bricha Fatima Zahra
- Mekedem Sara

## Comment jouer ?

- Un joueur propose un mot
- La liste des joueurs s'affiche
- Les autres joueurs peuvent proposer des lettres tour à tour
- Les joueurs peuvent proposer un mot 
- Les joueurs ont 60 secondes pour trouver le mot

## Comment lancer l'application ? 

- Lancer redis server
- Lancer wamp

## Structure de l'application

- index.php : Affichage de la page html général, c'est la page qu'on doit afficher dans l'url du navigateur
- page.php : Interractions entre php et redis
- redis-connexion.php : Contient le code php nécessaire pour la connexion à redis
- redis-functions.php : Contient les fonctions redis utilisées dans le code, il est possible ici de changer, lesle host ou le password (qui est commenté pour nous)
- /images : Contient les 10 images du pendu qui seront affichées lors du jeu
