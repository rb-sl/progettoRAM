<?php
// Collection of functions used in the statistical section

// Number of test results after which the correlation is deemed significant
const CORRELATION_TRESH = 30;

// Constants for graphs
const GRAPH_YEAR = 0;
const GRAPH_CLASS = 1;
const GRAPH_GENDER = 2;
const GRAPH_TEST = 3;

// Function to compute the average of an array
function arr_avg($vals, $dec = 0)
{
	if(sizeof($vals))
    {
		$s = array_sum($vals);
		return number_format($s / sizeof($vals), $dec);
	}
	return "-";
}

// Function to compute the median of an array
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

// Function to get the numbers displayed in statistics.php
function get_general_stats($cond = null)
{
	$ret = [];

	// Number of students
	$count_st = prepare_stmt("SELECT COUNT(*) AS n FROM STUDENTI");
	$ret_s = execute_stmt($count_st);
	$count_st->close();

	$count_s = $ret_s->fetch_assoc();
	$ret['stud_tot'] = $count_s['n'];

	// Number of tests
	$count_st = prepare_stmt("SELECT COUNT(*) AS n FROM PROVE");
	$ret_r = execute_stmt($count_st);
	$count_st->close();

	$count_r = $ret_r->fetch_assoc();
	$ret['res_tot'] = $count_r['n'];

	// With restrictions, the number and ratio over the total
	// are calculated
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		// Restricted students' numbers
		$count_st = prepare_stmt("SELECT COUNT(DISTINCT(id_stud)) AS n FROM STUDENTI
			JOIN ISTANZE ON fk_stud=id_stud
			JOIN CLASSI ON fk_cl=id_cl
			WHERE anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");
		if($prof != "")
			$count_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$count_st->bind_param("ii", $cond['year1'], $cond['year2']);

		$ret_s = execute_stmt($count_st);
		$count_st->close();
		$count_s = $ret_s->fetch_assoc();
		$ret['stud_num'] = $count_s['n'];
		$ret['stud_perc'] = number_format($ret['stud_num'] / $ret['stud_tot'] * 100, 2);

		// Restricted results' numbers
		$count_st = prepare_stmt("SELECT COUNT(*) AS n FROM PROVE
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud
			JOIN CLASSI ON fk_cl=id_cl
			WHERE anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");
		if($prof != "")
			$count_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$count_st->bind_param("ii", $cond['year1'], $cond['year2']);

		$ret_r = execute_stmt($count_st);
		$count_st->close();
		$count_r = $ret_r->fetch_assoc();
		$ret['res_num'] = $count_r['n'];
		$ret['res_perc'] = number_format($ret['res_num'] / $ret['res_tot'] * 100, 2);
	}

	return $ret;	
}

// Function to obtain the data used in the general plot in statistics.php
function misc_graph($cond = null)
{
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		// Statement to get the number of results for each test
		$test_st = prepare_stmt("SELECT nometest, COUNT(*) AS n FROM PROVE 
			JOIN TEST ON fk_test=id_test
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl 
			WHERE anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			GROUP BY id_test 
			ORDER BY n");

		// Statement to get the number of results for each student's gender
		$stud_st = prepare_stmt("SELECT sesso, COUNT(*) AS n FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud
			JOIN CLASSI ON fk_cl=id_cl 
			WHERE anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			GROUP BY sesso");

		// Statement to get the number of results for each class number
		$class_st = prepare_stmt("SELECT classe, COUNT(*) AS n FROM PROVE
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN CLASSI ON fk_cl=id_cl 
			JOIN STUDENTI ON fk_stud=id_stud 
			WHERE anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			GROUP BY classe 
			ORDER BY classe ASC");

		// Statement to get the number of results for each year
		$year_st = prepare_stmt("SELECT anno, COUNT(*) AS n FROM PROVE
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN CLASSI ON fk_cl=id_cl 
			JOIN STUDENTI ON fk_stud=id_stud 
			WHERE anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			GROUP BY anno 
			ORDER BY anno ASC");

		if($prof != "")
		{
			$test_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
			$stud_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
			$class_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
			$year_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
		}
		else
		{
			$test_st->bind_param("ii", $cond['year1'], $cond['year2']);
			$stud_st->bind_param("ii", $cond['year1'], $cond['year2']);
			$class_st->bind_param("ii", $cond['year1'], $cond['year2']);
			$year_st->bind_param("ii", $cond['year1'], $cond['year2']);
		}
	}
	else
	{
		// Statement to get the number of results for each test
		$test_st = prepare_stmt("SELECT nometest, COUNT(*) AS n FROM PROVE 
			JOIN TEST ON fk_test=id_test
			GROUP BY id_test 
			ORDER BY n");

		// Statement to get the number of results for each student's gender
		$stud_st = prepare_stmt("SELECT sesso, COUNT(*) AS n FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			GROUP BY sesso");

		// Statement to get the number of results for each class number
		$class_st = prepare_stmt("SELECT classe, COUNT(*) AS n FROM PROVE
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN CLASSI ON fk_cl=id_cl 
			GROUP BY classe 
			ORDER BY classe ASC");

		// Statement to get the number of results for each year
		$year_st = prepare_stmt("SELECT anno, COUNT(*) AS n FROM PROVE
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN CLASSI ON fk_cl=id_cl 
			GROUP BY anno 
			ORDER BY anno ASC");
	}

	// Number of results divided by test
	$ret_t = execute_stmt($test_st);
	$test_st->close();

	$ret['test']['vals'] = [];
	$ret['test']['lbls'] = [];
	while($row = $ret_t->fetch_assoc())
	{
		$ret['test']['vals'][] = $row['n'];
		$ret['test']['lbls'][] = $row['nometest'];
	}

	// Number of results divided by students' gender
	$ret_s = execute_stmt($stud_st);
	$stud_st->close();

	$ret['stud']['vals'] = [];
	$ret['stud']['lbls'] = [];
	while($row = $ret_s->fetch_assoc())
	{
		$ret['stud']['vals'][] = $row['n'];
		$ret['stud']['lbls'][] = $row['sesso'];
	}

	// Number of results divided by class
	$ret_c = execute_stmt($class_st);
	$class_st->close();

	$ret['class']['vals'] = [];
	$ret['class']['lbls'] = [];
	while($row = $ret_c->fetch_assoc())
	{
		$ret['class']['vals'][] = $row['n'];
		$ret['class']['lbls'][] = $row['classe'];
	}

	// Number of results divided by year
	$ret_y = execute_stmt($year_st);
	$year_st->close();

	$ret['year']['vals'] = [];
	$ret['year']['lbls'] = [];
	while($row = $ret_y->fetch_assoc())
	{
		$ret['year']['vals'][] = $row['n'];
		$ret['year']['lbls'][] = $row['anno']."/".($row['anno']+1);
	}

	return $ret;
}

// Function to calculate the correlation coefficient between two tests
// given their ids
function calc_r($id1, $stat1, $id2, $stat2, $cond = null)
{    
	global $r_id1;
	global $r_id2;
	global $rval_st;

	$r_id1 = $id1;
	$r_id2 = $id2;
	$retvals = execute_stmt($rval_st);
	$r['n'] = $retvals->num_rows;

	// As r is not indicative with few data, only couples of tests 
	// with	at least N values are considered
    if($r['n'] > CORRELATION_TRESH)
    {
    	// Calculation of the correlation coefficient as
    	// sum((x - avg(x)) * (y - avg(y)) / ((n-1) * std(x) * std(y))
		// From "Introduction to probability and statistics for engineers and scientists"
		// By Sheldon M. Ross
    	$s = 0;
    	while($row = $retvals->fetch_assoc())
			$s += ($row['v1'] - $stat1['avg']) * ($row['v2'] - $stat2['avg']);

        $r['r'] = number_format($s / (($r['n'] - 1) * $stat1['std'] * $stat2['std']), 5);
    }
    else
      	$r['r'] = "-";

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
	$year = year_span();
	if($_GET['year1'] != $year['y1'] or $_GET['year2'] != $year['y2'])
		$base_cond = false;

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

// Gets updated statistics for a test
function get_stats($idtest, $cond = null, $get_median = true)
{
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		$stat_st = prepare_stmt("SELECT COUNT(valore) as n, AVG(valore) AS avg, STD(valore) AS std
			FROM PROVE 
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl 
			WHERE fk_test=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");
		
		if($get_median)
		{
			$even_st = prepare_stmt("SELECT AVG(valore) AS med FROM (
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
		}

		if($prof != "")
		{
			$stat_st->bind_param("iiii", $idtest, $cond['year1'], $cond['year2'], $_SESSION['id']);
			if($get_median)
			{
				$even_st->bind_param("iiiii", $idtest, $cond['year1'], $cond['year2'], $_SESSION['id'], $offset);
				$odd_st->bind_param("iiiii", $idtest, $cond['year1'], $cond['year2'], $_SESSION['id'], $offset);
			}
		}
		else
		{
			$stat_st->bind_param("iii", $idtest, $cond['year1'], $cond['year2']);
			if($get_median)
			{
				$even_st->bind_param("iiii", $idtest, $cond['year1'], $cond['year2'], $offset);
				$odd_st->bind_param("iiii", $idtest, $cond['year1'], $cond['year2'], $offset);
			}
		}		
	}
	else
	{
		$stat_st = prepare_stmt("SELECT COUNT(valore) as n, AVG(valore) AS avg, STD(valore) AS std
			FROM PROVE
			WHERE fk_test=?");
		$stat_st->bind_param("i", $idtest);

		if($get_median)
		{
			// Query to get the median if the number of results is even
			$even_st = prepare_stmt("SELECT AVG(valore) AS med FROM (
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
	}

	$ret = execute_stmt($stat_st);
	$stat = $ret->fetch_assoc();
	$stat_st->close();

	if($stat['n'] > 0 and $get_median)
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
		case GRAPH_CLASS:
			$field = "classe";
			break;
		case GRAPH_GENDER:
			$field = "sesso";
			break;
		case GRAPH_YEAR:
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
			JOIN STUDENTI ON fk_stud=id_stud 
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

// Function to open the global statement to get values of two tests
function open_rvals_stmt($cond = null)
{
	global $r_id1;
	global $r_id2;
	global $rval_st;

	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		$rval_st = prepare_stmt("SELECT P1.valore AS v1, P2.valore AS v2
			FROM PROVE AS P1 JOIN PROVE AS P2 ON P1.fk_ist=P2.fk_ist
			JOIN ISTANZE ON P1.fk_ist=id_ist
			JOIN STUDENTI ON fk_stud=id_stud 
			JOIN CLASSI ON fk_cl=id_cl 
			WHERE P1.fk_test=? AND P2.fk_test=?
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof");

		if($prof != "")
			$rval_st->bind_param("iiiii", $r_id1, $r_id2, $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$rval_st->bind_param("iiii", $r_id1, $r_id2, $cond['year1'], $cond['year2']);
	}
	else
	{
		$rval_st = prepare_stmt("SELECT P1.valore AS v1, P2.valore AS v2
			FROM PROVE AS P1 JOIN PROVE AS P2 ON P1.fk_ist=P2.fk_ist
			WHERE P1.fk_test=?
			AND P2.fk_test=?");
		$rval_st->bind_param("ii", $r_id1, $r_id2);
	}

	return;
}

// Function to get tests with a significant number of result (for correlation)
function get_test_correlation($cond = null)
{	
	$threshold = CORRELATION_TRESH;
	$test_st = prepare_stmt("SELECT id_test, nometest, pos FROM TEST 
		WHERE id_test IN (SELECT fk_test FROM PROVE GROUP BY fk_test HAVING COUNT(*) > ?) 
		ORDER BY nometest");
	$test_st->bind_param("i", $threshold);
	$res = execute_stmt($test_st);
	$test_st->close();

	$ret['list'] = "-1";
	while($row = $res->fetch_assoc())
	{
		$ret['names'][$row['id_test']] = $row['nometest'];
		$ret['positive'][$row['id_test']] = $row['pos'];
		$ret['statistics'][$row['id_test']] = get_stats($row['id_test'], $cond);
		$ret['list'] .= ", ".$row['id_test'];
	}
	return $ret;
}

// Function to get the data for the scatter plot matrix
function test_graph($testlist, $cond = null)
{
	// Gets each test's unit
	$unit_st = prepare_stmt("SELECT nometest, simbolo FROM TEST
		JOIN UNITA ON fk_udm=id_udm
		WHERE id_test IN($testlist)");
	$unit_r = execute_stmt($unit_st);
	$unit_st->close();
	while($row = $unit_r->fetch_assoc())
		$unit[$row['nometest']] = $row['simbolo'];

	// Builds the query to get all results for given tests
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['sex'];
		$prof = $cond['prof'];

		$splom_st = prepare_stmt("SELECT nometest, fk_ist, valore FROM PROVE 
			JOIN TEST ON fk_test=id_test
			JOIN ISTANZE ON fk_ist=id_ist
			JOIN CLASSI ON fk_cl=id_cl
			JOIN STUDENTI ON fk_stud=id_stud 
			WHERE fk_test IN ($testlist)
			AND anno BETWEEN ? AND ?
			AND classe IN (0 $classlist)
			AND sesso IN ('x' $genderlist)
			$prof
			ORDER BY fk_ist, nometest");

		if($prof != "")
			$splom_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$splom_st->bind_param("ii", $cond['year1'], $cond['year2']);
	}
	else
	{
		$splom_st = prepare_stmt("SELECT nometest, fk_ist, valore 
			FROM PROVE JOIN TEST ON fk_test=id_test
			WHERE fk_test IN ($testlist) ORDER BY fk_ist, nometest");
	}
	
	$splomret = execute_stmt($splom_st);
	$previnst = -1;
	$instances = [];
	$splom = [];
	while($splomrow = $splomret->fetch_assoc())
	{
		// Builds a table such as
		// id_ist | id_test | val
		// with empty val entries if needed
		$splom[$splomrow['nometest']][$splomrow['fk_ist']] = $splomrow['valore'];

		if($previnst != $splomrow['fk_ist'])
		{
			$previnst = $splomrow['fk_ist'];
			$instances[] = $splomrow['fk_ist'];
		}
	}

	$ret = [];
	foreach($splom as $test => $list)
	{	
		$curr = [];

		$curr['label'] = $test;
		if($unit[$test] != "")
			$curr['unit'] = " [".$unit[$test]."]";
		else
			$curr['unit'] = "";
			
 		foreach($instances as $i)
		{
			if(isset($list[$i]))
				$curr['values'][] = $list[$i];
			else
				$curr['values'][] = null;
		}

		$ret[] = $curr;
	}

	return $ret;
}
?>
