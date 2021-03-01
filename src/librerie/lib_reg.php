<?php
// Funzione per la costruzione della lista di studenti con checkbox per confermarne
// la presenza in una classe
function build_chk_table($classe,$prom="")
{
	if($prom)
		$chkp=" AND id_stud NOT IN (SELECT fk_stud FROM ISTANZE,CLASSI WHERE fk_cl=id_cl AND anno=YEAR(CURDATE()) 
			GROUP BY fk_stud) ";
	$table="<table id='tabchk' class='table table-striped' style='width:500px'>";
	$ret=query("SELECT id_stud,noms,cogs,sesso 
		FROM STUDENTI,ISTANZE 
		WHERE id_stud=fk_stud 
		AND fk_cl=$classe $chkp 
		ORDER BY cogs,noms");
	while($row=$ret->fetch_assoc())
	{
		$table.="<tr>
			<td><input type='checkbox' id='c".$row['id_stud']."' name='pr[]' value='".$row['id_stud']."' class='chkpro' checked='true'></td>
			<td>".$row['cogs']."</td>
			<td>";
		if($row['noms'])
			$table.=$row['noms'];
		else
			$table.="<input id='n".$row['id_stud']."' type='text' name='nold[".$row['id_stud']."]' placeholder='Nome' required>";
		$table.="</td>
			<td>".strtoupper($row['sesso'])."</td>
		</tr>";
	}
	$table.="</table>";

	return $table;
}

