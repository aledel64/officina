<?php include 'include/connessione.php'; ?>
<?php include 'include/header.php';   ?>		

<?php

// controllo se stampo da  modificare
if(isset($_GET['edit'])) { include 'include/modStampo.php'; }	

// modifica un record nella tabella stampi 
if(isset($_POST['modifica'])) {     // pulsante premuto   
    $id = $_SESSION['idStampo'];
	
	if(isset($_POST['nImpronte'])) { $impronte = $_POST['nImpronte']; } 
	
	$mod1 = $_POST['mod1'];	
    if(isset($_POST['mod2'])) { $mod2 = $_POST['mod2']; } else { $mod2 = 0; }	
	if(isset($_POST['mod3'])) { $mod3 = $_POST['mod3']; } else { $mod3 = 0; }	
    if(isset($_POST['mod4'])) { $mod4 = $_POST['mod4']; } else { $mod4 = 0; }	
	

	if(isset($_POST['int1'])) { $int1 = $_POST['int1']; } else { $int1 = 0; }	
    if(isset($_POST['int2'])) { $int2 = $_POST['int2']; } else { $int2 = 0; }	
	if(!is_numeric($int1)) { $int1 = 0;	}
	if(!is_numeric($int2)) { $int2 = 0;	}

	$int3 = 0;	
    $int4 = 0;	
	
	$fig1 = $_POST['fig1'];	
    $fig2 = $_POST['fig2'];	
	$fig3 = $_POST['fig3'];	
    $fig4 = $_POST['fig4'];	
	
	if($impronte == 2 && $mod2 == 0) { $mod2 = $mod1; }
	if($impronte == 2 && $int2 == 0) { $int2 = $int1; }	
	if($impronte == 4 && $mod2 == 0 && $mod3 == 0 && $mod4 == 0) { $mod2 = $mod1; $mod3 = $mod1; $mod4 = $mod1; }
	if($impronte == 4) { $int2 = $int1; $int3 = $int1; $int4 = $int1; }
	
	$sql= "UPDATE stampi SET mod_1=$mod1, mod_2=$mod2, mod_3=$mod3, mod_4=$mod4, ".
			"lung_1=$int1, lung_2=$int2, lung_3=$int3, lung_4=$int4, ".
			"fig_1='$fig1', fig_2='$fig2', fig_3='$fig3', fig_4='$fig4' WHERE idStampo='$id'";
			
	$aggiorna = $dbo->query($sql);
	if (!$aggiorna) {
		echo "Errore: $sql <br/> $dbo->error";
	}
	
	// disattivo il $_POST	
unset($_POST['modifica']);
header("Location: ".$_SERVER['PHP_SELF']);
	
}

// inserire un record nella tabella stampi 
if(isset($_POST['newStampo'])) {     // pulsante premuto	
	
	// numero dello stampo (se esiste, aggiorno la versione)
	$stampo = $_POST['nStampo']; 
	
	// trovo ultima versione dello stampo selezionato
	$sql = $dbo->query("SELECT MAX(versione) AS maxV FROM stampi WHERE numStampo=$stampo"); 
	$vers = $sql->fetch(PDO::FETCH_ASSOC); 
	$vers = $vers['maxV'];

	// se non esiste $vers, significa nuovo stampo e versione 1
	if(is_numeric($vers)) {  
		$vers += 1;		
	}  else {
	    $vers = 1;
	}
	
	// se non ricevo nImpronte dal POST newStampo, lo recupero da uno stampo uguale
	if(isset($_POST['nImpronte'])) { 
		$impronte = $_POST['nImpronte']; 
	} else { 
		$sql = $dbo->query("SELECT impronte FROM stampi WHERE numStampo = $stampo AND versione = 1"); 
		$res = $sql->fetch(PDO::FETCH_ASSOC); 
		$impronte = $res['impronte']; 
	}
	

	$mod1 = $_POST['mod1'];	
    $mod2 = $_POST['mod2'];	
	$mod3 = $_POST['mod3'];	
    $mod4 = $_POST['mod4'];	
	
	$int1 = $_POST['int1'];	
    $int2 = $_POST['int2'];	
	$int3 = 0;	
    $int4 = 0;	
	
	$fig1 = $_POST['fig1'];	
    $fig2 = $_POST['fig2'];	
	$fig3 = $_POST['fig3'];	
    $fig4 = $_POST['fig4'];	
	
	if($impronte == 1) { $mod2 = 0; $mod3 = 0; $mod4 = 0; $int1 = 0; $int2 = 0; $int3 = 0; $int4 = 0;}
	if($impronte == 2) { $mod3 = 0; $mod4 = 0; }
	if($impronte == 2 && $mod2 == 0) { $mod2 = $mod1; }
	if($impronte == 2 && $int2 == 0) { $int2 = $int1; }	
	if($impronte == 4 && $mod2 == 0 && $mod3 == 0 && $mod4 == 0) { $mod2 = $mod1; $mod3 = $mod1; $mod4 = $mod1; }
	if($impronte == 4) { $int2 = $int1; $int3 = $int1; $int4 = $int1; }

	$sql= "INSERT INTO stampi (numStampo, versione, impronte, mod_1, mod_2, mod_3, mod_4, lung_1, lung_2, lung_3, lung_4, fig_1, fig_2, fig_3, fig_4) ".
		"VALUES ('$stampo', '$vers', '$impronte', '$mod1', '$mod2', '$mod3', '$mod4', '$int1', '$int2', '$int3', '$int4', '$fig1', '$fig2', '$fig3', '$fig4')";
		
	$aggiorna = $dbo->query($sql);
	if (!$aggiorna) {
		echo "Errore: $sql <br/> $dbo->error";
	} 

// disattivo il $_POST	
unset($_POST['newStampo']);
header("Location: ".$_SERVER['PHP_SELF']);

}



