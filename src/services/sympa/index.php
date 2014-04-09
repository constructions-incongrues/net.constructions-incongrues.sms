<?php
/**
 * SMS service "Sympa"
 * Envois un message sympa
 */

//http://www.1messagedamour.com/search/label/SMS%20d%27amour#ixzz2yKQrV2Lv
//http://lesamoureuxdumonde.blogspot.fr/2012/02/sms-tu-me-manque.html
//http://www.adoskuat.com/article-ados-7071-25_sms_qui_lui_donneront_envie_de_decrocher_son_portable.html
//
header('Content-Type: text/html; charset=utf-8');
$say = [];

$say[]= "Je t'adore";
$say[]= "Tu seras toujours dans mon coeur";
$say[]= "Je t'aime";
$say[]= "Je te kiffe trop";

$say[]= "La mer est faite pour nager, le vent pour souffler, et moi je suis fait pour t'aimer";

$say[]= "Ca me ferait plaisir de te voir";

$say[]= "Tu me manque";

$say[]= "Tu sais quoi ? je t'adore !";

$say[]= "Si tu étais à côté de moi, je ne pourrais pas me retenir de t'embrasser";

$say[]= "Ca fait peu de temps depuis qu'on se connait mais je t'aime bcp déjà";

$say[]= "Je ne cesse de penser à toi, que ce soit en dormant, en chantant sous la douche, je ne cesse de penser à toi, à tes bras, à tes lèvres. Tu me manques mon amour";

$say[]= "Quand je pense à toi mon coeur s’accélère, quand je dors je rêve de toi... je crois que je t’aime";

$say[]= "Tu me manques trop. Ce SMS n’est pas assez puissant pour exprimer à quel point tu me manques";

$say[]= "Il n'y a pas une seconde où je ne pense pas à toi";
$say[]= "je t'envoie pleins de bisous tous doux, j'ai hate de te retrouver";
$say[]= "Tu me manques, gros calins mon ange";
$say[]= "Je pense fort à toi mon pti coeur, pleins de bisous partout";


#Si un jour le soleil venait à disparaître, je n'aurai rien à craindre puisque mon seul soleil, c'est toi
#Tu doubles mes joies et tu réduits mes peines tu illumines mes journées toi et seulement toi
#Si l'amour se comptait en grain de sable, je t'aimerai comme le désert.



shuffle($say);

die( $say[0] );
