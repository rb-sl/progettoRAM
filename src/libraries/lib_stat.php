<?php
// Collection of functions used in the statistical section

// Constants for graphs
const MULTIBOX_YEAR = 0;
const MULTIBOX_CLASS = 1;
const MULTIBOX_GENDER = 2;

// Funzione per il calcolo della media dato un array $vals
function arr_avg($vals, $dec = 0)
{
	if(sizeof($vals))
    {
		$s = array_sum($vals);
		return number_format($s / sizeof($vals), $dec);
	}
	return "-";
}

// Funzione per il calcolo della mediana dato un array $vals
function arr_med($vals, $dec)
{
	$size = sizeof($vals);

	if($size == 0)
		return "-";

	sort($vals);

	// To align with array indices, in the even case 1 is subtracted from the found
	// place, while in the odd case floor is used in place of ceil
	if($size % 2 == 0)
    	return number_format(($vals[$size / 2 - 1] + $vals[$size / 2]) / 2, $dec);
	else
    	return number_format($vals[floor($size / 2)], $dec);	
}

// Funzione per il calcolo della deviazione standard di un array
function arr_std($vals,$avg="")
{
	$n=sizeof($vals);
	if($n>2)
	{
		if(!$avg)
			$avg=arr_avg($vals);

		$sum=array_sum($vals);
		
		$sq=0;
		foreach($vals as $val)
			$sq+=pow(($val-$avg),2);

		return (sqrt((1/($n-1))*$sq));
	}
	return "-";
}

// Funzione che calcola il coefficiente di correlazione campionaria tra due test
// dati gli id
function calc_r($id1,$id2,$cond="")
{
	 $ret1=query("SELECT AVG(valore) AS avg,STD(valore) AS std FROM PROVE".$cond['tabs']." WHERE fk_test=$id1".$cond['rstr']);
	 $t1=$ret1->fetch_assoc();
	
	 $ret2=query("SELECT AVG(valore) AS avg,STD(valore) AS std FROM PROVE".$cond['tabs']." WHERE fk_test=$id2".$cond['rstr']);
     $t2=$ret2->fetch_assoc();
    
	$retvals=r_vals($id1,$id2,$cond);

	$r['n']=$retvals->num_rows;
    if($r['n']>30) // Non è indicativo con pochi dati
    {
    	// Calcolo del coefficiente di correlazione campionaria r
    	// s=sum((x-avgx)*(y-avg(y))
    	$s=0;
    	while($row=$retvals->fetch_assoc())
        	$s+=($row['v1']-$t1['avg'])*($row['v2']-$t2['avg']);
        
        $r['r']=number_format($s/(($retvals->num_rows-1)*$t1['std']*$t2['std']),5);
    }
    else
      	$r['r']="-";

	return $r;
}

// Construction of additional restrictions based on GET data
function cond_builder()
{
	$base_cond = true;
	
	// Constuction of the class list
	$cond['class'] = "";
	for($i = 1; $i <= 5; $i++)
    	if(isset($_GET['c'.$i]))
       		$cond['class'] .= ", $i";
		else
			$base_cond = false;

	// Construction of the gender list
	$cond['sex'] = "";
	if(isset($_GET['m']))
    	$cond['sex'] .= ", 'm'";
	else
		$base_cond = false;

	if(isset($_GET['f']))
    	$cond['sex'] .= ", 'f'";
	else
		$base_cond = false;
	
	// Year-related restrictions
	$cond['year1'] = $_GET['year1'];
	$cond['year2'] = $_GET['year2'];

	// Restriction on the teacher
	if(isset($_GET['rstr']))
	{
		$base_cond = false;
    	$cond['prof'] = "AND fk_prof=?";
	}
	else
		$cond['prof'] = "";

	// If all base elements are selected there 
	// is no need to restrict results
	if($base_cond)
		return null;

	return $cond;
}

