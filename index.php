<?php include 'include/connessione.php'; ?>
<?php include 'include/header.php';   ?>


<section>	
	
	<nav class='fisso'>
		<ul style='list-style-type:none;'>
		  <li class='btn ombra'><a href='newStampo.php'>Nuovo stampo</a></li>
		  <li class='btn ombra' onclick='changeTable()' id='isoleStampi'><a href='#tabellaIsole'>Elenco stampi</a></li>
		</ul>
	</nav >	
	
				
	<article>
	
	<table id='tabellaIsole' class='ombra'>
	<caption class='rossetto'>ISOLE E STAMPI</caption>
	<tr>
		<th>isola</th>
		<th>n.</th>
		<th>figure</th>
		<th colspan = 3></th>
	</tr>

<?php
// tabella con le isole e i relativi stampi
	
$sql = "SELECT * FROM isole ORDER BY idIsola";	
$tabella = $dbo->query($sql);
	
while($row = $tabella->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
	$id = $row['idIsola'];	
    $isola = $row['isola'];
	
	// ultimo stampo per isola in ordine di data
	$sql = "SELECT skStampo FROM stampipresse 
	WHERE skIsola=$id AND dataStampoPressa=(SELECT MAX(dataStampoPressa) FROM stampipresse WHERE skIsola=$id) ORDER BY idStampoPressa DESC";
	$riga = $dbo->query($sql);

	if (!$riga) {
		echo "Errore: $sql <br/> $dbo->error";
	} else {		
		while($row = $riga->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
			$skStampo = $row['skStampo'];
			
			$sql = "SELECT * FROM stampi WHERE idStampo=$skStampo";	
			$riga = $dbo->query($sql);	
			
			while($row = $riga->fetch(PDO::FETCH_ASSOC)){  // estrae una riga
				$idStampo = $row['idStampo'];
				$stampo = $row['numStampo'];	
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
			}
					
		}
	}
	if ($id == 21 || $id == 31) { echo "<tr class ='isole'><td colspan=6></td></tr>"; }
	echo "<tr><td class='destra'>$id $isola</td>";
	if (isset($skStampo)) {
		echo "<td class='rossetto destra'>$stampo</td>
			<td class='centro'> $mod1 $int1"; 
			 if($fig1 > 0) { echo  " fig." . $fig1 ; } 
			 if($impronte > 1) { echo " - $mod2 $int2"; }
			 if($fig2 > 0) { echo  " fig." . $fig2 ; } 
			 if($impronte > 2) { echo " - $mod3 $int3"; }
			 if($fig3>0) { echo  " fig." . $fig3 ; } 
			 if($impronte > 3) { echo " - $mod4 $int4"; }
			 if($fig4>0) { echo  " fig." . $fig4 ; } 
			 echo "</td>			 
			 ";
	} else{
		echo "<td></td><td></td>";
	}	
	
	echo "<td><a href='abbinaStampo.php?abbina={$id}'><img src='immagini/vip.png' class='icone' title='cambio stampo' 'alt='cambio stampo'></a></td>";
	
	if (isset($skStampo)) {		
		echo "
		<td><a href='misureStampo.php?misure={$idStampo}'><img src='immagini/elencoM.png' class='icone' title='scheda stampo' 'alt='scheda stampo'></a></td>
		<td><a href='interventoStampo.php?intervento={$idStampo} & isola={$id}'><img src='immagini/martello.png' class='icone' title='intervento stampo' 'alt='intervento stampo'></a></td>";
	} else {
		echo "<td></td><td></td>";
	}	
	
	echo "</tr>";
	
	unset($skStampo);
}

?>

</table>
	
	</article>		

<div id='tabellaStampi' style='display: none'>
	<?php include 'include/tabellaStampi.php';      ?>	
</div>  

</section>


<script language='javascript'>

	function changeTable() {
		if (isoleStampi.innerHTML != 'Elenco isole') {		
			isoleStampi.innerHTML = 'Elenco isole';
			tabellaIsole.style.display = 'none';
			tabellaStampi.style.display = 'block';
		} else {
			isoleStampi.innerHTML = 'Elenco stampi';
			tabellaIsole.style.display = 'block';
			tabellaStampi.style.display = 'none';
		}
	}
	var isoleStampi = document.getElementById('isoleStampi');
	var tabellaIsole = document.getElementById('tabellaIsole');
	var tabellaStampi = document.getElementById('tabellaStampi');
	
</script>

<?php include 'include/footer.php';      ?>
