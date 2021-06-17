<?php include 'include/connessione.php'; ?>
<?php include 'include/header.php';   ?>

<?php

// inserire un record nella tabella stampi 
if(isset($_POST['newAbbina'])) {     // pulsante premuto	

	$idIsola = $_SESSION['idIsola'];
	$skStampo = $_POST['stampoXisola']; 
	
		$gg = $_POST['giorno'];
		$m = $_POST['mese'];     // gen-feb-mar...
		$aa = $_POST['anno'];	 // anno in 4 cifre																			  
		if(!is_numeric($m)) {  // se $m non numerica, la trasformo numerica
			for($i=1; $i<13; $i++){if($mesi[$i] == $m) {$mm = $i;}}   // trasformo mese in numero (dic --> 12)
		} else  {
			$mm = $m;            // lascio numero del mese
		}	 																	  
				
		$data = str_replace(' ', '', "$aa - $mm - $gg");
		$data = rtrim($data); 
		$data = date('Y-m-d ', strtotime($data));   // data nel formato del database	
	
	$sql= "INSERT INTO stampipresse (skStampo, skIsola, dataStampoPressa) ".
		  "VALUES ('$skStampo', '$idIsola', '$data')";

	$aggiorna = $dbo->query($sql);
	if (!$aggiorna) {
		echo "Errore: $sql <br/> $dbo->error";
	} 
	
	// recupero nome isola
	$sql = $dbo->query("SELECT * FROM isole WHERE idIsola = $idIsola"); 
	$row = $sql->fetch(PDO::FETCH_ASSOC); 		
	$isola = $row['isola'];	
}

?>	


<?php // trovo isola chiamato dalla pagina base

if(isset($_GET['abbina'])) { 
	
	$idIsola = $_GET['abbina'];
	
	// recupero per abbinare lo stampo nella pagina index
	$_SESSION['idIsola'] = $idIsola;
	
	// recupero nome isola
	$sql = $dbo->query("SELECT * FROM isole WHERE idIsola = $idIsola"); 
	$row = $sql->fetch(PDO::FETCH_ASSOC); 		
	$isola = $row['isola'];	
	
	// ultimo stampo per isola in ordine di data
	$sql = "SELECT skStampo FROM stampipresse 
	WHERE skIsola=$idIsola AND dataStampoPressa=(SELECT MAX(dataStampoPressa) FROM stampipresse WHERE skIsola=$idIsola) ORDER BY idStampoPressa";
	$riga = $dbo->query($sql);
	
	if (!$riga) {
		echo "Errore: $sql <br/> $dbo->error";
	} else {		
		while($row = $riga->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			// a parità di data sarà quello con idStampoPressa + alto
			$skStampo = $row['skStampo'];			
		}
	}		
		/*
    echo '<pre>'; 
	print_r ($riga);
	echo '</pre>';
	*/
}	
?>

