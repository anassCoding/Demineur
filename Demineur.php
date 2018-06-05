<?php

//fonction qui initialise un tableau 2D contenant nos cases vides et les bombes.
function init($n,$m) {
	$a = array();
	for ($i = 0; $i < $n; $i++){
		$a[$i] = array();
		for ($j = 0; $j < $m; $j++){
			if (rand(0,9) === 0) {
			   $a[$i][$j] = "B";
			} else {
			   $a[$i][$j] = "?";
			 }
		}
	}
	return $a;
}

session_start();
if (!isset($_SESSION["tab"]) || isset($_GET["reset"]))	$_SESSION["tab"] = init(4,4);

//fonction qui renvoie si une case est valide ou non (Dans les limites du tableau)
function case_valide(&$tab, $i, $j)
{
	return ($i >= 0) && ($i < count($tab)) && ($j >= 0) && ($j < count($tab[$i]));
}

//fonction qui revele le nombre de bombes adjascentes
function bombes_adj(&$land, $x, $y)
{
	$num = 0;
	for($i = -1; $i <= 1; $i++) {
		for ($j = -1; $j <= 1; $j++) {
		        /*
			   Si la case n'est pas valide ou si on est sur la case cliquée,
			   on ne fait rien, i.e. on passe directement au tour de boucle suivant.
			*/
			if (!case_valide($land, $x+$i, $y+$j) || ($i == 0 && $j  == 0)) continue;
			if ($land[$x+$i][$y+$j] === "B") $num++;
		}
	}
	return $num;
}

//fonction qui dessine le jeu
function dessine(&$land, $bombe)
{
	echo "<table>";
	for ($x = 0; $x < count($land); $x++) {
		echo "<tr>";
		for ($y = 0; $y < count($land[$x]); $y++) {
			$c = $land[$x][$y];
			if ($c === "?")
				echo "<td class='click'><input type='radio' name='case' value='$x,$y'/></td>";
			else if ($c === "B") {
				if ($bombe) echo "<td class='bombe'>B</td>";
				else echo "<td class='click show'><input type='radio' name='case' value='$x,$y'/></td>";}
			else if ($c === "0") echo "<td class='vide'></td>";
			else if ($c > 0 && $c < 9) echo "<td class='num'>$c</td>";
		}
		echo "</tr>\n";
	}
	echo "</table>";

}

//fonction qui revele le nombre de bombes adjascentes a la case cliquee si elle n'est pas vide.
function clique(&$land, $x, $y)
{
	$c = $land[$x][$y];
	if ( $c !== "?" && $c !== "B") return;
	$num = bombes_adj($land, $x, $y);
	if ($c === "?") $land[$x][$y] = "$num";
	if ($num === 0) {
	   for($i = -1; $i <= 1; $i++) {
	       for ($j = -1; $j <= 1; $j++) {
		       if (!case_valide($land, $x+$i, $y+$j) || ($i === 0 && $j === 0)) continue;
		       clique($land, $x+$i, $y+$j);
		}
	   }
	}

}

//fonction qui definit la victoire
function victoire(&$land)
{
	foreach ($land as $key1 => $line)
		foreach($line as $key2 => $val)
		if ($val === "?") return FALSE;
	return TRUE;
}

//affichage
?>
<html>
<head>
<title>Démineur</title>
<style>

td { width : 20pt;
height: 20pt;
 }
.vide { background: #ddd; }
.num  { background: #aad;
color: #333; }
.bombe { background: #b33; }
.show {
border:  1pt dashed gray; }
</style>
</head>
<body>
<form method="get" action="Demineur.php">
	<?php
	$fini = FALSE;

if (isset($_GET["case"])) {
	$coord = explode(",", $_GET["case"]);
	$x = $coord[0];
	$y = $coord[1];

	if ($_SESSION["tab"][$x][$y] === "B") {
		echo "<b>PERDU</b>";
		$fini = TRUE;
	} else {
		clique($_SESSION["tab"], $x, $y);
		if (victoire($_SESSION["tab"])) {
			echo "<b>GAGNÉ</b><br/>";
			$fini = TRUE;
		}
	}

}

dessine($_SESSION["tab"], $fini);
if (!$fini) echo '<button type="submit">Jouer</button>';
?>
</form>
<a href="Demineur.php?reset=1">Recommencer</a>
</body>
</html>
