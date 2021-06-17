<?php include 'include/connessione.php'; ?>
<?php include 'include/header.php';   ?>		


<?php 

// trovo stampo chiamato dalla pagina base

if(isset($_GET['misure']) || isset($_POST['aggiungiMisure'])) { 
	
	if(isset($_GET['misure'])) {	
		$idStampo = $_GET['misure']; 
		$_SESSION['idStampo'] = $idStampo;
	} else {
		$idStampo = $_SESSION['idStampo']; 
	}
		
	// scarico le informazioni
	$sql = $dbo->query("SELECT * FROM stampi WHERE idStampo = $idStampo"); 
	$row = $sql->fetch(PDO::FETCH_ASSOC); 		
	
	$numStampo = $row['numStampo'];	
	$versione = $row['versione'];
	$impronte = $row['impronte'];							 
	
	$mod1 = nomeModello($row['mod_1']);	
	$mod2 = nomeModello($row['mod_2']);	
	$mod3 = nomeModello($row['mod_3']);	
	$mod4 = nomeModello($row['mod_4']);	

		// info per colata e spinotto testata
		if ($impronte == 1) {
			$colata_1 = 0;
			$spin_1 = 0;
			$colata_2 = 0;
			$spin_2 = 0;
		} else {	
			$interasse = $row['lung_1'];	
			$sql = $dbo->query("SELECT zColata, quoteSpinotti FROM lunghezze WHERE idLunghezza = $interasse"); 
			$res = $sql->fetch(PDO::FETCH_ASSOC); 
			$colata_1 = $res['zColata'];
			$spin_1 = $res['quoteSpinotti'];
			
			if ($impronte == 2) {
				$interasse = $row['lung_2'];	
			}		
			$sql = $dbo->query("SELECT zColata, quoteSpinotti FROM lunghezze WHERE idLunghezza = $interasse"); 
			$res = $sql->fetch(PDO::FETCH_ASSOC); 
			$colata_2 = $res['zColata'];
			$spin_2 = $res['quoteSpinotti'];
		}
		
	$int1 = nomeLunghezza($row['lung_1'], $mod1);	
	$int2 = nomeLunghezza($row['lung_2'], $mod2);	
	$int3 = nomeLunghezza($row['lung_3'], $mod3);	
	$int4 = nomeLunghezza($row['lung_4'], $mod4);	

	$fig1 = $row['fig_1'];	
	$fig2 = $row['fig_2'];	
	$fig3 = $row['fig_3'];	
	$fig4 = $row['fig_4'];	
	
	// preparo stringa stampo e figure
	$stringaNumStampo = 'stampo n. ' . $numStampo . ' ';
	// stringa di tutte le figure (non la uso)
	$stringaStampo = stringaS($impronte, $mod1, $mod2, $mod3, $mod4, $int1, $int2, $fig1, $fig2, $fig3, $fig4);
	// ma uso stringhe figura singola
	$stringaFig1 = stringaF($mod1, $int1, $fig1);
	$stringaFig2 = stringaF($mod2, $int2, $fig2);
	$stringaFig3 = stringaF($mod3, $int1, $fig3);
	$stringaFig4 = stringaF($mod4, $int1, $fig4);
}	

?>


<?php

// salvo misure stampo 

if(isset($_POST['aggiungiMisure'])) {     // pulsante premuto   
    
	$pm1 = $_POST['pm1'];	
    $pm2 = $_POST['pm2'];	
	$pm3 = $_POST['pm3'];	
    $pm4 = $_POST['pm4'];	
	
	$pf1 = $_POST['pf1'];	
    $pf2 = $_POST['pf2'];	
	$pf3 = $_POST['pf3'];	
    $pf4 = $_POST['pf4'];	
	
	// trasformo la virgola in punto decimale 
	$portaStampo = virgolaPunto($_POST['portaStampo']);	
    $mattone = virgolaPunto($_POST['mattone']);	
	$piastraEstrazioni = virgolaPunto($_POST['piastraEstrazioni']);	
    $piedino = virgolaPunto($_POST['piedino']);
	$tavolino = virgolaPunto($_POST['tavolino']);	
	$diametroColonne = virgolaPunto($_POST['diametroColonne']);
	$lunghezzaColonne = virgolaPunto($_POST['lunghezzaColonne']);

	$operatoreMisure = $_POST['operatore'];	
	if ($operatoreMisure == '') { $operatoreMisure = 0; }
    $note = $_POST['note'];	

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
	
	$sql= "INSERT INTO misurestampo (skStampoMisure, operatoreMisure, dataMisure, pm1, pm2, pm3, pm4, pf1, pf2, pf3, pf4, ".
		"portaStampo, mattone, piastraEstrazioni, piedino, tavolino, diametroColonne, lunghezzaColonne, note   ) ".
		"VALUES ('$idStampo', '$operatoreMisure', '$data', '$pm1', '$pm2', '$pm3', '$pm4', '$pf1', '$pf2', '$pf3', '$pf4', ".
		"'$portaStampo', '$mattone', '$piastraEstrazioni', '$piedino', '$tavolino', '$diametroColonne', '$lunghezzaColonne', '$note')";

	$aggiorna = $dbo->query($sql);
	if (!$aggiorna) {
		echo "Errore: $sql <br/> $dbo->error";
	}

}