?>	

<!--------------           precarico array stampi - impronte             -------------->

<?php $sel = ''; ?>


<?php	
	$datiImpronte = array();
	$tab = $dbo->query("SELECT DISTINCT numStampo, impronte FROM stampi ORDER BY numStampo");
	if (!$tab) {
		echo "Errore: $sql <br/> $dbo->error";
	}	
	// preparo file json numero stampo --> numero impronte
	while($row = $tab->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
		$num = $row['numStampo']; 
		$impr = $row['impronte'];
		$datiImpronte[$num] = $impr; 
	}
	$stringa_impronte = json_encode($datiImpronte);
?> 
	
<span class='nascosta' id='appImpronte'><?php echo $stringa_impronte; ?></span>

<select  class='hidden' id='impronte1'>
	<option></option>
	<?php 
	if(isset($impronte)){ $sel = $impronte; }
	for($i = 1; $i < 5; $i++){ 
		if($i == 3) { continue; }
		if ($i == $sel)   // confronto stampo selezionato
			{ print "<option value='$i' selected> $i </option>"; }
		else 
			{ print "<option> $i </option>"; }	
	} ?>    
</select>

<select class='hidden' id='impronte2' disabled>
	<?php 
	if(isset($impronte)){ $sel = $impronte; }
	for($i = 1; $i < 5; $i++){ 
		if($i == 3) { continue; }
		if ($i == $sel)   // confronto stampo selezionato
			{ print "<option value='$i' selected> $i </option>"; }
		else 
			{ print "<option> $i </option>"; }	
	} ?>    
</select>

<!------------            precarico modelli suddivisi per numero impronte             ---------------->

<select class='hidden' id='modelli1impronte'>
<option></option>
	<?php				
		$modelli = $dbo->query("SELECT * FROM modelli");
		if(isset($impronte)){ $sel = $mod1; }
		while($row = $modelli->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$id = $row['idModello']; 
			$modello = $row['modello'];
			$impr = $row['impronte'];
			if($impr != 1) { continue; }
			if ($id == $sel)   // confronto stampo selezionato
				{ print "<option value='$id' selected> $modello </option>"; }
			else 
				{ print "<option value='$id'> $modello </option>"; }				
		}
	?>   
</select>

<select class='hidden' id='modelli2impronte_a'>
<option></option>
	<?php				
		$modelli = $dbo->query("SELECT * FROM modelli");
		if(isset($impronte)){ $sel = $mod1; }
		while($row = $modelli->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$id = $row['idModello']; 
			$modello = $row['modello'];
			$impr = $row['impronte'];
			if($impr == 1 || $impr == 4) { continue; }			
			if ($id == $sel)   // confronto stampo selezionato
				{ print "<option value='$id' selected> $modello </option>"; }
			else 
				{ print "<option value='$id'> $modello </option>"; }							
		}
	?>   
</select>


<select class='hidden' id='modelli2impronte_b'>
<option></option>
	<?php				
		$modelli = $dbo->query("SELECT * FROM modelli");
		if(isset($impronte)){ $sel = $mod2; }
		while($row = $modelli->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$id = $row['idModello']; 
			$modello = $row['modello'];
			$impr = $row['impronte'];
			if($impr == 1 || $impr == 4) { continue; }			
			if ($id == $sel)   // confronto stampo selezionato
				{ print "<option value='$id' selected> $modello </option>"; }
			else 
				{ print "<option value='$id'> $modello </option>"; }							
		}
	?>   
