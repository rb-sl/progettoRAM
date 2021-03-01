<?php
// Funzione per la costruzione della lista di studenti con checkbox per confermarne
// la presenza in una classe
function build_chk_table($classe, $prom = false)
{
	$table = "<table id='tabchk' class='table table-striped' style='width:500px'>";
	
	// If this is the modification due to promotion, students already promoted to other classes
	// will not be shown
	if($prom)
		$chkp = " AND id_stud NOT IN (SELECT DISTINCT(fk_stud) FROM ISTANZE JOIN CLASSI ON fk_cl=id_cl WHERE anno=YEAR(CURDATE())) ";
	
	$stud_st = prepare_stmt("SELECT id_stud, noms, cogs, sesso 
		FROM STUDENTI JOIN ISTANZE ON id_stud=fk_stud
		WHERE fk_cl=$classe 
		$chkp 
		ORDER BY cogs, noms");

	$ret = execute_stmt();
	while($row = $ret->fetch_assoc())
	{
		$table .= "<tr>
			<td>
				<input type='checkbox' id='c".$row['id_stud']."' name='pr[]' value='".$row['id_stud']."' class='chkpro' checked='true'>
			</td>
			<td>".$row['cogs']."</td>
			<td>";
		
		// TODO Legacy for when students did not have names, may remove?
		if($row['noms'])
			$table .= $row['noms'];
		else
			$table .= "<input id='n".$row['id_stud']."' type='text' name='nold[".$row['id_stud']."]' placeholder='Nome' required>";
		
		$table .= "</td>
			<td>".strtoupper($row['sesso'])."</td>
		</tr>";
	}
	$table .= "</table>";

	return $table;
}

// Function to build the students's name column (for visualization purposes)
function col_stud()
{
	$stud_st = prepare_stmt("SELECT id_ist, id_stud, noms, cogs, sesso 
	FROM STUDENTI JOIN ISTANZE ON fk_stud=id_stud
	WHERE fk_cl=? 
	ORDER BY cogs, noms ASC");
	$stud_st->bind_param("i", $_GET['id']);

	$retstud = execute_stmt($stud_st);
	
	// Counter for the rows' color
	$i = 0;
	while($row = $retstud->fetch_assoc())
	{
		if($i % 2 == 0)
  			$cl = "evenrow";
  		else
    		$cl = "oddrow";

		$rstud[$row['id_ist']]['strow'] = "<tr id='tr".$row['id_ist']."' class='dat tdr'>
    		<td id='st".$row['id_ist']."' class='leftfix $cl '>
        		<div ><a href='show_stud.php?id=".$row['id_stud']."&cl=".$_GET['id']
					."' class='resizetext' title=\"".addslashes($row['cogs']." ".$row['noms'])
					." (".strtoupper($row['sesso']).")\" tabindex='-1'>".$row['cogs']." "
					.(isset($row['noms'][0]) ? $row['noms'][0]."." : "")."</a></div>
        	</td>";
		$i++;
	}
	
	return $rstud;
}

// Funzione per ottenere la lista di test unitamente al numero di test
function get_test()
{
	$stat_st = prepare_stmt("SELECT id_test, nometest, simbolo, pos,
		MIN(data) AS data, ROUND(AVG(valore), 2) AS avg
		FROM TEST JOIN PROVE ON fk_test=id_test
		JOIN ISTANZE ON fk_ist=id_ist
		JOIN UNITA ON fk_udm=id_udm
		WHERE fk_cl=?  
		GROUP BY id_test
		ORDER BY data, id_test ASC");
	$stat_st->bind_param("i", $_GET['id']);

	$ret = execute_stmt($stat_st);
	$stat_st->close();

	return $ret;	
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

// Calculates average and median rows and columns (percentiles)
function get_avgmed_prc($idtest, $vals, $color)
{
	// Tests' averages and medians
	$sum = 0;
	foreach($idtest as $id)
	{
		// Test's average and color
		$ret['avg'][$id]['val'] = arr_avg(array_column($vals, $id), 5);

		$color_index = floor($ret['avg'][$id]['val']);
		while(!isset($color[$color_index]))
			$color_index++;
		$ret['avg'][$id]['color'] = $color[$color_index];

		// Sum for the total average
		$sum += $ret['avg'][$id]['val'];

		// Test's median and color
		$ret['med'][$id]['val'] = arr_med(array_column($vals, $id), 5);
		
		$color_index = floor($ret['med'][$id]['val']);
		while(!isset($color[$color_index]))
			$color_index++;
		$ret['med'][$id]['color'] = $color[$color_index];
	}

	// Students' averages and medians
	foreach($vals as $ids => $arr)
	{
		// Student's average and color
		$ret['savg'][$ids]['val'] = arr_avg($arr, 5);

		$color_index = floor($ret['savg'][$ids]['val']);
		while(!isset($color[$color_index]))
			$color_index++;
		$ret['savg'][$ids]['color'] = $color[$color_index];

		// Student's median and color 
		$ret['smed'][$ids]['val'] = arr_med($arr, 5);

		$color_index = floor($ret['smed'][$ids]['val']);
		while(!isset($color[$color_index]))
			$color_index++;
		$ret['smed'][$ids]['color'] = $color[$color_index];
	}
	
	// Total percentile average	and percentile
	$ret['tavg']['val'] = number_format($sum / sizeof($idtest), 5);
	$color_index = floor($ret['tavg']['val']);
	while(!isset($color[$color_index]))
		$color_index++;
	$ret['tavg']['color'] = $color[$color_index];

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
	$color_st = prepare_stmt("SELECT * FROM VALUTAZIONI JOIN VOTI ON fk_voto=id_voto WHERE fk_prof=?");
	$color_st->bind_param("i", $_SESSION['id']);

	$retvoti = execute_stmt($color_st);
	while($row = $retvoti->fetch_assoc())
		$color[$row['perc']] = $row['color'];

	$color_st->close();
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

// Function to obtain the percentiles of a class
// The structure with multiple queries is chosen as it improves greatly the
// Execution time (~0.2s) wrt bigger nested queries (~0.6s) such as
//  SELECT fk_ist, data, (
//      SELECT COUNT(*) FROM PROVE
//          WHERE fk_test=? 
//      	AND valore<=(SELECT valore FROM PROVE WHERE fk_ist=P.fk_ist AND fk_test=?)
//      ) * 100 / (SELECT COUNT(*) FROM PROVE WHERE fk_test=?) AS perc 
//  FROM PROVE P
//  WHERE fk_ist IN (SELECT id_ist FROM ISTANZE WHERE fk_cl=?)
//  AND fk_test=?;
function get_prc_all($class, $color)
{
	// TODO: IF STAT PREPARE DIFFERENT

	// Gets the class's tests and their positive values
	$ctst_st = prepare_stmt("SELECT id_test, pos FROM TEST 
		WHERE id_test IN (
			SELECT DISTINCT(fk_test) FROM PROVE JOIN ISTANZE ON fk_ist=id_ist 
			WHERE fk_cl=?
		)");
	$ctst_st->bind_param("i", $class);
	$ctst = execute_stmt($ctst_st);
	$ctst_st->close();

	$testlist = "0";
	while($row = $ctst->fetch_assoc())
	{
		$testlist .= ",".$row['id_test'];
		
		// Set to true if greater values correspond to a better performance
		$positive[$row['id_test']] = ($row['pos'] == "Maggiori");
	}

	// Gets the total count of tests done by the class
	$count_st = prepare_stmt("SELECT fk_test, COUNT(*) AS n FROM PROVE WHERE fk_test IN ($testlist) GROUP BY fk_test");
	$cnt = execute_stmt($count_st);
	$count_st->close();

	while($row = $cnt->fetch_assoc())
		$count[$row['fk_test']] = $row['n'];

	// Statement to get the values of a class
	$class_st = prepare_stmt("SELECT fk_ist, data, valore FROM PROVE JOIN ISTANZE ON fk_ist=id_ist WHERE fk_test=? AND fk_cl=?");
	$class_st->bind_param("ii", $test, $class);

	// Statements to get the percentile 
	$greater_st = prepare_stmt("SELECT COUNT(*) AS perc FROM PROVE WHERE fk_test=? AND valore<=?");
	$greater_st->bind_param("id", $test, $curval);

	$lower_st = prepare_stmt("SELECT COUNT(*) AS perc FROM PROVE WHERE fk_test=? AND valore>=?");
	$lower_st->bind_param("id", $test, $curval);

	foreach($positive as $test => $greater)
	{
		$vals = execute_stmt($class_st);
		while($val = $vals->fetch_assoc())
		{
			$curval = $val['valore'];
			$instance = $val['fk_ist'];

			// The right statement is chosen based on the better values of the test
			if($greater)
				$prc_ret = execute_stmt($greater_st);
			else
				$prc_ret = execute_stmt($lower_st);

			$p = $prc_ret->fetch_assoc();
			$perc = number_format(($p['perc']  / $count[$test]) * 100, 5);

			$rstud['val'][$instance][$test] = $perc;
			$rstud['data'][$instance][$test] = $val['data'];

			$i = floor($perc);
			while(!isset($color[$i]))
				$i++;

			$rstud['color'][$instance][$test] = $color[$i];
		}
	}

	$class_st->close();
	$greater_st->close();
	$lower_st->close();

	return $rstud;
}







function get_prc($color, $cond = "")
{
	$retcnt=query("SELECT fk_test, pos, COUNT(*) AS n FROM PROVE JOIN
    (
    	SELECT id_test, pos FROM PROVE,TEST,ISTANZE 
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

// Function to check whether a value belongs to the acceptance interval of a test.
// The function avg +- 10stddev is used as, per the Chebychev Inequality, 
// for each k at least the (1-1/(k^2))-th fraction of data falls in the interval avg +- k*std.
// With k = 10, 99% of the data is accepted
function is_accettable($test, $val)
{
	$chk_st = prepare_stmt("SELECT COUNT(*) AS n, AVG(valore) AS avg, STD(valore) AS std FROM PROVE WHERE fk_test=?");
	$chk_st->bind_param("i", $test);
	$ret = execute_stmt($chk_st);
	$chk_st->close();
	
	$row = $ret->fetch_assoc();

	// The test is not performed if less than 10 values are present in the DB
	// and the value is accepted
	if($row['n'] < 10)
    	return 1;
	
	// Construction of the interval bounds
	$int['sup'] = $row['avg'] + 10 * $row['std'];
	$int['inf'] = $row['avg'] - 10 * $row['std'];
	
	// Check and response
	if($val < $int['inf'] or $val > $int['sup'])
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