?>


<?php 

// preparo i dati con le misure standard se nuovo foglio stampi

	$pm1 = 0;	
	$pm2 = 0;	
	$pm3 = 0;	
	$pm4 = 0;	
	
	$pf1 = 0;	
	$pf2 = 0;	
	$pf3 = 0;	
	$pf4 = 0;	

	$portaStampo = 215;	
	$mattone = 145;	
	$piastraEstrazioni = 48;	
	$piedino = 8;	
	$tavolino = 67;	
	
	$diametroColonne = 45;	
	$lunghezzaColonne = 215;	
	
	$note = '';
	$operatoreMisure = '';
	
	$idStampo = $_SESSION['idStampo']; 

// leggo le misure recenti dello stampo chiamato dalla pagina base

$dati_stampi = array();
$conta = 0;
	
$sql = "SELECT * FROM misurestampo WHERE skStampoMisure = $idStampo ORDER BY dataMisure, idMisure";	
$tabella = $dbo->query($sql);

if (!$tabella) {
	echo "Errore: $sql <br/> $dbo->error";
} else {	
	while($row = $tabella->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
		
		// creo array con tutte le misure stampo 
		
		foreach($row as $col => $contenuto)
			{ $dati_stampi[$conta][$col] = $contenuto; }
		
		$id  = $row['skStampoMisure'];
		$operatoreMisure = $row['operatoreMisure'];
		$operatoreMisure = nomeOperatore($operatoreMisure);
		
			$data = strtotime($row['dataMisure']);  // riconverto data per il form
			$gg = date("d", $data);
			$mm = date("m", $data);
			$aa = date("Y", $data);

		$pm1 = $row['pm1'];	
		$pm2 = $row['pm2'];	
		$pm3 = $row['pm3'];	
		$pm4 = $row['pm4'];	
		
		$pf1 = $row['pf1'];	
		$pf2 = $row['pf2'];	
		$pf3 = $row['pf3'];	
		$pf4 = $row['pf4'];	
		
		$portaStampo = $row['portaStampo'];	
		$mattone = $row['mattone'];	
		$piastraEstrazioni = $row['piastraEstrazioni'];	
		$piedino = $row['piedino'];	
		$tavolino = $row['tavolino'];	
		
		$diametroColonne = $row['diametroColonne'];	
		$lunghezzaColonne = $row['lunghezzaColonne'];
		
		$operatoreMisure = $row['operatoreMisure'];	
		$note = $row['note'];	
		
		$conta += 1;
	}
	$stringa_stampi = json_encode($dati_stampi);
	/* echo '<pre>';
	print_r ($dati_stampi);
	echo '</pre>'; */
}

?>		

<span class='hidden' id='appImpronte'><?php echo $impronte; ?></span>
<span class='hidden' id='appStampi'><?php echo $stringa_stampi; ?></span>
<span class='hidden' id='gggg'><?php echo $gggg; ?></span>
<span class='hidden' id='mmmm'><?php echo $mmmm; ?></span>
<span class='hidden' id='aaaa'><?php echo $aaaa; ?></span>

<span class='hidden' id='colata_1'><?php echo $colata_1; ?></span>
<span class='hidden' id='spin_1'><?php echo $spin_1; ?></span>
<span class='hidden' id='colata_2'><?php echo $colata_2; ?></span>
<span class='hidden' id='spin_2'><?php echo $spin_2; ?></span>

<!-- inizio pagina -->	
	
<section class='misure'>
	
	<nav class='fissa'>
		<a href="index.php">
			<img src='immagini/smallLogo.png' class='btn ombra bottone' title = 'indietro' alt='home'>
		</a>
	</nav>	
		
	<article class='frmStampo '>
	