// Funzione per la costruzione della colonna degli studenti
function col_stud()
{
	$retstud=query("SELECT id_ist,id_stud,noms,cogs,sesso 
		FROM STUDENTI,ISTANZE 
		WHERE fk_stud=id_stud 
		AND fk_cl=".$_GET['id']." 
		ORDER BY cogs,noms ASC");
	
	// Contatore per il colore delle righe
	$i=0;
	while($row=$retstud->fetch_assoc())
	{
		if($i%2==0)
  			$col="#eee";
  		else
    		$col="#f9f9f9";

		$rstud[$row['id_ist']]['strow']="<tr id='tr".$row['id_ist']."' class='dat tdr'>
    		<td id='st".$row['id_ist']."' class='leftfix' style='width:auto;background-color:$col'>
        		<a href='show_stud.php?id=".$row['id_stud']."&cl=".$_GET['id']."' title=\"".$row['cogs']." ".$row['noms']." (".strtoupper($row['sesso']).")\" tabindex='-1'>".$row['cogs']." ".$row['noms'][0].".</a>
        	</td>";
		$i++;
	}
	
	return $rstud;
}

// Funzione per cancellare un'istanza e, se uno studente non ha più istanze, eliminarlo
function delete_inst($id_ist)
{
	$ret=query("SELECT COUNT(*) AS n,fk_stud 
		FROM ISTANZE 
		WHERE fk_stud=(SELECT fk_stud FROM ISTANZE WHERE id_ist=$id_ist)");
	$c=$ret->fetch_assoc();
	query("DELETE FROM ISTANZE WHERE id_ist=$id_ist");
	if($c['n']==1)
		query("DELETE FROM STUDENTI WHERE id_stud=".$c['fk_stud']);
	
	return;
}

// Funzione per ricavare le righe di medie e mediane per i test
function get_am_prc($idtest,$vals,$color)
{
	// Medie e mediane dei test
	$c=0;
	foreach($idtest as $id)
	{
		$ret['avg'][$id]['val']=arr_avg(array_column($vals,$id),5);

		$i=intval($ret['avg'][$id]['val']);
		while(!$color[$i])
			$i++;
		$ret['avg'][$id]['color']=$color[$i];

		$ret['tavg']['val']=($ret['tavg']['val']*$c+$ret['avg'][$id]['val'])/++$c;

		$ret['med'][$id]['val']=arr_med(array_column($vals,$id),5);
		
		$i=intval($ret['med'][$id]['val']);
		while(!$color[$i])
			$i++;
		$ret['med'][$id]['color']=$color[$i];
	}

	// Medie e mediane degli studenti
	foreach($vals as $ids => $arr)
	{
		$ret['savg'][$ids]['val']=arr_avg($arr,5);
		$i=intval($ret['savg'][$ids]['val']);
		while(!$color[$i])
			$i++;
		$ret['savg'][$ids]['color']=$color[$i];

		$ret['smed'][$ids]['val']=arr_med($arr,5);
		$i=intval($ret['smed'][$ids]['val']);
		while(!$color[$i])
			$i++;
		$ret['smed'][$ids]['color']=$color[$i];
	}
	
	// Media totale
	$ret['tavg']['val']=number_format($ret['tavg']['val'],5);
	$i=intval($ret['tavg']['val']);
	while(!$color[$i])
		$i++;
	$ret['tavg']['color']=$color[$i];

	return $ret;
}

function get_am_std($idtest,$vals,$color)
{
	// Medie e mediane dei test
	$c=0;
	foreach($idtest as $id)
	{
		$ret['avg'][$id]['val']=arr_avg(array_column($vals,$id),5);
		$i=intval($ret['avg'][$id]['val']*10);
		if($i==0)
			$ret['avg'][$id]['color']="";
		else
		{
			if($i<-20)
				$i=-20;
			while($i%5!=0 or $i>20)
				$i--;
			$ret['avg'][$id]['color']=$color[$i*0.2+4];
		}

		$ret['tavg']['val']=($ret['tavg']['val']*$c+$ret['avg'][$id]['val'])/++$c;

		$ret['med'][$id]['val']=arr_med(array_column($vals,$id),5);
		$i=intval($ret['med'][$id]['val']*10);
		if($i==0)
			$ret['avg'][$id]['color']="";
		else
		{
			if($i<-20)
				$i=-20;
			while($i%5!=0 or $i>20)
				$i--;
			$ret['med'][$id]['color']=$color[$i*0.2+4];
		}
	}

	// Medie e mediane degli studenti
	foreach($vals as $ids => $arr)
	{
		$ret['savg'][$ids]['val']=arr_avg($arr,5);
		$i=intval($ret['savg'][$ids]['val']*10);
		if($i==0)
			$ret['avg'][$id]['color']="";
		else
		{
			if($i<-20)
				$i=-20;
			while($i%5!=0 or $i>20)
				$i--;
			$ret['savg'][$ids]['color']=$color[$i*0.2+4];
		}

		$ret['smed'][$ids]['val']=arr_med($arr,5);
		$i=intval($ret['smed'][$ids]['val']*10);
		if($i==0)
			$ret['avg'][$id]['color']="";
		else
		{
			if($i<-20)
				$i=-20;
			while($i%5!=0 or $i>20)
				$i--;
			$ret['smed'][$ids]['color']=$color[$i*0.2+4];
		}
	}
	
	// Media totale
	$ret['tavg']['val']=number_format($ret['tavg']['val'],5);
	$i=intval($ret['tavg']['val']*10);
	if($i==0)
		$ret['avg'][$id]['color']="";
	else
	{
		if($i<-20)
			$i=-20;
		while($i%5!=0 or $i>20)
			$i--;
		$ret['tavg']['color']=$color[$i*0.2+4];
	}

	return $ret;
}

function get_am_vt($idtest,$rstud,$color)
{
	// Medie e mediane dei test
	$c=0;
	foreach($idtest as $id)
	{
		$ret['avg'][$id]['val']=arr_avg(array_column($rstud['val'],$id),2);
		$i=intval($ret['avg'][$id]['val']*10);
		while(!$color[$i])
			$i++;
		$ret['avg'][$id]['color']=$color[$i];

		$ret['tavg']['val']=($ret['tavg']['val']*$c+$ret['avg'][$id]['val'])/++$c;

		$ret['med'][$id]['val']=arr_med(array_column($rstud['val'],$id),2);
		$i=intval($ret['med'][$id]['val']*10);
		while(!$color[$i])
			$i++;
		$ret['med'][$id]['color']=$color[$i];
	}

	// Medie I e II quadrimestre, vengono impostate in savg e smed
	foreach($rstud['val'] as $ids => $arr)
	{
		$q1=0;
		$q2=0;
		foreach($arr as $idt => $val)
		{
			if($rstud['data'][$ids][$idt]>8)
				$ret['savg'][$ids]['val']=($ret['savg'][$ids]['val']*$q1+$val)/++$q1;	
			else
				$ret['smed'][$ids]['val']=($ret['smed'][$ids]['val']*$q2+$val)/++$q2;
		}	
	}
	foreach($ret['savg'] as $ids => $avg)
	{
		$ret['savg'][$ids]['val']=number_format($ret['savg'][$ids]['val'],2);
		$i=intval($ret['savg'][$ids]['val']*10);
		while(!$color[$i])
			$i++;
		$ret['savg'][$ids]['color']=$color[$i];
	}
	
	foreach($ret['smed'] as $ids => $avg)
	{
		$ret['smed'][$ids]['val']=number_format($ret['smed'][$ids]['val'],2);
		$i=intval($ret['smed'][$ids]['val']*10);
		while(!$color[$i])
			$i++;
		$ret['smed'][$ids]['color']=$color[$i];
	}
	
	// Media totale
	$ret['tavg']['val']=number_format($ret['tavg']['val'],2);
	$i=intval($ret['tavg']['val']*10);
	while(!$color[$i])
		$i++;
	$ret['tavg']['color']=$color[$i];

	return $ret;
}

// Funzione per leggere la tabella dei colori
function get_color_prc()
{
	$retvoti=query("SELECT * FROM VALUTAZIONI,VOTI WHERE fk_voto=id_voto AND fk_prof=".$_SESSION['id']);
	while($row=$retvoti->fetch_assoc())
		$color[$row['perc']]=$row['color'];

	return $color;
}

function get_color_std()
{
	$std=0;
	$retvoti=query("SELECT * FROM VOTI WHERE voto NOT IN(6.5,8.5,9,9.5) ORDER BY voto");
	while($row=$retvoti->fetch_assoc())
	{
		$color[$std]=$row['color'];
		$std++;
	}

	return $color;
}

function get_color_vt()
{
	$retvoti=query("SELECT * FROM VALUTAZIONI,VOTI WHERE fk_voto=id_voto AND fk_prof=".$_SESSION['id']);
	while($row=$retvoti->fetch_assoc())
		$color[$row['voto']*10]=$row['color'];

	return $color;
}

// Funzione per ottenere i valori percentili delle prove di una classe
function get_prc($color,$cond="")
{
	$retcnt=query("SELECT fk_test,pos,COUNT(*) AS n FROM PROVE,
    (
    	SELECT id_test,pos FROM PROVE,TEST,ISTANZE 
        WHERE fk_test=id_test AND fk_ist=id_ist
        AND fk_cl=".$_GET['id']." GROUP BY id_test
    ) AS p2".$cond['tabs']." WHERE id_test=fk_test".$cond['rstr']." GROUP BY fk_test");
	while($row=$retcnt->fetch_assoc())
    {
    	$count[$row['fk_test']]=$row['n'];
    	$pos[$row['fk_test']]=$row['pos'];
	}
	
	$cond2=str_replace(",ISTANZE", "", $cond['tabs']);
	
	$retprove=query("SELECT id_ist,p1.fk_test AS fk_test,p1.data AS data,COUNT(*) AS lte
	FROM PROVE AS p1,TEST,ISTANZE,(SELECT valore,fk_test FROM PROVE".$cond['tabs']." WHERE 1=1".$cond['rstr'].") AS p2".$cond2."
	WHERE p1.fk_test=id_test 
	AND IF(pos='Maggiori',p2.valore<=p1.valore,p2.valore>=p1.valore) 
	AND fk_ist=id_ist AND fk_cl=".$_GET['id']." 
	AND p1.fk_test=p2.fk_test".$cond['rstr']."
	GROUP BY fk_ist,fk_test");

	while($row=$retprove->fetch_assoc())
	{			
		$prc=number_format(($row['lte']/$count[$row['fk_test']])*100,5);
		$rstud['val'][$row['id_ist']][$row['fk_test']]=$prc;	
		$rstud['data'][$row['id_ist']][$row['fk_test']]=$row['data'];

    	$i=intval($prc);
		while(!$color[$i])
			$i++;		
   		$rstud['color'][$row['id_ist']][$row['fk_test']]=$color[$i];
    }

	return $rstud;
}

function get_std($test,$cond="")
{
	$color=get_color_std();

	foreach($test['id'] as $id)
	{
		$ret=query("SELECT AVG(valore) AS avg,STD(valore) AS std FROM PROVE".$cond['tabs']." WHERE fk_test=$id".$cond['rstr']);
		$row=$ret->fetch_assoc();
		$avg[$id]=$row['avg'];
		$std[$id]=$row['std'];
	}

	$cond['tabs']=str_replace(",ISTANZE", "", $cond['tabs']);
	
	$retprove=query("SELECT id_ist,fk_test,valore FROM PROVE,ISTANZE".$cond['tabs']." WHERE fk_ist=id_ist AND fk_cl=".$_GET['id'].$cond['rstr']);
	while($row=$retprove->fetch_assoc())
	{
		$z=($row['valore']-$avg[$row['fk_test']])/$std[$row['fk_test']];
		if($test['pos'][$row['fk_test']]=="Minori")
			$z*=-1;
		
		$rstud['val'][$row['id_ist']][$row['fk_test']]=number_format($z,5);

		if($z===0)
			$rstud['color'][$row['id_ist']][$row['fk_test']]="";
		else
		{
			$i=number_format($z,1)*10;
			if($i>20)
				$i=20;

			while($i%5!=0 or $i<-15)
				$i++;

			$rstud['color'][$row['id_ist']][$row['fk_test']]=$color[$i*0.2+3]; 
		}  		
	}
	return $rstud;
}

// Funzione per ottenere la lista di test unitamente al numero di test
function get_test()
{
	$ret=query("SELECT id_test,nometest,pos,MIN(data)
		FROM TEST,PROVE,STUDENTI,ISTANZE
		WHERE fk_cl=".$_GET['id']."
		AND fk_test=id_test 
		AND fk_ist=id_ist
		AND fk_stud=id_stud 
		GROUP BY id_test
		ORDER BY data,id_test ASC");

	while($row=$ret->fetch_assoc())
	{
		$test['id'][]=$row['id_test'];
		$test['pos'][$row['id_test']]=$row['pos'];

	  	$test['row'].="<td id='c".$row['id_test']."' class='col topfix'>".$row['nometest']."</td>";
	}
	
	return $test;
}

function get_vt($color,$cond="")
{
	$ret=query("SELECT * FROM VOTI,VALUTAZIONI WHERE fk_voto=id_voto AND fk_prof=".$_SESSION['id']);
	while($row=$ret->fetch_assoc())
		$voti[$row['perc']]=$row['voto'];

	$retcnt=query("SELECT fk_test,pos,COUNT(*) AS n FROM PROVE,
		(
			SELECT id_test,pos FROM PROVE,TEST,ISTANZE 
			WHERE fk_test=id_test AND fk_ist=id_ist
			AND fk_cl=".$_GET['id']." GROUP BY id_test
		) AS p2".$cond['tabs']." WHERE id_test=fk_test".$cond['rstr']." GROUP BY fk_test");
	while($row=$retcnt->fetch_assoc())
    {
    	$count[$row['fk_test']]=$row['n'];
    	$pos[$row['fk_test']]=$row['pos'];
	}
	
	$cond2=str_replace(",ISTANZE", "", $cond['tabs']);
	
	$retprove=query("SELECT id_ist,p1.fk_test AS fk_test,MONTH(p1.data) AS data,COUNT(*) AS lte
		FROM PROVE AS p1,TEST,ISTANZE,(SELECT valore,fk_test FROM PROVE".$cond['tabs']." WHERE 1=1".$cond['rstr'].") as p2".$cond2."
		WHERE p1.fk_test=id_test 
		AND IF(pos='Maggiori',p2.valore<=p1.valore,p2.valore>=p1.valore) 
		AND fk_ist=id_ist AND fk_cl=".$_GET['id']." 
		AND p1.fk_test=p2.fk_test".$cond['rstr']."
		GROUP BY fk_ist,fk_test");

	while($row=$retprove->fetch_assoc())
	{			
		$prc=number_format(($row['lte']/$count[$row['fk_test']])*100,5);
		
		$base=intval($prc);
		while(!$voti[$base])
			$base++;
		$rstud['val'][$row['id_ist']][$row['fk_test']]=$voti[$base];

   		$rstud['color'][$row['id_ist']][$row['fk_test']]=$color[$voti[$base]*10];
		$rstud['data'][$row['id_ist']][$row['fk_test']]=$row['data'];
	}

	return $rstud;
}

// Funzione per l'inserimento di uno studente già registrato in una classe
function insert_stud_ex($idcl,$ids,$noms="")
{
	if($noms)
    {
    	$nom=maiuscolo($noms);
    	query("UPDATE STUDENTI SET noms='$nom' WHERE id_stud=$ids");
    }

	query("INSERT INTO ISTANZE(fk_stud,fk_cl) VALUES($ids,$idcl)");
	writelog("[p] $ids");

	return;
}

// Funzione per l'inserimento di un nuovo studente in una classe, dati l'id della classe e le informazioni
function insert_stud_new($idcl,$c,$n,$s)
{
	$cog=maiuscolo($c);
    $nom=maiuscolo($n);
    
    $ret=query("INSERT INTO STUDENTI(cogs,noms,sesso) VALUES('$cog','$nom','$s')");
	$ids=$_SESSION['sql']->insert_id;
    query("INSERT INTO ISTANZE(fk_stud,fk_cl) VALUES($ids,$idcl)");

	writelog("[+st] $ids");

	return;
}

// Funzione per controllare l'appartenenza di un valore all'intervallo di accetabilità di un test.
// Viene usata la funzione avg +- 10std poiché, per la disuguaglianza di Chebychev,
// per ogni k almeno la frazione (1-1/(k^2))-esima dei dati cade nell'intervallo avg +- k*std
// k=10 => 99% dati viene accettato
function is_accettable($test,$val)
{
	$ret=query("SELECT COUNT(*) AS n,AVG(valore) AS avg,STD(valore) AS std FROM PROVE WHERE fk_test=$test");
	$row=$ret->fetch_assoc();
	if($row['n']<10)
    	return 1;
	
	$int['sup']=$row['avg']+10*$row['std'];
	$int['inf']=$row['avg']-10*$row['std'];
	
	if($val<$int['inf'] or $val>$int['sup'])
		return 0;
  	return 1;
}

// Funzione per mostrare il form di modifica della classe
function show_cl_form($cl=0,$sez="",$anno="")
{
	switch($cl)
    {
		case 0:
			$n="<option selected disabled></option>";
			break;
		case 1:
			$c1=" selected";
			break;
		case 2:
			$c2=" selected";
			break;
		case 3:
			$c3=" selected";
			break;
		case 4:
			$c4=" selected";
			break;
		case 5:
			$c5=" selected";
			break;		
	}

	if($anno)
    	$y=$anno;
	else
    {
    	$y=date('Y');
		if(date("m")<8)
			$y--;
    }

	echo "Classe: 
		<select class='form-control' id='cl' name='cl' style='width:80px' required>
    		$n
    		<option value='1'$c1>Prima</option>
    		<option value='2'$c2>Seconda</option>
    		<option value='3'$c3>Terza</option>
    		<option value='4'$c4>Quarta</option>
    		<option value='5'$c5>Quinta</option>
  		</select> 
  	Sezione: <input type='text' id='sez' name='sez'  style='width:80px' value='$sez' required>
  	Anno: <input class='anno' type='text' name='anno' id='a1' style='width:50px;text-align:right' value='$y' required>/<span id='flwa1'>".($y+1)."</span>";
	
	return;
}

?>