// Function to obtain record values and schools 
function get_records($id, $cond = null)
{
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		$rec_st = prepare_stmt("SELECT passo, pos, MAX(valore) AS max, MIN(valore) AS min 
			FROM PROVE JOIN TEST ON fk_test=id_test
			JOIN TIPOTEST ON fk_tipot=id_tipot
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN CLASSI ON fk_cl=id_cl 
			JOIN STUDENTI ON fk_stud=id_stud 
			WHERE id_test=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");
		
		$class_st = prepare_stmt("SELECT nomescuola, id_cl, classe, sez, anno, fk_prof 
			FROM PROVE JOIN ISTANZE ON fk_ist=id_ist
			JOIN CLASSI ON fk_cl=id_cl
			JOIN SCUOLE ON fk_scuola=id_scuola
			JOIN STUDENTI ON fk_stud=id_stud
			WHERE fk_test=? 
			AND valore=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof 
			ORDER BY anno ASC");

		if($prof != "")
		{ 
			$rec_st->bind_param("iiii", $id, $cond['year1'], $cond['year2'], $_SESSION['id']);
			$class_st->bind_param("idiii", $id, $best, $cond['year1'], $cond['year2'], $_SESSION['id']);
		}
		else
		{
			$rec_st->bind_param("iii", $id, $cond['year1'], $cond['year2']);
			$class_st->bind_param("idii", $id, $best, $cond['year1'], $cond['year2']);
		}
	}
	else
	{
		$rec_st = prepare_stmt("SELECT passo, pos, MAX(valore) AS max, MIN(valore) AS min 
		FROM PROVE JOIN TEST ON fk_test=id_test
		JOIN TIPOTEST ON fk_tipot=id_tipot
		WHERE id_test=?");
		$rec_st->bind_param("i", $id);

		$class_st = prepare_stmt("SELECT nomescuola, id_cl, classe, sez, anno, fk_prof 
			FROM PROVE JOIN ISTANZE ON fk_ist=id_ist
			JOIN CLASSI ON fk_cl=id_cl
			JOIN SCUOLE ON fk_scuola=id_scuola
			WHERE fk_test=? 
			AND valore=? 
			ORDER BY anno ASC");
		$class_st->bind_param("id", $id, $best);
	}

	$ret = execute_stmt($rec_st);
	$rec_st->close();

	$record = $ret->fetch_assoc();
	if($record['pos'] == "Maggiori")
	{
		$rcr['best'] = $record['max'];
		$rcr['worst'] = $record['min'];
	}
	else
	{
		$rcr['best'] = $record['min'];
		$rcr['worst'] = $record['max'];
	}

	$best = $rcr['best'];

	// Gets classes' information
	$ret = execute_stmt($class_st);	
	
	// Float results are formatted
	if($rcr['best'] and $record['passo'] < 1)
	{
		$rcr['best'] = number_format($rcr['best'], 2);
		$rcr['worst'] = number_format($rcr['worst'], 2);
	}
	
	$rcr['list'] = "<table id='tbest' class='table table-striped'>";
	while($rcp = $ret->fetch_assoc())
	{
		$rcr['list'] .= "<tr><td class='rcr'>".$rcp['nomescuola']."</td><td class='rcr'>";

		if($rcp['fk_prof'] == $_SESSION['id'] or $_SESSION['priv'] == 0)
  		{
    		$rcr['list'] .= "<a href='/register/class_show.php?id=".$rcp['id_cl']."'>";
    		$fl = "</a>";
  		}
  		else
    		$fl = "";

		$rcr['list'] .= $rcp['classe'].$rcp['sez']." ".$rcp['anno']."/".($rcp['anno'] + 1)."$fl</td></tr>";
	}
	$rcr['list'] .= "</table>";

	return $rcr;
}

// Ottiene le statistiche aggiornate con la condizione
function get_stats($idtest, $cond = null)
{
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		$stat_st = prepare_stmt("SELECT COUNT(valore) as n, ROUND(AVG(valore), 2) AS avg, ROUND(STD(valore), 2) AS std
			FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl 
			WHERE fk_test=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");
		
		$even_st = prepare_stmt("SELECT ROUND(AVG(valore), 2) AS med FROM (
				SELECT valore 
				FROM PROVE JOIN ISTANZE ON fk_ist=id_ist
				JOIN STUDENTI ON fk_stud=id_stud 
				JOIN CLASSI ON fk_cl=id_cl 
				WHERE fk_test=? 
				AND anno BETWEEN ? AND ?
				AND classe IN (0 $classlist)
				AND sesso IN ('x' $genderlist)
				$prof
				ORDER BY valore ASC 
				LIMIT ?, 2
			) AS P");
		
		// Query to get the median if the number of results is odd
		$odd_st = prepare_stmt("SELECT valore AS med FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl 
			WHERE fk_test=? 
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			ORDER BY valore ASC 
			LIMIT ?, 1");

		if($prof != "")
		{
			$stat_st->bind_param("iiii", $idtest, $cond['year1'], $cond['year2'], $_SESSION['id']);
			$even_st->bind_param("iiiii", $idtest, $cond['year1'], $cond['year2'], $_SESSION['id'], $offset);
			$odd_st->bind_param("iiiii", $idtest, $cond['year1'], $cond['year2'], $_SESSION['id'], $offset);
		}
		else
		{
			$stat_st->bind_param("iii", $idtest, $cond['year1'], $cond['year2']);
			$even_st->bind_param("iiii", $idtest, $cond['year1'], $cond['year2'], $offset);
			$odd_st->bind_param("iiii", $idtest, $cond['year1'], $cond['year2'], $offset);
		}		
	}
	else
	{
		$stat_st = prepare_stmt("SELECT COUNT(valore) as n, ROUND(AVG(valore), 2) AS avg, ROUND(STD(valore), 2) AS std
			FROM PROVE
			WHERE fk_test=?");
		$stat_st->bind_param("i", $idtest);

		// Query to get the median if the number of results is even
		$even_st = prepare_stmt("SELECT ROUND(AVG(valore), 2) AS med FROM (
				SELECT valore 
				FROM PROVE 
				WHERE fk_test=? 
				ORDER BY valore ASC 
				LIMIT ?, 2
			) AS P");
		$even_st->bind_param("ii", $idtest, $offset);

		// Query to get the median if the number of results is odd
		$odd_st = prepare_stmt("SELECT valore AS med
			FROM PROVE 
			WHERE fk_test=? 
			ORDER BY valore ASC 
			LIMIT ?, 1");
		$odd_st->bind_param("ii", $idtest, $offset);
	}

	$ret = execute_stmt($stat_st);
	$stat = $ret->fetch_assoc();
	$stat_st->close();

	if($stat['n'] > 0)
	{
		// Calculation of the position of the median value(s)
		// taking into account that MySQL limit starts at $offset + 1
		if($stat['n'] % 2 == 0)
		{
			$offset =  $stat['n'] / 2 - 1;
			$ret = execute_stmt($even_st);
		}
		else
		{
			$offset = floor($stat['n'] / 2);
			$ret = execute_stmt($odd_st);
		}
		$odd_st->close();
		$even_st->close();
		$med = $ret->fetch_assoc();
	}
	else
		$med['med'] = null;

	return array_merge($stat, $med);
}

// Function to get labels and values for multiple box plots
function graph_multibox($id, $group, $cond = null)
{
	switch($group)
	{
		case MULTIBOX_CLASS:
			$field = "classe";
			break;
		case MULTIBOX_GENDER:
			$field = "sesso";
			break;
		case MULTIBOX_YEAR:
			$field = "anno";
			break;
	}

	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		$val_st = prepare_stmt("SELECT $field, valore FROM PROVE
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN CLASSI ON fk_cl=id_cl
			JOIN STUDENTI ON fk_stud=id_stud 
			WHERE fk_test=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			ORDER BY $field, valore ASC");
		
		if($prof != "")
			$val_st->bind_param("iiii", $id, $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$val_st->bind_param("iii", $id, $cond['year1'], $cond['year2']);
	}
	else
	{
		$val_st = prepare_stmt("SELECT $field, valore FROM PROVE
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN CLASSI ON fk_cl=id_cl
			WHERE fk_test=?
			ORDER BY $field, valore ASC");
		$val_st->bind_param("i", $id);
	}

	$ret = execute_stmt($val_st);
	$val_st->close();

	while($row = $ret->fetch_assoc())
    	$graph[$row[$field]][] = $row['valore'];
	
	return $graph;
}

// Function to get labels and values for percentile plots
function graph_prc($id, $cond = null)
{
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		$count_st = prepare_stmt("SELECT COUNT(*) AS n, pos FROM PROVE 
			JOIN TEST ON fk_test=id_test 
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl  
			WHERE id_test=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");

		if($prof != "")
			$count_st->bind_param("iiii", $id, $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$count_st->bind_param("iii", $id, $cond['year1'], $cond['year2']);
	}
	else
	{
		$count_st = prepare_stmt("SELECT COUNT(*) AS n, pos FROM PROVE 
			JOIN TEST ON fk_test=id_test 
			WHERE id_test=?");
		$count_st->bind_param("i", $id);
	}

	$ret = execute_stmt($count_st);
	$count_st->close();
	$test = $ret->fetch_assoc();

	$graph['vals'] = [];
	$graph['lbls'] = [];

	if($test['n'] == 0)
    	return $graph;   

	// Preparation of the query, whose order is defined by the test
    if($test['pos'] == "Maggiori")
		$order = "ASC";
	else
		$order = "DESC";
	
	if($cond)
	{
		$val_st = prepare_stmt("SELECT valore FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl  
			WHERE fk_test=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			ORDER BY valore $order");
		
		if($prof != "")
			$val_st->bind_param("iiii", $id, $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$val_st->bind_param("iii", $id, $cond['year1'], $cond['year2']);
	}
	else
	{
		$val_st = prepare_stmt("SELECT valore FROM PROVE 
			WHERE fk_test=?
			ORDER BY valore $order");
		$val_st->bind_param("i", $id);
	}

	$retvals = execute_stmt($val_st);
	$val_st->close();

	$i = 1;
	while($val = $retvals->fetch_assoc())
    {
    	$graph['lbls'][] = number_format(($i / $test['n']) * 100, 2);
    	$graph['vals'][] = $val['valore'];
    	
    	$i++;
    }
	
	return $graph;
}

// Function to get values for normal and single box plots
function graph_vals($id, $cond = null)
{
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		$val_st = prepare_stmt("SELECT valore FROM PROVE
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl  
			WHERE fk_test=? 
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			ORDER BY valore ASC");

		if($prof != "")
			$val_st->bind_param("iiii", $id, $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$val_st->bind_param("iii", $id, $cond['year1'], $cond['year2']);
	}
	else
	{
		$val_st = prepare_stmt("SELECT valore FROM PROVE WHERE fk_test=? ORDER BY valore ASC");
		$val_st->bind_param("i", $id);
	}
	$ret = execute_stmt($val_st);
	$val_st->close();

	$graph['vals'] = [];
	while($row = $ret->fetch_assoc())
    	$graph['vals'][] = $row['valore'];

	return $graph;
}

//Appoggio per la funzione calc_r, query usata anche in altri casi
function r_vals($id1,$id2,$cond="")
{
	$cond['tabs']=str_replace(",ISTANZE,CLASSI","",$cond['tabs']);

    return query("SELECT DISTINCT (P1.fk_stud),P1.anno,v1,v2  FROM 
		(SELECT valore AS v1,anno,fk_stud from PROVE,ISTANZE,CLASSI".$cond['tabs']." WHERE fk_ist=id_ist AND fk_cl=id_cl AND fk_test=$id1".$cond['rstr']." ) AS P1 
		INNER JOIN 
		(SELECT valore AS v2,anno,fk_stud from PROVE,ISTANZE,CLASSI".$cond['tabs']." WHERE fk_ist=id_ist AND fk_cl=id_cl AND fk_test=$id2".$cond['rstr']." ) AS P2 
		USING (fk_stud,anno)");
}
?>
