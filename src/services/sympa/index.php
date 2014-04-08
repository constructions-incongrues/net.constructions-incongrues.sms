<?php
/**
 * SMS service "Sympa"
 * Envois un message sympa
 */

//http://www.1messagedamour.com/search/label/SMS%20d%27amour#ixzz2yKQrV2Lv
//http://lesamoureuxdumonde.blogspot.fr/2012/02/sms-tu-me-manque.html

header('Content-Type: text/html; charset=utf-8');
$say = [];

$say[]= "Je t'adore";
$say[]= "Je t'aime";
$say[]= "Je te kiffe trop";
$say[]= "Je te kiffe tro";
$say[]= "La mer est faite pour nager, le vent pour souffler, et moi je suis fait pour t'aimer";
$say[]= "Ca me ferait plaisir de te voir";
$say[]= "Tu me manque";

$say[]= "Je ne cesse de penser à toi, que ce soit en dormant, en chantant sous la douche, je ne cesse de penser à toi, à tes bras, à tes lèvres. Tu me manques mon amour";

$say[]= "Quand je pense à toi mon cœur s’accélère, quand je dors je rêve de toi… je crois que je t’aime";

$say[]= "Tu me manques trop. Ce SMS n’est pas assez puissant pour exprimer à quel point tu me manques";

#Si un jour le soleil venait à disparaître, je n'aurai rien à craindre puisque mon seul soleil, c'est toi
#Tu doubles mes joies et tu réduits mes peines tu illumines mes journées toi et seulement toi
#Si l'amour se comptait en grain de sable, je t'aimerai comme le désert.



shuffle($say);

die( $say[0] );