<section>	

	<nav class='fisso'>
		<a href="index.php">
			<img src='immagini/smallLogo.png' class='btn ombra bottone' title = 'indietro' alt='home'>
		</a>
	</nav>	

	<article class='frmStampo ombra'>
	
	 <form action="abbinaStampo.php" method="post">
	
	<fieldset>
	
	<legend id='legenda'class='rossetto biggest'> &nbsp; 
		 Cambio stampo &nbsp; 
	</legend>
	
					<div class='destra'>
						
						<select  class='big' name="giorno">
							<?php for($i = 1; $i < 32; $i++){?>
								<option	<?php if($i==$gg) { echo "value = $gg selected "; }	?>>
								<?php echo $i?></option>
							<?php } ?>     
						</select>
					
						<select class='big' name="mese">
							<?php for($i = 1; $i < 13; $i++){?>
								<option	<?php if($i == $mm) { echo "value = $mm selected "; }	?>>
								<?php echo $mesi[$i]?></option>
							<?php } ?>
						</select>
					
						<select class='big' name="anno">
							<?php for($i = $aa; $i >= 2015; $i--){?>
								<option	<?php if($i == $aa) { echo "value = $aa selected "; }	?>>
								<?php echo $i?></option>
							<?php } ?>     
						</select><br>
						
					
					</div><br>
					
			<div class='grid-container'>		
		
				<div class='biggest rossetto'> &nbsp; <?php echo 'isola ' . $idIsola . ' ' . $isola ?> </div>
				<span class='destra'>
					<label for='stampate'>stampate n:</label>
					<input type='text' name='stampate' disabled>
				</span>
			</div>
		
		<br>
		
		<select name='stampoXisola' id='stampoXisola'>
		<option></option>
			<?php				
				$stampi = $dbo->query("SELECT * FROM stampi ORDER BY numStampo DESC, versione DESC");
				$sel = $skStampo; 
				
				while($row = $stampi->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
					$idStampo = $row['idStampo']; 
					
					$numStampo = $row['numStampo'];					
					$impronte = $row['impronte'];							 
					
					$mod1 = nomeModello($row['mod_1']);	
					$mod2 = nomeModello($row['mod_2']);	
					$mod3 = nomeModello($row['mod_3']);	
					$mod4 = nomeModello($row['mod_4']);	

					$int1 = nomeLunghezza($row['lung_1']);	
					$int2 = nomeLunghezza($row['lung_2']);	
					$int3 = nomeLunghezza($row['lung_3']);	
					$int4 = nomeLunghezza($row['lung_4']);	
					
					$fig1 = $row['fig_1'];	
					$fig2 = $row['fig_2'];	
					$fig3 = $row['fig_3'];	
					$fig4 = $row['fig_4'];	
						

				// preparo stringa stampo e figure
				$stringaNumStampo = $numStampo . ' - ';
				$stringaStampo = stringaS($impronte, $mod1, $mod2, $mod3, $mod4, $int1, $int2, $fig1, $fig2, $fig3, $fig4);
										
					if ($idStampo == $sel)   // confronto stampo selezionato
						{ print "<option value='$idStampo' selected> $stringaNumStampo $stringaStampo </option>"; }
					else 
						{ print "<option value='$idStampo'> $stringaNumStampo $stringaStampo </option>"; }							
				}
			?>   
		</select>
		<br><br>
		
	
	<div class='grid-container'>
		<input type='submit' class='btn ok_btn ombra nascosta' id='newAbbina' name='newAbbina' value='SALVA'>
		<input type='reset' class='btn ko_btn ombra nascosta' id='annulla' value='ANNULLA'>
	</div>
	
	</fieldset>
	</form>
	
	</article>	


<article>
	
	<table class='ombra'>
	<caption class='rossetto'>STAMPI ISOLA  <?php echo $idIsola . ' ' . $isola ?> </caption>
	<tr>
		<th>data</th>
		<th>n.</th>
		<th>figure</th>
		
	</tr>

<?php
// tabella con stampi relativi a isola
	
$sql = "SELECT * FROM stampipresse WHERE skIsola = $idIsola  ORDER BY dataStampoPressa DESC";	
$tabella = $dbo->query($sql);
	
while($row = $tabella->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
	$skStampo = $row['skStampo'];	
    //$dataStampoPressa = $row['dataStampoPressa'];
		$data = strtotime($row['dataStampoPressa']);  // riconverto data per il form
			$gg = date("d", $data);
			$mm = date("m", $data);
			$aa = date("Y", $data);
	
	$sql = "SELECT * FROM stampi WHERE idStampo=$skStampo";	
	$riga = $dbo->query($sql);	
	
	while($row = $riga->fetch(PDO::FETCH_ASSOC)){  // estrae una riga

		$numStampo = $row['numStampo'];	
		$impronte = $row['impronte'];
				
		$mod1 = nomeModello($row['mod_1']);	
		$mod2 = nomeModello($row['mod_2']);	
		$mod3 = nomeModello($row['mod_3']);	
		$mod4 = nomeModello($row['mod_4']);						

		$int1 = nomeLunghezza($row['lung_1']);	
		$int2 = nomeLunghezza($row['lung_2']);	
		$int3 = nomeLunghezza($row['lung_3']);	
		$int4 = nomeLunghezza($row['lung_4']);	
			
		$fig1 = $row['fig_1'];	
		$fig2 = $row['fig_2'];	
		$fig3 = $row['fig_3'];	
		$fig4 = $row['fig_4'];			
	}
	$stringaStampo = stringaS($impronte, $mod1, $mod2, $mod3, $mod4, $int1, $int2, $fig1, $fig2, $fig3, $fig4);
	
	echo "<tr><td><nobr>$gg-$mm-$aa</nobr></td>";
	echo "<td class='destra rossetto'>$numStampo</td>";
	echo "<td class='centro'>$stringaStampo</td></tr>";
	
}

?>

</table>
	
	</article>	



</section>


<script language='javascript'>

	var stampoXisola = document.getElementById('stampoXisola');
	var newAbbina = document.getElementById('newAbbina');
	var annulla = document.getElementById('annulla');
	
	stampoXisola.addEventListener('change', function() {
		newAbbina.style.visibility = "visible";		
		annulla.style.visibility = "visible";	
	}, false);   // fine evento impronte

</script>

<?php include 'include/footer.php';      ?>
