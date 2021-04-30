<?php
// Copyright 2021 Roberto Basla

// This file is part of progettoRAM.

// progettoRAM is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// progettoRAM is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.

// You should have received a copy of the GNU Affero General Public License
// along with progettoRAM.  If not, see <http://www.gnu.org/licenses/>.

// Collection of functions used in the statistical section

// Number of test results after which the correlation is deemed significant
const CORRELATION_THRESH = 30;

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
function arr_med($vals, $dec = 0)
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
	$cond['gender'] = "";
	if(isset($_GET['m']))
		$cond['gender'] .= ", 'm'";
	else
		$base_cond = false;

	if(isset($_GET['f']))
		$cond['gender'] .= ", 'f'";
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
		$cond['user'] = "AND user_fk=?";
	}
	else
		$cond['user'] = "";

	// If all base elements are selected there 
	// is no need to restrict results
	if($base_cond)
		return null;

	return $cond;
}

// Function to get the numbers displayed in statistics.php
function get_general_stats($cond = null)
{
	$ret = [];

	// Number of students
	$count_st = prepare_stmt("SELECT COUNT(*) AS n FROM student");
	$ret_s = execute_stmt($count_st);
	$count_st->close();

	$count_s = $ret_s->fetch_assoc();
	$ret['stud_tot'] = $count_s['n'];

	// Number of tests
	$count_st = prepare_stmt("SELECT COUNT(*) AS n FROM results");
	$ret_r = execute_stmt($count_st);
	$count_st->close();

	$count_r = $ret_r->fetch_assoc();
	$ret['res_tot'] = $count_r['n'];

	// With restrictions, the number and ratio over the total
	// are calculated
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		// Restricted students' numbers
		$count_st = prepare_stmt("SELECT COUNT(DISTINCT(student_id)) AS n FROM student
			JOIN instance ON student_fk=student_id
			JOIN class ON class_fk=class_id
			WHERE class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user");
		if($user != "")
			$count_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$count_st->bind_param("ii", $cond['year1'], $cond['year2']);

		$ret_s = execute_stmt($count_st);
		$count_st->close();
		$count_s = $ret_s->fetch_assoc();
		
		$ret['stud_num'] = $count_s['n'];
		if($ret['stud_tot'] == 0)
			$ret['stud_perc'] = 100;
		else
			$ret['stud_perc'] = number_format($ret['stud_num'] / $ret['stud_tot'] * 100, 2);
	
		// Restricted results' numbers
		$count_st = prepare_stmt("SELECT COUNT(*) AS n FROM results
			JOIN instance ON instance_fk=instance_id
			JOIN student ON student_fk=student_id
			JOIN class ON class_fk=class_id
			WHERE class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user");
		if($user != "")
			$count_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$count_st->bind_param("ii", $cond['year1'], $cond['year2']);

		$ret_r = execute_stmt($count_st);
		$count_st->close();
		$count_r = $ret_r->fetch_assoc();
		$ret['res_num'] = $count_r['n'];
		if($ret['res_tot'] == 0)
			$ret['res_perc'] = 100;
		else
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
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		// Statement to get the number of results for each test
		$test_st = prepare_stmt("SELECT test_name, COUNT(*) AS n FROM results 
			JOIN test ON test_fk=test_id
			JOIN instance ON instance_fk=instance_id
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk=class_id 
			WHERE class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			GROUP BY test_id 
			ORDER BY n");

		// Statement to get the number of results for each student's gender
		$stud_st = prepare_stmt("SELECT gender, COUNT(*) AS n FROM results 
			JOIN instance ON instance_fk=instance_id
			JOIN student ON student_fk=student_id
			JOIN class ON class_fk=class_id 
			WHERE class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			GROUP BY gender");

		// Statement to get the number of results for each class number
		$class_st = prepare_stmt("SELECT class, COUNT(*) AS n FROM results
			JOIN instance ON instance_fk=instance_id
			JOIN class ON class_fk=class_id 
			JOIN student ON student_fk=student_id 
			WHERE class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			GROUP BY class 
			ORDER BY class ASC");

		// Statement to get the number of results for each year
		$year_st = prepare_stmt("SELECT class_year, COUNT(*) AS n FROM results
			JOIN instance ON instance_fk=instance_id
			JOIN class ON class_fk=class_id 
			JOIN student ON student_fk=student_id 
			WHERE class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			GROUP BY class_year 
			ORDER BY class_year ASC");

		if($user != "")
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
		$test_st = prepare_stmt("SELECT test_name, COUNT(*) AS n FROM results 
			JOIN test ON test_fk=test_id
			GROUP BY test_id 
			ORDER BY n");

		// Statement to get the number of results for each student's gender
		$stud_st = prepare_stmt("SELECT gender, COUNT(*) AS n FROM results 
			JOIN instance ON instance_fk=instance_id
			JOIN student ON student_fk=student_id 
			GROUP BY gender");

		// Statement to get the number of results for each class number
		$class_st = prepare_stmt("SELECT class, COUNT(*) AS n FROM results
			JOIN instance ON instance_fk=instance_id
			JOIN class ON class_fk=class_id 
			GROUP BY class 
			ORDER BY class ASC");

		// Statement to get the number of results for each year
		$year_st = prepare_stmt("SELECT class_year, COUNT(*) AS n FROM results
			JOIN instance ON instance_fk=instance_id
			JOIN class ON class_fk=class_id 
			GROUP BY class_year 
			ORDER BY class_year ASC");
	}

	// Number of results divided by test
	$ret_t = execute_stmt($test_st);
	$test_st->close();

	$ret['test']['vals'] = [];
	$ret['test']['lbls'] = [];
	while($row = $ret_t->fetch_assoc())
	{
		$ret['test']['vals'][] = $row['n'];
		$ret['test']['lbls'][] = $row['test_name'];
	}

	// Number of results divided by students' gender
	$ret_s = execute_stmt($stud_st);
	$stud_st->close();

	$ret['stud']['vals'] = [];
	$ret['stud']['lbls'] = [];
	while($row = $ret_s->fetch_assoc())
	{
		$ret['stud']['vals'][] = $row['n'];
		$ret['stud']['lbls'][] = $row['gender'];
	}

	// Number of results divided by class
	$ret_c = execute_stmt($class_st);
	$class_st->close();

	$ret['class']['vals'] = [];
	$ret['class']['lbls'] = [];
	while($row = $ret_c->fetch_assoc())
	{
		$ret['class']['vals'][] = $row['n'];
		$ret['class']['lbls'][] = $row['class'];
	}

	// Number of results divided by year
	$ret_y = execute_stmt($year_st);
	$year_st->close();

	$ret['year']['vals'] = [];
	$ret['year']['lbls'] = [];
	while($row = $ret_y->fetch_assoc())
	{
		$ret['year']['vals'][] = $row['n'];
		$ret['year']['lbls'][] = $row['class_year']."/".($row['class_year']+1);
	}

	return $ret;
}

// Function to obtain record values and schools 
function get_records($id, $cond = null)
{
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		$rec_st = prepare_stmt("SELECT step, positive_values, MAX(value) AS max, MIN(value) AS min 
			FROM results JOIN test ON test_fk=test_id
			JOIN datatype ON datatype_fk=datatype_id
			JOIN instance ON instance_fk=instance_id
			JOIN class ON class_fk=class_id 
			JOIN student ON student_fk=student_id 
			WHERE test_id=?
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user");
		
		$class_st = prepare_stmt("SELECT school_name, class_id, class, section, class_year, user_fk 
			FROM results JOIN instance ON instance_fk=instance_id
			JOIN class ON class_fk=class_id
			JOIN school ON school_fk=school_id
			JOIN student ON student_fk=student_id
			WHERE test_fk=? 
			AND value=?
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user 
			ORDER BY class_year ASC");

		if($user != "")
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
		$rec_st = prepare_stmt("SELECT step, positive_values, MAX(value) AS max, MIN(value) AS min 
		FROM results JOIN test ON test_fk=test_id
		JOIN datatype ON datatype_fk=datatype_id
		WHERE test_id=?");
		$rec_st->bind_param("i", $id);

		$class_st = prepare_stmt("SELECT school_name, class_id, class, section, class_year, user_fk 
			FROM results JOIN instance ON instance_fk=instance_id
			JOIN class ON class_fk=class_id
			JOIN school ON school_fk=school_id
			WHERE test_fk=? 
			AND value=? 
			ORDER BY class_year ASC");
		$class_st->bind_param("id", $id, $best);
	}

	$ret = execute_stmt($rec_st);
	$rec_st->close();

	$record = $ret->fetch_assoc();
	if($record['positive_values'] == GREATER)
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
	if($rcr['best'] and $record['step'] < 1)
	{
		$rcr['best'] = number_format($rcr['best'], 2);
		$rcr['worst'] = number_format($rcr['worst'], 2);
	}
	
	$rcr['list'] = "<table id='tbest' class='table table-light table-striped'>";
	while($rcp = $ret->fetch_assoc())
	{
		$rcr['list'] .= "<tr><td class='rcr'>".$rcp['school_name']."</td><td class='rcr'>";

		if($rcp['user_fk'] == $_SESSION['id'] or chk_auth(ADMINISTRATOR))
  		{
			$rcr['list'] .= "<a href='/register/class_show.php?id=".$rcp['class_id']."'>";
			$fl = "</a>";
  		}
  		else
			$fl = "";

		$rcr['list'] .= $rcp['class'].$rcp['section']." ".$rcp['class_year']."/".($rcp['class_year'] + 1)."$fl</td></tr>";
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
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		$stat_st = prepare_stmt("SELECT COUNT(value) as n, AVG(value) AS avg, STD(value) AS std
			FROM results 
			JOIN instance ON instance_fk=instance_id
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk=class_id 
			WHERE test_fk=?
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user");
		
		if($get_median)
		{
			$even_st = prepare_stmt("SELECT AVG(value) AS med FROM (
					SELECT value 
					FROM results JOIN instance ON instance_fk=instance_id
					JOIN student ON student_fk=student_id 
					JOIN class ON class_fk=class_id 
					WHERE test_fk=? 
					AND class_year BETWEEN ? AND ?
					AND class IN (0 $classlist)
					AND gender IN ('x' $genderlist)
					$user
					ORDER BY value ASC 
					LIMIT ?, 2
				) AS P");
			
			// Query to get the median if the number of results is odd
			$odd_st = prepare_stmt("SELECT value AS med FROM results 
				JOIN instance ON instance_fk=instance_id
				JOIN student ON student_fk=student_id 
				JOIN class ON class_fk=class_id 
				WHERE test_fk=? 
				AND class_year BETWEEN ? AND ?
				AND class IN (0 $classlist)
				AND gender IN ('x' $genderlist)
				$user
				ORDER BY value ASC 
				LIMIT ?, 1");
		}

		if($user != "")
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
		$stat_st = prepare_stmt("SELECT COUNT(value) as n, AVG(value) AS avg, STD(value) AS std
			FROM results
			WHERE test_fk=?");
		$stat_st->bind_param("i", $idtest);

		if($get_median)
		{
			// Query to get the median if the number of results is even
			$even_st = prepare_stmt("SELECT AVG(value) AS med FROM (
					SELECT value 
					FROM results 
					WHERE test_fk=? 
					ORDER BY value ASC 
					LIMIT ?, 2
				) AS P");
			$even_st->bind_param("ii", $idtest, $offset);

			// Query to get the median if the number of results is odd
			$odd_st = prepare_stmt("SELECT value AS med
				FROM results 
				WHERE test_fk=? 
				ORDER BY value ASC 
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

// Function to get values for normal and single box plots
function graph_vals($id, $cond = null)
{
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		$val_st = prepare_stmt("SELECT value FROM results
			JOIN instance ON instance_fk=instance_id
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk=class_id  
			WHERE test_fk=? 
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			ORDER BY value ASC");

		if($user != "")
			$val_st->bind_param("iiii", $id, $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$val_st->bind_param("iii", $id, $cond['year1'], $cond['year2']);
	}
	else
	{
		$val_st = prepare_stmt("SELECT value FROM results WHERE test_fk=? ORDER BY value ASC");
		$val_st->bind_param("i", $id);
	}
	$ret = execute_stmt($val_st);
	$val_st->close();

	$graph['vals'] = [];
	while($row = $ret->fetch_assoc())
		$graph['vals'][] = $row['value'];

	return $graph;
}

// Function to get labels and values for percentile plots
function graph_prc($id, $cond = null)
{
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		$count_st = prepare_stmt("SELECT COUNT(*) AS n, positive_values FROM results 
			JOIN test ON test_fk=test_id 
			JOIN instance ON instance_fk=instance_id
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk=class_id  
			WHERE test_id=?
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user");

		if($user != "")
			$count_st->bind_param("iiii", $id, $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$count_st->bind_param("iii", $id, $cond['year1'], $cond['year2']);
	}
	else
	{
		$count_st = prepare_stmt("SELECT COUNT(*) AS n, positive_values FROM results 
			JOIN test ON test_fk=test_id 
			WHERE test_id=?");
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
	if($test['positive_values'] == GREATER)
		$order = "ASC";
	else
		$order = "DESC";
	
	if($cond)
	{
		$val_st = prepare_stmt("SELECT value FROM results 
			JOIN instance ON instance_fk=instance_id
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk=class_id  
			WHERE test_fk=?
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			ORDER BY value $order");
		
		if($user != "")
			$val_st->bind_param("iiii", $id, $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$val_st->bind_param("iii", $id, $cond['year1'], $cond['year2']);
	}
	else
	{
		$val_st = prepare_stmt("SELECT value FROM results 
			WHERE test_fk=?
			ORDER BY value $order");
		$val_st->bind_param("i", $id);
	}

	$retvals = execute_stmt($val_st);
	$val_st->close();

	$i = 1;
	while($val = $retvals->fetch_assoc())
	{
		$graph['lbls'][] = number_format(($i / $test['n']) * 100, 2);
		$graph['vals'][] = $val['value'];
		
		$i++;
	}
	
	return $graph;
}


// Function to get labels and values for multiple box plots
function graph_multibox($id, $group, $cond = null)
{
	switch($group)
	{
		case GRAPH_CLASS:
			$field = "class";
			break;
		case GRAPH_GENDER:
			$field = "gender";
			break;
		case GRAPH_YEAR:
			$field = "class_year";
			break;
	}

	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		$val_st = prepare_stmt("SELECT $field, value FROM results
			JOIN instance ON instance_fk=instance_id
			JOIN class ON class_fk=class_id
			JOIN student ON student_fk=student_id 
			WHERE test_fk=?
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			ORDER BY $field, value ASC");
		
		if($user != "")
			$val_st->bind_param("iiii", $id, $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$val_st->bind_param("iii", $id, $cond['year1'], $cond['year2']);
	}
	else
	{
		$val_st = prepare_stmt("SELECT $field, value FROM results
			JOIN instance ON instance_fk=instance_id
			JOIN class ON class_fk=class_id
			JOIN student ON student_fk=student_id 
			WHERE test_fk=?
			ORDER BY $field, value ASC");
		$val_st->bind_param("i", $id);
	}

	$ret = execute_stmt($val_st);
	$val_st->close();

	while($row = $ret->fetch_assoc())
		$graph[$row[$field]][] = $row['value'];
	
	return $graph;
}

// Functions for the correlation section

// Function to open the global statement to get values of two tests
function open_rvals_stmt($cond = null)
{
	global $r_id1;
	global $r_id2;
	global $rval_st;

	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		$rval_st = prepare_stmt("SELECT P1.value AS v1, P2.value AS v2
			FROM results AS P1 JOIN results AS P2 ON P1.instance_fk=P2.instance_fk
			JOIN instance ON P1.instance_fk=instance_id
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk=class_id 
			WHERE P1.test_fk=? AND P2.test_fk=?
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user");

		if($user != "")
			$rval_st->bind_param("iiiii", $r_id1, $r_id2, $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$rval_st->bind_param("iiii", $r_id1, $r_id2, $cond['year1'], $cond['year2']);
	}
	else
	{
		$rval_st = prepare_stmt("SELECT P1.value AS v1, P2.value AS v2
			FROM results AS P1 JOIN results AS P2 ON P1.instance_fk=P2.instance_fk
			WHERE P1.test_fk=?
			AND P2.test_fk=?");
		$rval_st->bind_param("ii", $r_id1, $r_id2);
	}

	return;
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
	if($r['n'] > CORRELATION_THRESH)
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

	$r['color'] = correlation_color($r['r']);

	return $r;
}

// Function to get tests with a significant number of result (for correlation)
function get_test_correlation($cond = null)
{	
	$threshold = CORRELATION_THRESH;
	$test_st = prepare_stmt("SELECT test_id, test_name, positive_values FROM test 
		WHERE test_id IN (SELECT test_fk FROM results GROUP BY test_fk HAVING COUNT(*) > ?) 
		ORDER BY test_name");
	$test_st->bind_param("i", $threshold);
	$res = execute_stmt($test_st);
	$test_st->close();

	$ret['names'] = [];
	$ret['positive'] = [];
	$ret['statistics'] = [];
	$ret['list'] = "-1";

	if($res->num_rows == 0)
		return $ret;
	
	while($row = $res->fetch_assoc())
	{
		$ret['names'][$row['test_id']] = $row['test_name'];
		$ret['positive'][$row['test_id']] = $row['positive_values'];
		$ret['statistics'][$row['test_id']] = get_stats($row['test_id'], $cond);
		$ret['list'] .= ", ".$row['test_id'];
	}
	return $ret;
}

// Function to get the data for the scatter plot matrix
function test_graph($testlist, $cond = null)
{
	// Gets each test's unit
	$unit_st = prepare_stmt("SELECT test_name, symbol FROM test
		JOIN unit ON unit_fk=unit_id
		WHERE test_id IN($testlist)");
	$unit_r = execute_stmt($unit_st);
	$unit_st->close();
	while($row = $unit_r->fetch_assoc())
		$unit[$row['test_name']] = $row['symbol'];

	// Builds the query to get all results for given tests
	if($cond)
	{
		$classlist = $cond['class'];
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		$splom_st = prepare_stmt("SELECT test_name, instance_fk, value FROM results 
			JOIN test ON test_fk=test_id
			JOIN instance ON instance_fk=instance_id
			JOIN class ON class_fk=class_id
			JOIN student ON student_fk=student_id 
			WHERE test_fk IN ($testlist)
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			ORDER BY test_name, instance_fk");

		if($user != "")
			$splom_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
		else
			$splom_st->bind_param("ii", $cond['year1'], $cond['year2']);
	}
	else
	{
		$splom_st = prepare_stmt("SELECT test_name, instance_fk, value 
			FROM results JOIN test ON test_fk=test_id
			WHERE test_fk IN ($testlist) ORDER BY test_name, instance_fk");
	}
	
	$splomret = execute_stmt($splom_st);
	$previnst = -1;
	$instances = [];
	$splom = [];
	while($splomrow = $splomret->fetch_assoc())
	{
		// Builds a table such as
		// instance_id | test_id | val
		// with empty val entries if needed
		$splom[$splomrow['test_name']][$splomrow['instance_fk']] = $splomrow['value'];

		if($previnst != $splomrow['instance_fk'])
		{
			$previnst = $splomrow['instance_fk'];
			$instances[] = $splomrow['instance_fk'];
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

// Function to get a color based on thr correlation coefficient
function correlation_color($r)
{
	if($r == "-")
		 return "rgba(0, 0, 0, 0.05)";
	return "rgba(23, 147, 255, ".min(number_format(abs($r), 2), 1).")";
}
?>