</select>

<select class='hidden' id='modelli4impronte_a'>
<option></option>
	<?php				
		$modelli = $dbo->query("SELECT * FROM modelli");
		if(isset($impronte)){ $sel = $mod1; }
		while($row = $modelli->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$id = $row['idModello']; 
			$modello = $row['modello'];
			$impr = $row['impronte'];
			if($impr < 4) { continue; }
			if ($id == $sel)   // confronto stampo selezionato
				{ print "<option value='$id' selected> $modello </option>"; }
			else 
				{ print "<option value='$id'> $modello </option>"; } 				
		}
	?>   
</select>

<select class='hidden' id='modelli4impronte_b'>
<option></option>
	<?php				
		$modelli = $dbo->query("SELECT * FROM modelli");
		if(isset($impronte)){ $sel = $mod2; }
		while($row = $modelli->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$id = $row['idModello']; 
			$modello = $row['modello'];
			$impr = $row['impronte'];
			if($impr < 4) { continue; }
			if ($id == $sel)   // confronto stampo selezionato
				{ print "<option value='$id' selected> $modello </option>"; }
			else 
				{ print "<option value='$id'> $modello </option>"; } 				
		}
	?>   
</select>

<select class='hidden' id='modelli4impronte_c'>
<option></option>
	<?php				
		$modelli = $dbo->query("SELECT * FROM modelli");
		if(isset($impronte)){ $sel = $mod3; }
		while($row = $modelli->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$id = $row['idModello']; 
			$modello = $row['modello'];
			$impr = $row['impronte'];
			if($impr < 4) { continue; }
			if ($id == $sel)   // confronto stampo selezionato
				{ print "<option value='$id' selected> $modello </option>"; }
			else 
				{ print "<option value='$id'> $modello </option>"; } 				
		}
	?>   
</select>

<select class='hidden' id='modelli4impronte_d'>
<option></option>
	<?php				
		$modelli = $dbo->query("SELECT * FROM modelli");
		if(isset($impronte)){ $sel = $mod4; }
		while($row = $modelli->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$id = $row['idModello']; 
			$modello = $row['modello'];
			$impr = $row['impronte'];
			if($impr < 4) { continue; }
			if ($id == $sel)   // confronto stampo selezionato
				{ print "<option value='$id' selected> $modello </option>"; }
			else 
				{ print "<option value='$id'> $modello </option>"; } 				
		}
	?>   
</select>

<!-- fine modelli e  inizio lunghezze -->

<select class='hidden' id='lunghezze2impronte_a'>
<option></option>
	<?php				
		$lunghezze = $dbo->query("SELECT * FROM lunghezze");
		if(isset($impronte)){ $sel = $int1; }
		while($row = $lunghezze->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$id = $row['idLunghezza']; 
			$lunghezza = $row['lunghezza'];
			$impr = $row['impronte'];
			if($impr == 4) { continue; }
			if ($id == $sel)   // confronto stampo selezionato
				{ print "<option value='$id' selected> $lunghezza </option>"; }
			else 
				{ print "<option value='$id'> $lunghezza </option>"; } 				
		}
	?> 
</select>

<select class='hidden' id='lunghezze2impronte_b'>
<option></option>
	<?php				
		$lunghezze = $dbo->query("SELECT * FROM lunghezze");
		if(isset($impronte)){ $sel = $int2; }
		while($row = $lunghezze->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$id = $row['idLunghezza']; 
			$lunghezza = $row['lunghezza'];
			$impr = $row['impronte'];
			if($impr == 4) { continue; }
			if ($id == $sel)   // confronto stampo selezionato
				{ print "<option value='$id' selected> $lunghezza </option>"; }
			else 
				{ print "<option value='$id'> $lunghezza </option>"; } 				
		}
	?> 
</select>

<select class='hidden' id='lunghezze4impronte'>
<option></option>
	<?php				
		$lunghezze = $dbo->query("SELECT * FROM lunghezze");
		if(isset($impronte)){ $sel = $int1; }
		while($row = $lunghezze->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$id = $row['idLunghezza']; 
			$lunghezza = $row['lunghezza'];
			$impr = $row['impronte'];
			if($impr < 4) { continue; }
			if ($id == $sel)   // confronto stampo selezionato
				{ print "<option value='$id' selected> $lunghezza </option>"; }
			else 
				{ print "<option value='$id'> $lunghezza </option>"; }
		}
	?> 
