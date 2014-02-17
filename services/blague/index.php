<?php
/**
 * SMS script
 * Blagues courtes
 */
header('Content-Type: text/html; charset=utf-8');

$list = Array();

$list[]= "Vous savez pourquoi grand mère sait faire un bon café?\nElle a que ça à foutre";

$list[]= "Qu’est ce qui a 4 jambes, 3 bras, 2 têtes, un revolver et un chapeau?\nLe cow-boy de Tchernobyl!";

$list[]= "C’est l’histoire de 2 prostituées qui se disputent";

$list[]= "Que fait JCVan Damme quand il rentre dans sa voiture ?\nIl fout le contact";

$list[]= "Qu'est ce qui est pire que de trouver un ver de terre dans une pomme ?\nUn génocide";

$list[]= "Pourquoi l'enfant a fait tomber sa glace ?\nParce qu'il s'est fait renverser par un bus";

$list[]= "Concours de sosie en chine : tous le monde a gagne";

$list[]= "C'est l'histoire d'un mec qui rentre dans un cafe : et plouf";

$list[]= "Pourquoi les femmes aiment lecher les montres ?\nPar ce qu'un tictac contient moins de 2 calories";

$list[]= "Boire au volant, c'est pas bien ! Faut boire à la bouteille";

$list[]= "Que fait un éléphant dans un orchestre ?\nDe la trompette !";


shuffle( $list );

die( $list[0] );