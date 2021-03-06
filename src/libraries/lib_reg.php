<?php
// Function to retrieve a class's data or show an error if it does not exist
function get_class_info($id)
{
	$class_st = prepare_stmt("SELECT * FROM CLASSI WHERE id_cl=?");
	$class_st->bind_param("i", $id);
	$ret = execute_stmt($class_st);

	if($ret->num_rows == 0)
	{
		$_SESSION['alert'] = "Errore: Classe inesistente";
		header("Location: /register/register.php");
		exit;
	}

	$class_st->close();
	return $ret->fetch_assoc();
}

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
function get_test($test)
{
	$stat_st = prepare_stmt("SELECT id_test, nometest, simbolo, pos,
		MIN(data) AS data, ROUND(AVG(valore), 2) AS avg
		FROM TEST JOIN PROVE ON fk_test=id_test
		JOIN ISTANZE ON fk_ist=id_ist
		JOIN UNITA ON fk_udm=id_udm
		WHERE fk_cl=?  
		GROUP BY id_test
		ORDER BY data, id_test ASC");
	$stat_st->bind_param("i", $test);

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

function color_from_val($val, $color, $isperc)
{
	if($val == "-")
		return "";

	if($isperc)
	{
		// Color in the percentile case
		$color_index = floor($val);

		while(!isset($color[$color_index]))
			$color_index++;

		return $color[$color_index];
	}
	else
	{
		// Color in the standard case
		$color_index = floor($val * 10);
		if($color_index == 0)
			return "";

		$color_index = max(min(floor($val * 10), 20), -20);
		return $color[ceil($color_index / 5) + 4];
	}
}

// Calculates average and median rows and columns.
// $isperc indicates whether the values are from percentiles or standards
function get_avgmed($class, $vals, $isperc)
{
	if($isperc)
		$color = get_color_prc();
	else
		$color = get_color_std();

	$testinfo = get_class_tests($class);
	$idtest = $testinfo[0];

	// Tests' averages and medians
	$sum = 0;
	foreach($idtest as $id)
	{
		// Test's average and color
		$ret['avg'][$id]['val'] = arr_avg(array_column($vals, $id), 5);
		$ret['avg'][$id]['color'] = color_from_val($ret['avg'][$id]['val'], $color, $isperc);

		// Sum for the total average
		if($ret['avg'][$id]['val'] != "-")
			$sum += $ret['avg'][$id]['val'];
	
		// Test's median and color
		$ret['med'][$id]['val'] = arr_med(array_column($vals, $id), 5);
		$ret['med'][$id]['color'] = color_from_val($ret['med'][$id]['val'], $color, $isperc);
	}

	// Students' averages and medians
	foreach($vals as $ids => $arr)
	{
		// Student's average and color
		$ret['savg'][$ids]['val'] = arr_avg($arr, 5);
		$ret['savg'][$ids]['color'] = color_from_val($ret['savg'][$ids]['val'], $color, $isperc);

		// Student's median and color 
		$ret['smed'][$ids]['val'] = arr_med($arr, 5);
		$ret['smed'][$ids]['color'] = color_from_val($ret['smed'][$ids]['val'], $color, $isperc);
	}
	
	// Total percentile average	and percentile
	$ret['tavg']['val'] = number_format($sum / sizeof($idtest), 5);
	$ret['tavg']['color'] = color_from_val($ret['tavg']['val'], $color, $isperc);

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

// Function to read colors for percentiles
function get_color_prc()
{
	$color_st = prepare_stmt("SELECT * FROM VALUTAZIONI JOIN VOTI ON fk_voto=id_voto WHERE fk_prof=?");
	$color_st->bind_param("i", $_SESSION['id']);
	$ret_gr = execute_stmt($color_st);
	$color_st->close();
	
	while($row = $ret_gr->fetch_assoc())
		$color[$row['perc']] = $row['color'];

	return $color;
}

// Function to elaborate colors wrt standard values
function get_color_std()
{
	// Gets only 6 colors
	$color_st = prepare_stmt("SELECT * FROM VOTI WHERE voto NOT IN(6.5, 8.5, 9, 9.5) ORDER BY voto");
	$ret_gr = execute_stmt($color_st);
	$color_st->close();

	$std = 0;
	while($row = $ret_gr->fetch_assoc())
	{
		$color[$std] = $row['color'];
		$std++;
	}

	return $color;
}

function get_color_vt()
{
	$ret_gr=query("SELECT * FROM VALUTAZIONI,VOTI WHERE fk_voto=id_voto AND fk_prof=".$_SESSION['id']);
	while($row=$ret_gr->fetch_assoc())
		$color[$row['voto']*10]=$row['color'];

	return $color;
}

function get_class_tests($class)
{
	$ctst_st = prepare_stmt("SELECT id_test, pos FROM TEST 
		WHERE id_test IN (
			SELECT DISTINCT(fk_test) FROM PROVE JOIN ISTANZE ON fk_ist=id_ist 
			WHERE fk_cl=?
		)");
	$ctst_st->bind_param("i", $class);
	$ctst = execute_stmt($ctst_st);
	$ctst_st->close();

	if($ctst->num_rows == 0)
		return null;

	while($row = $ctst->fetch_assoc())
	{
		$testlist[] = $row['id_test'];
		
		// Set to true if greater values correspond to a better performance
		$positive[$row['id_test']] = ($row['pos'] == "Maggiori");
	}

	return array($testlist, $positive);
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
function get_perc($class, $cond = null)
{
	$color = get_color_prc();

	$testinfo = get_class_tests($class);

	$testlist = "0";
	foreach($testinfo[0] as $id)
		$testlist .= ", $id";

	$positive = $testinfo[1];

	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		// Statement to get the count of tests only on results relevant to the user's selection
		$count_st = prepare_stmt("SELECT fk_test, COUNT(*) AS n FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl = id_cl 
			WHERE fk_test IN ($testlist)
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			GROUP BY fk_test");

		// Statement to get only the results of a class relevant to the user's selection
		$class_st = prepare_stmt("SELECT fk_ist, data, valore FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist 
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl
			WHERE fk_test=? AND fk_cl=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");

		// Statement to get the percentiles for tests with greater better values
		$greater_st = prepare_stmt("SELECT COUNT(*) AS perc FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist 
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl
			WHERE fk_test=? AND valore<=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");
		
		// Statement to get the percentiles for tests with lower better values
		$lower_st = prepare_stmt("SELECT COUNT(*) AS perc FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist 
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl
			WHERE fk_test=? AND valore>=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");

		// Binding is done based on the presence of the restriction of the professor
		if($cond['prof'] != "")
		{
			$count_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
			$class_st->bind_param("iiiii", $test, $class, $cond['year1'], $cond['year2'], $_SESSION['id']);
			$greater_st->bind_param("idiii", $test, $curval, $cond['year1'], $cond['year2'], $_SESSION['id']);
			$lower_st->bind_param("idiii", $test, $curval, $cond['year1'], $cond['year2'], $_SESSION['id']);
		}
		else
		{	
			$count_st->bind_param("ii", $cond['year1'], $cond['year2']);
			$class_st->bind_param("iiii", $test, $class, $cond['year1'], $cond['year2']);
			$greater_st->bind_param("idii", $test, $curval, $cond['year1'], $cond['year2']);
			$lower_st->bind_param("idii", $test, $curval, $cond['year1'], $cond['year2']);
		}
	}
	else
	{
		// Statement to get the number of results for each test done by the class
		$count_st = prepare_stmt("SELECT fk_test, COUNT(*) AS n FROM PROVE 
			WHERE fk_test IN ($testlist) GROUP BY fk_test");
		
		// Statement to get the values of a class
		$class_st = prepare_stmt("SELECT fk_ist, data, valore FROM PROVE JOIN ISTANZE ON fk_ist=id_ist 
			WHERE fk_test=? AND fk_cl=?");
		$class_st->bind_param("ii", $test, $class);

		// Statement to get the percentiles for tests with greater better values
		$greater_st = prepare_stmt("SELECT COUNT(*) AS perc FROM PROVE 
			WHERE fk_test=? AND valore<=?");
		$greater_st->bind_param("id", $test, $curval);

		// Statement to get the percentiles for tests with lower better values
		$lower_st = prepare_stmt("SELECT COUNT(*) AS perc FROM PROVE 
			WHERE fk_test=? AND valore>=?");
		$lower_st->bind_param("id", $test, $curval);
	}
	
	// Gets the total count of tests done by the class
	$cnt = execute_stmt($count_st);
	$count_st->close();
	
	$empty = true;
	while($row = $cnt->fetch_assoc())
	{ 
		$empty = $empty && ($row['n'] == 0);
		$count[$row['fk_test']] = $row['n'];
	}

	// If no rows are returned the function ends
	if($empty)
	{
		$class_st->close();
		$greater_st->close();
		$lower_st->close();

		return null;
	}

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
			$rstud['color'][$instance][$test] = color_from_val($perc, $color, true);
		}
	}

	$class_st->close();
	$greater_st->close();
	$lower_st->close();

	return $rstud;
}

// Function to get the standardized values for a class
function get_std($class, $cond = null)
{
	$color = get_color_std();

	$testinfo = get_class_tests($class);

	$testlist = "0";
	foreach($testinfo[0] as $id)
		$testlist .= ", $id";

	$positive = $testinfo[1];	

	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		// Statement to get the class's tests and their average and standard deviation, only for sets 
		// defined by the user
		$ctst_st = prepare_stmt("SELECT fk_test, AVG(valore) AS avg, STD(valore) AS std FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl
			WHERE fk_test IN ($testlist)
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			GROUP BY fk_test");

		// Statement to get the class's results that fall in the categories selected by the user
		$res_st = prepare_stmt("SELECT id_ist, fk_test, valore FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist 
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl
			WHERE fk_cl=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");
		
		if($cond['prof'] != "")
		{
			$ctst_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
			$res_st->bind_param("iiii", $class, $cond['year1'], $cond['year2'], $_SESSION['id']);
		}
		else
		{
			$ctst_st->bind_param("ii", $cond['year1'], $cond['year2']);
			$res_st->bind_param("iii", $class, $cond['year1'], $cond['year2']);
		}
	}
	else
	{
		// Statement to get the class's tests and their average and standard deviation
		$ctst_st = prepare_stmt("SELECT fk_test, AVG(valore) AS avg, STD(valore) AS std FROM PROVE 
			WHERE fk_test IN ($testlist)
			GROUP BY fk_test");

		// Statement to get the class's results
		$res_st = prepare_stmt("SELECT id_ist, fk_test, valore FROM PROVE JOIN ISTANZE ON fk_ist=id_ist WHERE fk_cl=?");
		$res_st->bind_param("i", $class);
	}
	
	$ctst = execute_stmt($ctst_st);
	$ctst_st->close();

	while($row = $ctst->fetch_assoc())
	{
		$avg[$row['fk_test']] = $row['avg'];
		$std[$row['fk_test']] = $row['std'];
	}

	$ret_res = execute_stmt($res_st);
	$res_st->close();

	while($row = $ret_res->fetch_assoc())
	{
		$z = ($row['valore'] - $avg[$row['fk_test']]) / $std[$row['fk_test']];

		// Inverts the sign if to a better perfomance corresponds a lower value
		if($positive[$row['fk_test']] == "Minori")
			$z *= -1;
		
		$rstud['val'][$row['id_ist']][$row['fk_test']] = number_format($z, 5);
		$rstud['color'][$row['id_ist']][$row['fk_test']] = color_from_val($z, $color, false);
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
	
	$ret_res=query("SELECT id_ist,p1.fk_test AS fk_test,MONTH(p1.data) AS data,COUNT(*) AS lte
		FROM PROVE AS p1,TEST,ISTANZE,(SELECT valore,fk_test FROM PROVE".$cond['tabs']." WHERE 1=1".$cond['rstr'].") as p2".$cond2."
		WHERE p1.fk_test=id_test 
		AND IF(pos='Maggiori',p2.valore<=p1.valore,p2.valore>=p1.valore) 
		AND fk_ist=id_ist AND fk_cl=".$_GET['id']." 
		AND p1.fk_test=p2.fk_test".$cond['rstr']."
		GROUP BY fk_ist,fk_test");

	while($row=$ret_res->fetch_assoc())
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