</select>
	
<!-- fine lunghezze -->	








<!-- inizio pagina -->	
	
<section>
	<nav class='fisso'>
		<a href="index.php">
			<img src='immagini/smallLogo.png' class='btn ombra bottone' title = 'indietro' alt='home'>
		</a>
	</nav>	
	

	<article class='frmStampo ombra'>
	
<!-- inizio form -->	
	  
  <form action="newStampo.php" method="post">
	<!-- $id per modifiche in php + dinamico per javascript-->
	
	<input type='hidden' id='dinamico' value ='<?php if(isset($_GET['edit'])) {echo 'modifica';} ?>'>
	
	<fieldset>
	<legend id='legenda'class='rossetto biggest'> &ensp; Nuovo stampo &ensp; </legend>
	
	<div class='head'>		
					
		<?php // trovo ultimo stampo archiviato
			$sql = $dbo->query("SELECT MAX(numStampo) AS maxStampo FROM stampi"); 
			$maxStampo = $sql->fetch(PDO::FETCH_ASSOC); 
			$maxStampo = $maxStampo['maxStampo'];
		?>
	
		<label class='destra big' for='nStampo'>stampo n:</label>
		<select <?php if(isset($_GET['edit'])){ echo "disabled"; } ?> name='nStampo' id='nStampo' required>
		<option></option>
			<?php 
			if(isset($numStampo)){ $sel = $numStampo; } 
			for($i = $maxStampo + 3; $i > 0; $i--){
				if ($i == $sel)   // confronto stampo selezionato
					{ print "<option value='$i' selected> $i </option>"; }
				else 
					{ print "<option> $i </option>"; }			
			 } ?>    
		</select>
		
		<label class='destra nascosta big' for='nImpronte' id='lbl_imp'>impronte n:</label>
		<select class='nascosta' name='nImpronte' id='nImpronte'>
		</select>
		
	</div>
	
	<div class='rossetto'>segnare le figure da sinistra a destra vista piano mobile</div><br>
	
	<div class='frm'>
	
		<!-- ---------------------- -->
	
		<span class='destra nascosta' id='lblModello'>modello:</span>
		
		<select class='nascosta' name='mod1' id='mod1' required>
		</select>
		
		<select class='nascosta' name='mod2' id='mod2'>
		</select>
		
		<select class='nascosta' name='mod3' id='mod3'>
		</select>
		
		<select class='nascosta' name='mod4' id='mod4'>
		</select>
		
		<!-- ---------------------- -->
		
		<span class='destra nascosta' id='lblLunghezza'>interasse:</span>
		
		<select class='nascosta' id="int1" name="int1">
			<option></option>
		</select>
		
		<select class='nascosta' id="int2" name="int2">
			<option></option>
		</select>		
		
		<span></span>
		<span></span>
		
		<!-- ---------------------- -->
		
		<span class='destra nascosta' id='lblFigura'>figura:</span>
		<input type="text" class='nascosta' id="fig1" name="fig1" size='5' placeholder='elemento 1' 
							<?php if(isset($fig1)){echo 'value = ' . $fig1; } ?> >		
		<input type="text" class='nascosta' id="fig2" name="fig2" size='5' placeholder='elemento 2'
							<?php if(isset($fig1)){echo 'value = ' . $fig2; } ?> >	
		<input type="text" class='nascosta' id="fig3" name="fig3" size='5' placeholder='elemento 3'
							<?php if(isset($fig1)){echo 'value = ' . $fig3; } ?> >	
		<input type="text" class='nascosta' id="fig4" name="fig4" size='5' placeholder='elemento 4'
							<?php if(isset($fig1)){echo 'value = ' . $fig4; } ?> >	
		
		<!-- ---------------------- -->
		
	</div> 
	
	<div class='head'>
	<?php
		if(isset($_GET['edit'])){    
			echo "<span></span><input type='submit' class='btn ok_btn ombra' name='modifica' value='CONFERMA'>";
		} else {		
			echo "<span></span><input type='submit' class='btn ok_btn ombra nascosta' name='newStampo' id='newStampo' value='AGGIUNGI'>";
		}
		echo "<span></span><input type='reset' class='btn ko_btn ombra nascosta' id='annulla' value='ANNULLA'>";
	?>   
	<div>
	
	</fieldset>
	</form>
	
<!-- fine form -->	
	
	</article>
	
<br>

<?php include 'include/tabellaStampi.php';      ?>

</section>
	
<?php include 'include/footer.php';      ?>
