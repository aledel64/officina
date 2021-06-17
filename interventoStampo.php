<?php include 'include/connessione.php'; ?>
<?php include 'include/header.php';   ?>

<?php 

if(isset($_GET['isola'])) {	
		$idIsola = $_GET['isola']; 
		$_SESSION['idIsola'] = $idIsola;
	} else {
		$idIsola = $_SESSION['idIsola']; 		
	} 

if(isset($_GET['intervento'])) {	
	$idStampo = $_GET['intervento']; 
	$_SESSION['idStampo'] = $idStampo;
} else {
	$idStampo = $_SESSION['idStampo']; 		
}
	
if(isset($_GET['isola']) && isset($_GET['intervento'])) {	
	$luogo = 'p'; 
}  
	
// trovo stampo chiamato dalla pagina base
// $_ GET dalla pagina principale - $_POST inviato il form

if(isset($_GET['intervento']) || isset($_POST['addIntervento'])) { 
	
	if (isset($_POST['addIntervento'])) {	
		$luogo = $_POST['luogo'];
		$datainizio = dataLunga($_POST['ggss'], $_POST['mmss'], $_POST['aass'], $_POST['hhss'], $_POST['mins']);
		$idIsola = $_SESSION['idIsola'];
		$impronte = $_POST['impronte'];
		if ($_POST['operatoreRic'] > 0) { 
			$operatoreRic = $_POST['operatoreRic'];
		} else {
			$operatoreRic = 0;
		}
		$operatore1 = $_POST['operatore1'];
		$note = $_POST['note'];
		if ($_POST['generico'] > 0) { 
			$generico = $_POST['generico'];
		} else {
			$generico = 0;
		}
		
		$sql= "INSERT INTO interventi (datainizio, dataFine, skIsola, skStampo, impronte, skOpR, skOp1, nota, luogo, tGenerico) ".
		"VALUES ('$datainizio', '$datainizio', '$idIsola', '$idStampo', '$impronte', '$operatoreRic', '$operatore1', '$note', '$luogo', '$generico')";

		$aggiorna = $dbo->query($sql);
		if (!$aggiorna) {
			echo "Errore: $sql <br/> $dbo->error";
		}
		// disattivo il $_POST	
		unset($_POST['addIntervento']);
		header("Location: ".$_SERVER['PHP_SELF']);			
	}		
	
}	

// scarico le informazioni dello stampo
$sql = $dbo->query("SELECT * FROM stampi WHERE idStampo = $idStampo"); 
$row = $sql->fetch(PDO::FETCH_ASSOC); 		

$numStampo = $row['numStampo'];	
$versione = $row['versione'];
$impronte = $row['impronte'];							 

$mod1 = nomeModello($row['mod_1']);	
$mod2 = nomeModello($row['mod_2']);	
$mod3 = nomeModello($row['mod_3']);	
$mod4 = nomeModello($row['mod_4']);			
	
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
// stringa di tutte le figure 
$stringaStampo = stringaS($impronte, $mod1, $mod2, $mod3, $mod4, $int1, $int2, $fig1, $fig2, $fig3, $fig4);
// ma uso stringhe figura singola
$stringaFig1 = stringaF($mod1, $int1, $fig1);
$stringaFig2 = stringaF($mod2, $int2, $fig2);
$stringaFig3 = stringaF($mod3, $int1, $fig3);
$stringaFig4 = stringaF($mod4, $int1, $fig4);
	
?>


<section>	

    <nav class='fisso'>
		<a href="index.php">
			<img src='immagini/smallLogo.png' class='btn ombra bottone' title = 'indietro' alt='home'>
		</a>
	</nav>	

    <article>