<!-- inizio form -->	
	  
  <form action="misureStampo.php" method="post">
	
	<fieldset id='field'>
	
	<legend id='legenda'class='rossetto biggest'> &nbsp; 
		 Manutenzione stampi &nbsp; 
	</legend>
	
		<div class='bigParent'>					
		
			<span class='parent centrato biggest'> &nbsp; <?php echo $stringaNumStampo ?> </span><br>
			<!-- span class='biggest'> &nbsp; <?php echo $stringaStampo ?> </span -->
		
		</div>
		
		<div class='bigParent'>
			<h1 class='parent centrato'> PIANO MOBILE </h1>
			<h1 class='parent centrato'> PIANO FISSO </h1>
		</div>
		
	<div class='bigParent'>	
		<div class='parent matrice bordo'>
			<div class='child'>
				<label class='biggest testoRuotato' id='lblFig1'><?php echo $stringaFig1; ?></label>
			</div>
			<div class='child' id='elem2mobile'>				
				<label class='biggest testoRuotato' id='lblFig2'><?php echo $stringaFig2; ?></label>
			</div>	
			<div class='child' id='elem3mobile'>
				<label class='biggest testoRuotato'><?php echo $stringaFig3; ?></label>
			</div>
			<div class='child' id='elem4mobile'>	
				<label class='biggest testoRuotato'><?php echo $stringaFig4; ?></label>
			</div>
			<input class='big bordo fissoSx' type='number' name='pm1' id='pm1' <?php echo 'value = ' . $pm1; ?> min=0 max=9>
			<input class='big bordo fissoSX' type='number' name='pm2' id='pm2' <?php echo 'value = ' . $pm2; ?> min=0 max=9>
			<input class='big bordo fissoDX' type='number' name='pm3' id='pm3' <?php echo 'value = ' . $pm3; ?> min=0 max=9>
			<input class='big bordo fissoDx' type='number' name='pm4' id='pm4' <?php echo 'value = ' . $pm4; ?> min=0 max=9>
		</div>
		
		
		<div class='parent matrice bordo'>
			
			<div class='child' id='elem1fisso'>
			    <label class='biggest testoRuotato'><?php echo $stringaFig4; ?></label>
			</div>
			<div class='child' id='elem2fisso'>			
				<label class='biggest testoRuotato'><?php echo $stringaFig3; ?></label>	
			</div>			
			<div class='child' id='elem3fisso'>
				<label class='biggest testoRuotato'><?php echo $stringaFig2; ?></label>
			</div>
			<div class='child'>
				<label class='biggest testoRuotato'><?php echo $stringaFig1; ?></label>
			</div>		
			
			<input class='big bordo fissoSx' type='number' name='pf1' id='pf1' <?php echo 'value = ' . $pf1; ?> min=0 max=9>
			<input class='big bordo fissoSX' type='number' name='pf2' id='pf2' <?php echo 'value = ' . $pf2; ?> min=0 max=9>
			<input class='big bordo fissoDX' type='number' name='pf3' id='pf3' <?php echo 'value = ' . $pf3; ?> min=0 max=9>	
			<input class='big bordo fissoDx' type='number' name='pf4' id='pf4' <?php echo 'value = ' . $pf4; ?> min=0 max=9>	
		</div>		

			<!--    eventuale zColata e spinotto testata separati per figura    -->

			<div class='nascosta colataSpinotto big' id='colataSpinotto'>
				<label class='c2 middle'> colata </label>
				<input class='c1' type='text' disabled='disabled' id='colataElemento1' size=2>				
				<input class='c3' type='text' disabled='disabled' id='colataElemento2' size=2>
				<label class='s2 middle'id='lblSpinotto'>spinotti testata</label>
				<input class='s1' type='text' disabled='disabled' id='spinottoTestata1' size=3>				
				<input class='s3' type='text' disabled='disabled' id='spinottoTestata2' size=3>
			</div>
		
		<!--    fine zColata e spinotto testata separati per figura    -->
		
		</div>
		<br><br>
		<div class='grid-container'>
		
		<span class='destra'>
			<label for='diametroColonne'>diametro col. carrelli:</label>			
			<input class='colonne' type='number' step='0.1' id='diametroColonne' name='diametroColonne' <?php echo 'value = ' . $diametroColonne; ?>><br>	
			<label for='lunghezzaColonne'>lunghezza col. carrelli:</label>			
			<input class='colonne'type='number' step='0.1' id='lunghezzaColonne' name='lunghezzaColonne' <?php echo 'value = ' . $lunghezzaColonne; ?>>			
		</span>
	
			<span class='destra'>
				<label for='quotaAsse'>quota asse:</label>
				<input type='text' disabled='disabled' id='quotaAsse' size=3><br>	
				<label for='rimandi'>rimandi:</label>			
				<input type='text' disabled='disabled' id='rimandi' size=3><br>		
				<label for='bussole'>bussole:</label>
				<input type='text' disabled='disabled' id='bussole' size=3><br>	
				<label for='estrattoreLamelle'>estrat. lamelle:</label>			
				<input type='text' disabled='disabled' id='estrattoreLamelle' size=3><br>		
				<label for='colata'>colata:</label>
				<input type='text' disabled='disabled' id='colata' size=3><br>					
				<label for='spinottiSuperiori' size=3>spin. superiori:</label>
				<input type='text' disabled='disabled' id='spinottiSuperiori' size=3><br>		
				<label for='spinottiTestata'>spin. testata:</label>			
				<input type='text' disabled='disabled' id='spinottiTestata' size=3><br>	
				<label for='inizioSmusso'>inizio smusso:</label>			
				<input type='text' disabled='disabled' id='inizioSmusso' size=3>				
				
			</span>
			<span class='grid-dx'>
				<textarea id='note' name='note' <?php echo 'value = ' . $note; ?> > <?php if(isset($note)){echo $note;} ?> </textarea>	
			</span>
		</div>
	
	
		<br><br>
		
	<div class='grid-container'>
	<span></span>
	    <div class='destra'>
		
			<select name='giorno' id='giorno'>
				<?php for($i = 1; $i < 32; $i++){?>
					<option	<?php if($i==$gg) { echo "value = $gg selected "; }	?>>
					<?php echo $i?></option>
				<?php } ?>     
			</select>
			
			<select name='mese' id='mese'>
				<?php for($i = 0; $i < 13; $i++){?>
					<option	<?php if($i == $mm) { echo "value = $mm selected "; }	?>>
					<?php echo $mesi[$i]?></option>
				<?php } ?>
			</select>
			
			<select name='anno' id='anno'>
				<?php for($i = $aaaa; $i >= 2015; $i--){?>
					<option	<?php if($i == $aa) { echo "value = $aa selected "; }	?>>
					<?php echo $i?></option>
				<?php } ?>     
			</select>
		
		</div>
		
	
		<div class='grid-dx'>
				
				&emsp;
				<select class='big' name="operatore" id="operatore">
				<option></option>
				<?php 
					if(isset($operatoreMisure)) { $sel = $operatoreMisure; } else { $sel = ''; }
					$operatori = $dbo->query("SELECT * FROM operatori WHERE reparto='o'");
					 while($row = $operatori->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
						$id = $row['idOperatore'];
						$operatoreMisure = $row['nome'];
						if ($id == $sel)   // confronto se operatore selezionato
							{ print "<option value='$id' selected> $operatoreMisure </option>"; }
						else 
							{ print "<option value='$id'> $operatoreMisure </option>"; } 
					}
				?>
			</select>
						
		</div>
	
	
	</div>
	<br>
	
	<div class='grid-container'>
	<span></span>
		 <input type='submit' class='btn ok_btn ombra nascosta' id='aggiungi' name='aggiungiMisure' value='SALVA'>
		 <input type='reset' class='btn ko_btn ombra nascosta' id='annulla' value='ANNULLA'>
		
	</div>
	
	<!--        disegno del portastampo piano mobile         -->	
	
			<div class='pianoMobile'>
				<span class='portaStampo scuro big'></span>
				<span class='psRuotato big'><nobr> porta stampo mobile </nobr></span>
				<span class='mattone2 chiaro'> &nbsp; mattone</span>
				<span class='mattone1 chiaro big'></span>
				<span class='tavolino scuro'></span>
				<span class='tavolinoRuotato big'>tavolino</span>
				<span class='piedino1 chiaro'> &nbsp;  p</span>
				<span class='piedino2 chiaro'></span>
				<span class='piastraEstrazioni scuro'></span>
				<span class='piastraRuotato big'><nobr> piastra estraz. </nobr></span>
				<input type='number' step='0.1' class='ps' id='portaStampo' name='portaStampo' <?php echo 'value = ' . $portaStampo; ?> >	
				<input type='number' step='0.1' class='mt' id='mattone' name='mattone' <?php echo 'value = ' . $mattone; ?> >	
				<label class='pelbl'>piastra estrazioni:</label>	
				<input type='number' step='0.1' class='pe' id='piastraEstrazioni' name='piastraEstrazioni' <?php echo 'value = ' . $piastraEstrazioni; ?> >		
				<label class='pdlbl'>piedino:</label>	
				<input type='number' step='0.1' class='pd' id='piedino' name='piedino' <?php echo 'value = ' . $piedino; ?> >	
				<input type='number' step='0.1' class='tv' id='tavolino' name='tavolino' <?php echo 'value = ' . $tavolino; ?> >
			</div>
		
		<!--       fine disegno        -->		

		<span class=''>
				<textarea class='nascosta' style='height: 50px;'> prova ingombro </textarea>	
			</span>

	</fieldset>
	</form>
	
<!-- fine form -->	
<nav class='frontBack'>	
	<form>
	  <input type='button' class='puls largo' id='backback' value='<<'>
      <input type='button' class='puls' id='back' value='<'>
	  <label id='tot'></label>
      <input type='button' class='puls' id='front' value='>'>
	  <input type='button' class='puls largo' id='frontfront' value='>>'>
	</form>
</nav>
	
	</article>	

</section>
	
<?php include 'include/footer.php';      ?>
