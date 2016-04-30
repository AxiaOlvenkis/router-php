# Router-php
Un petit router en PHP, inspiré de celui de Symfony, pour simplifier vos URL

# Explication
Le fichier de routing lié est un fichier .yml, situé par défaut dans un dossier config, au même niveau que le dossier service contenant le routeur.
Le fichier se présente comme suit :

nom_de_la_route:
    controller: nom_du_controller
    action: nom_de_la_fonction_du_controller
    chemin: url_affiché_dans_le_navigateur/{parametre_eventuel}/{parametre_eventuel_2}

Note : par défaut, l'action se nomme comme dans le framework symfony, c'est à dire nom_de_la_fonction, avec le suffixe Action accolé.

Note2: par défaut, la fonction action du router appelle un ControllerFactory, qui redirige sur le bon controller et la bonne action en fonction des paramètres. A modifier selon la forme du projet. Le terme controller du fichier de route étant celui utilisé pour rediriger vers le bon controller. 


Le router est fourni avec un fichier twig, qui fournit une fonction, path(), comme dans symfony, permettant de transformer les url automatiquement à partir du nom de la route.
A appelé comme suit : {{ path(nom_de_la_route) }} ou éventuellement en cas de paramètre : {{ path(nom_de_la_route, {'id_Param':param, 'id_Param_2':param2} et ainsi de suite.

Le router fournit au controller un tableau contenant tous les paramètres avec en clé leur ID, et en valeur ... leur valeur.

En cas d'erreur sur le routing, ou de route non trouvé, que ce soit sur le router, ou sur la fonction Twig, le router renvoie une exception, à gérer. Une fonction "gestionErreur" est aussi fourni, lié à un controller Erreur.

# Liste de Modification à Apporter au Routeur
- Optimisation du code
- Optimiser la factory pour partage, pour rendre le router utilisable facilement dans tout projet.
- Envoyer une liste de paramètre a l'action du controller plutôt qu'un tableau, plus simple d'utilisation.
- Continuer de tester le router pour corriger les bugs éventuels.