<!-- inizio form -->	
	  
  <form class='frmIntervento' action="interventoStampo.php" method="post">
	
	<input type='hidden' id='dinamico' value ='<?php if(isset($_GET['edit'])) {echo 'modifica';} ?>'>
	<input type='hidden' id='luogo' name ='luogo' value ='<?php if(isset($luogo)) {echo $luogo; } ?>'>
	
	<fieldset>
		<legend id='legenda' class='rossetto biggest'> &ensp; Intervento in pressa &ensp; </legend>
	
	<div class='container'>
		<label class='big rossetto'><?php if(isset($idIsola)) { echo 'isola ' . $idIsola . '&ensp;'; } ?></label>
		<span class='destra'>
			<label class='big'><?php echo $stringaNumStampo;  ?></label><br>
			<input class='hidden' type='text' name='impronte' value='<?php echo $impronte; ?>' >
			<label class='big rossetto'><?php echo $stringaStampo; ?></label>
		</span>
	</div>
	<br>
	<div class='container'>
	<span></span>
	<span class='destra'>
		<label id='lblDataInizio'>data inizio:</label>
		<select name='ggss' id='ggss'>
				<?php for($i = 1; $i < 32; $i++){?>
					<option	<?php if($i==$gg) { echo "value = $gg selected "; }	?>>
					<?php echo $i?></option>
				<?php } ?>     
			</select>
			
			<select name='mmss' id='mmss'>
				<?php for($i = 0; $i < 13; $i++){?>
					<option	<?php if($i == $mm) { echo "value = $mm selected "; }	?>>
					<?php echo $mesi[$i]?></option>
				<?php } ?>
			</select>
			
			<select name='aass' id='aass'>
				<?php for($i = $aaaa; $i >= 2015; $i--){?>
					<option	<?php if($i == $aa) { echo "value = $aa selected "; }	?>>
					<?php echo $i?></option>
				<?php } ?>     
			</select>
			
			<label>&ensp; &ensp;</label>
			
			<select name='hhss' id='hhss'>
				<?php for($i = 0; $i < 24; $i++){?>
					<option	<?php if($i == $hh) { echo "value = $hh selected "; }	?>>
					<?php echo $i?></option>
				<?php } ?>
			</select>
			
			<label>:</label>
			
			<select name='mins' id='mins'>
				<?php for($i = 0; $i < 60; $i++){?>
					<option	<?php if($i == $ii) { echo "value = $ii selected "; }	?>>
					<?php echo $i?></option>
				<?php } ?>     
			</select>
	</span>
	</div>
		
	
	<br><hr><br>
		
	<div class='container'>
		<span>
		<label>intervento: </label><br>
			<textarea id='note' name='note' class='area'> <?php if(isset($note)){echo $note;} ?> </textarea>	
		</span>
		
	<span class='destra' id='tipoIntervento1'>
		<label>tempo intervento: </label><br>
		<input type='number' step='10' name='generico' min=0><br>
	</span>	
	
		
	</div>		
			<br>
			<div class='container-operatori'>
		
		<label class='destra' id='lblOpRic' for='operatoreRic'>chiesto da: </label>
		<select class='operatore' name='operatoreRic' id='operatoreRic'>
				<option></option>
				<?php 
					if(isset($operatoreMisure)) { $sel = $operatoreMisure; } else { $sel = ''; }
					$operatori = $dbo->query("SELECT * FROM operatori WHERE reparto<>'o'");
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
			
			<label class='destra'  id='lblOp1' for='operatore1'>eseguito da: </label>
			<select class='operatore' name="operatore1" id="operatore1">
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
			
			
			
			<br>
	<div  class='container'>
	<?php
		if(isset($_GET['edit'])){    
			echo "<input type='submit' class='btn ok_btn ombra' name='modifica' value='CONFERMA'>";
		} else {		
			echo "<input type='submit' class='btn ok_btn ombra ' name='addIntervento' id='addIntervento' value='AGGIUNGI'>";
		}
		echo "<input type='reset' class='btn ko_btn ombra ' id='annulla' value='ANNULLA'>";
	?>   
	<div>
	
	</fieldset>
	</form>
	
<!-- fine form -->	

    </article>
	
	


<?php


// tabella interventi
	
$sql = "SELECT * FROM interventi 
		WHERE skIsola = $idIsola AND skStampo = $idStampo
		ORDER BY dataInizio DESC";	
$tabella = $dbo->query($sql);
$riga = 0;

	
while($row = $tabella->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
	if($riga == 0) {
	echo"
	<table id='tabellaInterventi' class='ombra'>
	<caption class='rossetto'>INTERVENTI</caption>
	<tr>
		<th>data</th>
		<th>chiede</th>
		<th>esegue</th>
		<th>tempo</th>
		<th>intervento</th>
	</tr>
	";
	$riga = 1;
	}
	// $dataInizio = $row['dataInizio'];
	$dataInizio = lungaData($row['dataInizio']);
		
	$operatoreR = nomeOperatore($row['skOpR']);
	$operatore1 = nomeOperatore($row['skOp1']);
	$tempo = $row['tGenerico'];
	$intervento = $row['nota'];
	
	echo "<tr>";
	echo"<td>$dataInizio</td>";
	echo"<td>$operatoreR</td>";
	echo"<td>$operatore1</td>";
	echo"<td>$tempo</td>";
	echo"<td>$intervento</td>";	
	echo "</tr>";	
}

echo "</table>";


?>	

</section>


<script language='javascript'>

	var n = 'pezzo_';

	var k = 'value';
    var i = 0;
    for(i = 1; i < 5; i++) {
        eval('var ' + k + i + '= ' + i + ';');
		eval('var ' + n + i + ';');
		
    }
    console.log("value1=" + value1);
    console.log("value2=" + value2);
    console.log("value3=" + value3);
    console.log("value4=" + value4);
	pezzo_1 = 3;
	console.log(pezzo_1);


	var note = document.getElementById('note');
	var lblOpRic = document.getElementById('lblOpRic');
	var operatoreRic = document.getElementById('operatoreRic');
	var lblOp1 = document.getElementById('lblOp1');
	var operatore1 = document.getElementById('operatore1');
	
	var annulla = document.getElementById('annulla');
	var addIntervento = document.getElementById('addIntervento');

	lblOpRic.style.visibility = 'hidden';
	operatoreRic.style.visibility = 'hidden';
	lblOp1.style.visibility = 'hidden';
	operatore1.style.visibility = 'hidden';
	annulla.style.visibility = 'hidden';
	addIntervento.style.visibility = 'hidden';


	/////////////////    eventi     ////////////////////////

	note.addEventListener('keyup', function() {
		lblOpRic.style.visibility = 'visible';
		operatoreRic.style.visibility = 'visible';
		lblOp1.style.visibility = 'visible';
		operatore1.style.visibility = 'visible';	
	}, false);  

	operatore1.addEventListener('change', function() {
		annulla.style.visibility = 'visible';
		addIntervento.style.visibility = 'visible';
	}, false);  	
	
	
</script>



<?php include 'include/footer.php';      ?>
