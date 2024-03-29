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

// This file contains a collection of functions used in the register section of
// the application

// Function to retrieve a class's data or show an error if it does not exist
function get_class_info($id)
{
	$class_st = prepare_stmt("SELECT * FROM class WHERE class_id=?");
	$class_st->bind_param("i", $id);
	$ret = execute_stmt($class_st);

	if($ret->num_rows == 0)
	{
		set_alert("Errore: Classe inesistente");
		header("Location: /register/register.php");
		exit;
	}

	$class_st->close();
	return $ret->fetch_assoc();
}

// Construction of the list of students to confirm their presence in a class
function build_chk_table($class, $prom = false)
{
	$table = "<div class='tdiv'>
			<div class='innerx'>
				<table id='tabchk' class='table table-light table-striped studtable'>";
	
	// If this is the modification due to promotion, students already promoted to 
	// other classes will not be shown
	if($prom)
	{
		$year = get_current_year();

		$stud_st = prepare_stmt("SELECT student_id, firstname, lastname, gender 
			FROM student JOIN instance ON student_id=student_fk
			WHERE class_fk=? 
			AND student_id NOT IN (
				SELECT DISTINCT(student_fk) FROM instance 
				JOIN class ON class_fk=class_id 
				WHERE class_year=?
			) ORDER BY lastname, firstname");
		$stud_st->bind_param("ii", $class, $year);
	}
	else
	{
		$stud_st = prepare_stmt("SELECT student_id, firstname, lastname, gender 
			FROM student JOIN instance ON student_id=student_fk
			WHERE class_fk=?
			ORDER BY lastname, firstname");
		$stud_st->bind_param("i", $class);
	}
		
	$ret = execute_stmt($stud_st);
	$stud_st->close();

	while($row = $ret->fetch_assoc())
	{
		$table .= "<tr>
			<td class='containerflex'>
				<div class='form-check'>
					<input type='checkbox' id='c".$row['student_id']."' name='pr[]' value='"
						.$row['student_id']."' class='form-check-input chkpro' checked='true'>
					<label id='lbl".$row['student_id']."' class='form-check-label' for='c"
						.$row['student_id']."'></label>
				</div>
			</td>
			<td><label for='c".$row['student_id']."'>".htmlentities($row['lastname'])."</label></td>
			<td><label for='c".$row['student_id']."'>".htmlentities($row['firstname'])."</label></td>
			<td><label for='c".$row['student_id']."'>".htmlentities(strtoupper($row['gender']))."</label></td>
		</tr>";
	}
	$table .= "</table>
			</div>
		</div>";

	return $table;
}

// Function to build the students's name column (for visualization purposes)
function col_stud()
{
	$stud_st = prepare_stmt("SELECT instance_id, student_id, firstname, lastname, gender 
		FROM student JOIN instance ON student_fk=student_id
		WHERE class_fk=? 
		ORDER BY lastname, firstname ASC");
	$stud_st->bind_param("i", $_GET['id']);

	$retstud = execute_stmt($stud_st);
	
	// Counter for the rows' color
	$i = 0;
	$rstud = [];
	while($row = $retstud->fetch_assoc())
	{
		if($i % 2 == 0)
  			$cl = "evenrow";
  		else
			$cl = "oddrow";

		$rstud[$row['instance_id']]['strow'] = "<tr id='tr".$row['instance_id']."' class='dat tdr'>
			<td id='st".$row['instance_id']."' class='leftfix $cl'>
				<div><a href='student_show.php?id=".$row['student_id']
					."' class='resizetext' title=\"".htmlentities($row['lastname']." ".$row['firstname'])
					." (".htmlentities(strtoupper($row['gender'])).")\" tabindex='-1'>"
					.htmlentities($row['lastname'])." ".(isset($row['firstname'][0]) ? $row['firstname'][0]."." : "")
					."</a></div>
			</td>";
		$i++;
	}
	
	return $rstud;
}

// Function to get classes associated to a student. If the user does not own
// at least one class containing the student no data is returned 
function col_class($stud)
{
	$cl_st = prepare_stmt("SELECT class_id, class, section, class_year, user_fk
		FROM results RIGHT JOIN instance ON instance_fk=instance_id
		JOIN class ON class_fk=class_id
		WHERE student_fk=?
		GROUP BY class_id 
		ORDER BY class_year");
	$cl_st->bind_param("i", $stud);
	$ret = execute_stmt($cl_st);
	$cl_st->close();

	$auth = false;
	$rclass = [];
	$i = 0;
	while($row = $ret->fetch_assoc())
	{
		if($i % 2 == 0)
  			$cl = "evenrow";
  		else
			$cl = "oddrow";

		if($row['user_fk'] == $_SESSION['id'] or chk_auth(ADMINISTRATOR))
		{
			$slnk = "<a href='./class_show.php?id=".$row['class_id']."'>";
			$elnk = "</a>";
			$auth = true;
		}
		else
		{
			$slnk = "";
			$elnk = "";
		}
		
		$rclass[$row['class_id']]['name'] = $row['class'].htmlentities($row['section'])." ".$row['class_year']
			."/".($row['class_year'] + 1);
		
		$rclass[$row['class_id']]['clrow']  = "<tr id='tr".$row['class_id']."' class='dat tdr'>
			<td id='st".$row['class_id']."' class='leftfix $cl'>"
			.$slnk.$row['class'].htmlentities($row['section'])." ".$row['class_year']."/"
			.($row['class_year'] + 1).$elnk."</td>";
		$i++;
	}

	if(!$auth and !chk_auth(ADMINISTRATOR))
		return null;
	return $rclass;
}

// Gets the test list and their number
function get_test($id, $forstud = false)
{
	// The query is built slightly different if for students
	if(!$forstud)
		$stat_st = prepare_stmt("SELECT test_id, test_name, symbol, positive_values,
			MIN(date) AS date, ROUND(AVG(value), 2) AS avg
			FROM test JOIN results ON test_fk=test_id
			JOIN instance ON instance_fk=instance_id
			JOIN unit ON unit_fk=unit_id
			WHERE class_fk=?  
			GROUP BY test_id
			ORDER BY date, test_id ASC");
	else
		$stat_st = prepare_stmt("SELECT test_id, test_name, symbol, positive_values,
			date, ROUND(AVG(value), 2) AS avg
			FROM test JOIN results ON test_fk=test_id
			JOIN instance ON instance_fk=instance_id
			JOIN unit ON unit_fk=unit_id
			WHERE student_fk=?  
			GROUP BY test_id
			ORDER BY test_name ASC");

	$stat_st->bind_param("i", $id);
	$ret = execute_stmt($stat_st);
	$stat_st->close();

	return $ret;	
}

// Function to insert or update students of a class
function class_students($isupdate, $class, $precedent, $newln, $newfn, $gnd, $external)
{
	global $mysqli;

	$inst_st = prepare_stmt("INSERT INTO instance(student_fk, class_fk) VALUES(?, ?)");
	$inst_st->bind_param("ii", $ids, $class);

	$newstud_st = prepare_stmt("INSERT INTO student(lastname, firstname, gender) VALUES(?, ?, ?)");
	$newstud_st->bind_param("sss", $lastname, $firstname, $gender);

	$log = "";

	// Inserts the promoted students (on insert)
	// or builds the list of students still in the class (on update)
	$idlist = "-1";
	if(isset($precedent))
		if($isupdate)
			foreach($precedent as $ids)
				$idlist .= ",".$ids;
		else
			foreach($precedent as $ids)
			{
				execute_stmt($inst_st);
				$log .= "\n>>> Promosso: $ids";
			}
	
	// Inserts new students
	if(isset($newln))
		foreach($newln as $i => $ln)
		{
			$lastname = maiuscolo($ln);
			$firstname = maiuscolo($newfn[$i]);
			$gender = $gnd[$i];
			
			execute_stmt($newstud_st);
			$ids = $mysqli->insert_id;
			$idlist .= ",".$ids;

			execute_stmt($inst_st);
			$log .= "\n>>> Nuovo: $ids";
		}

	// Creation or update of students possibly already registered
	if(isset($external))
		foreach($external as $dat => $ids)
		{
			$info = explode($dat, "_"); 
			if($ids == "new")
			{
				$lastname = maiuscolo($info[0]);
				$firstname = maiuscolo($info[1]);
				$gender = $info[2];
				
				execute_stmt($newstud_st);
				$ids = $mysqli->insert_id;
				
				execute_stmt($inst_st);
				$log .= "\n>>> Nuovo: $ids";
			}
			else
			{
				execute_stmt($inst_st);
				$log .= "\n>>> Promosso: $ids";
			}

			$idlist .= ",".$ids;
		}

	$inst_st->close();
	$newstud_st->close();

	if($log != "")
		writelog("Studenti nella classe $class:".$log);

	return $idlist;
}

// Returns the correct color based on percentiles or z-values
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
		$color_index = floor(abs($val) * 10);
		if($color_index == 0)
			return "";

		$color_index = max(min(floor($val * 10), 20), -20);
		return $color[ceil($color_index / 5) + 4];
	}
}

// Returns the correct color based on the grade
function color_from_grade($val, $color)
{
	if($val == "-")
		return "";

	$color_index = ceil(floor($val * 10) / 5) * 5;

	return $color[$color_index];
}

// Calculates average and median rows and columns.
// $isperc indicates whether the values are from percentiles or standards
function get_avgmed($class, $vals, $isperc, $forstud = false)
{
	$ret['avg'] = [];
	$ret['med'] = [];
	$ret['savg'] = [];
	$ret['smed'] = [];
	$ret['tavg'] = [];

	if($isperc)
		$color = get_color_prc();
	else
		$color = get_color_std();

	$testinfo = get_tests($class, $forstud);

	if($testinfo === null)
		return $ret;

	$idtest = $testinfo[0];

	// Tests' averages and medians
	$sum = 0;
	$enablesum = false;
	foreach($idtest as $id)
	{
		// Test's average and color
		$ret['avg'][$id]['val'] = arr_avg(array_column($vals, $id), 5);
		$ret['avg'][$id]['color'] = color_from_val($ret['avg'][$id]['val'], $color, $isperc);

		// Sum for the total average
		if($ret['avg'][$id]['val'] != "-")
		{
			$sum += $ret['avg'][$id]['val'];
			$enablesum = true;
		}
	
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
	
	// Total percentile average	and color
	if($enablesum)
		$ret['tavg']['val'] = number_format($sum / sizeof($idtest), 5);
	else
		$ret['tavg']['val'] = "-";

	$ret['tavg']['color'] = color_from_val($ret['tavg']['val'], $color, $isperc);

	return $ret;
}

// Calculates averages for grades based on the quarter
function get_avgmed_grades($class, $rstud, $forstud = false)
{
	$ret['avg'] = [];
	$ret['med'] = [];
	$ret['savg'] = [];
	$ret['smed'] = [];
	$ret['tavg'] = [];

	$color = get_color_gr();

	$testinfo = get_tests($class, $forstud);

	if($testinfo === null)
		return $ret;

	$idtest = $testinfo[0];

	$vals = $rstud['val'];
	$dates = $rstud['date'];

	// Tests' averages and medians
	$sum = 0;
	foreach($idtest as $id)
	{
		// Test's average and color
		$ret['avg'][$id]['val'] = arr_avg(array_column($vals, $id), 2);
		$ret['avg'][$id]['color'] = color_from_grade($ret['avg'][$id]['val'], $color);

		// Sum for the total average
		if($ret['avg'][$id]['val'] != "-")
			$sum += $ret['avg'][$id]['val'];
	
		// Test's median and color
		$ret['med'][$id]['val'] = arr_med(array_column($vals, $id), 2);
		$ret['med'][$id]['color'] = color_from_grade($ret['med'][$id]['val'], $color);
	}

	// Students' averages divided for first and second quarter
	// savg and smed are used respectively
	foreach($vals as $ids => $arr)
	{
		$c1 = 0;
		$c2 = 0;
		$q1 = 0;
		$q2 = 0;
		foreach($arr as $idt => $val)
		{
			if(date("m", strtotime($dates[$ids][$idt])) > 8)
			{	
				$c1++;
				$q1 += $val;
			}
			else
			{
				$c2++;
				$q2 += $val;
			}	
		}

		// First quarter average
		if($c1 != 0)
		{
			$ret['savg'][$ids]['val'] = number_format($q1 / $c1, 2);
			$ret['savg'][$ids]['color'] = color_from_grade($ret['savg'][$ids]['val'], $color);
		}
		else
		{
			$ret['savg'][$ids]['val'] = "-";
			$ret['savg'][$ids]['color'] = "";
		}

		// Second quarter average
		if($c2 != 0)
		{
			$ret['smed'][$ids]['val'] = number_format($q2 / $c2, 2);
			$ret['smed'][$ids]['color'] = color_from_grade($ret['smed'][$ids]['val'], $color);
		}
		else
		{
			$ret['smed'][$ids]['val'] = "-";
			$ret['smed'][$ids]['color'] = "";
		}
	}
	
	// Total average
	if($sum != 0)
		$ret['tavg']['val'] = number_format($sum / sizeof($idtest), 2);
	else
		$ret['tavg']['val'] = "-";

	$ret['tavg']['color'] = color_from_grade($ret['tavg']['val'], $color);

	return $ret;
}

// Function to read colors for percentiles
function get_color_prc()
{
	$color_st = prepare_stmt("SELECT * FROM grading 
		JOIN grade ON grade_fk=grade_id 
		WHERE user_fk=?");
	$color_st->bind_param("i", $_SESSION['id']);
	$ret_gr = execute_stmt($color_st);
	$color_st->close();
	
	while($row = $ret_gr->fetch_assoc())
		$color[$row['percentile']] = $row['color'];

	return $color;
}

// Function to elaborate colors wrt standard values
function get_color_std()
{
	// Gets only 6 colors
	$color_st = prepare_stmt("SELECT * FROM grade 
		WHERE grade NOT IN(6.5, 8.5, 9, 9.5) ORDER BY grade");
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

// Function to obtain color based on grades
function get_color_gr()
{
	$grade_st = prepare_stmt("SELECT * FROM grade");
	$ret_gr = execute_stmt($grade_st);
	$grade_st->close();

	while($row = $ret_gr->fetch_assoc())
		$color[$row['grade'] * 10] = $row['color'];

	return $color;
}

// Returns the list of test ids done by the class, and their positive values
function get_tests($id, $forstud = false)
{
	if($forstud)
	{
		$restr = "student_fk";
		$order = "ORDER BY test_name";
	}
	else
	{
		$restr = "class_fk";
		$order = "";
	}
	$ctst_st = prepare_stmt("SELECT test_id, step, positive_values FROM test 
		JOIN datatype ON datatype_fk=datatype_id
		WHERE test_id IN (
			SELECT DISTINCT(test_fk) FROM results JOIN instance ON instance_fk=instance_id 
			WHERE $restr=?
		)
		$order");
	$ctst_st->bind_param("i", $id);
	$ctst = execute_stmt($ctst_st);
	$ctst_st->close();

	if($ctst->num_rows == 0)
		return null;

	while($row = $ctst->fetch_assoc())
	{
		$testlist[] = $row['test_id'];
		
		// Set to true if greater values correspond to a better performance
		$positive[$row['test_id']] = ($row['positive_values'] == GREATER);

		$step[$row['test_id']] = $row['step'];
	}

	return array($testlist, $positive, $step);
}

// Function to obtain the percentiles of a class
// The structure with multiple queries was chosen as it improves greatly the
// Execution time (~0.2s) wrt bigger nested queries (~0.6s) such as
//  SELECT instance_fk, date, (
//      SELECT COUNT(*) FROM results
//          WHERE test_fk=? 
//      	AND value<=(SELECT value FROM results WHERE instance_fk=P.instance_fk AND test_fk=?)
//      ) * 100 / (SELECT COUNT(*) FROM results WHERE test_fk=?) AS percentile 
//  FROM results P
//  WHERE instance_fk IN (SELECT instance_id FROM instance WHERE class_fk=?)
//  AND test_fk=?;
// Moreover, structure with only one query to the database for each test instead
// of one for each value (using a parallel scan of the result) permits to lower
// execution time to ~0.02s
function get_perc($class, $cond = null, $forstud = false)
{
	$rstud['val'] = [];
	$rstud['date'] = [];
	$rstud['color'] = [];

	$color = get_color_prc();

	$testinfo = get_tests($class, $forstud);

	if($testinfo === null)
		return $rstud;

	if($forstud)
	{
		$select = "class_fk";
		$where = "student_fk";
	}
	else
	{
		$select = "instance_fk";
		$where = "class_fk";
	}

	$testlist = "0";
	foreach($testinfo[0] as $id)
		$testlist .= ", $id";

	$step = $testinfo[2];
	$positive = $testinfo[1];

	if($cond)
	{
		if(!class_in_years($class, $cond['year1'], $cond['year2']))
			return $rstud;

		$classlist = $cond['class'];
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		// Statement to get the count of tests only on results relevant to the user's selection
		$count_st = prepare_stmt("SELECT test_fk, COUNT(*) AS n FROM results 
			JOIN instance ON instance_fk=instance_id
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk = class_id 
			WHERE test_fk IN ($testlist)
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			GROUP BY test_fk");

		// Statement to get only the results of a class relevant to the user's selection
		$class_st = prepare_stmt("SELECT $select AS header, date, value FROM results 
			JOIN instance ON instance_fk=instance_id 
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk=class_id
			WHERE test_fk=? AND $where=?
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			ORDER BY test_fk ASC, value ASC");

		// Statement to get the count for each value
		$values_st = prepare_stmt("SELECT value, COUNT(*) AS percentile FROM results 
			JOIN instance ON instance_fk=instance_id 
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk=class_id
			WHERE test_fk=?
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			GROUP BY value 
			ORDER BY value ASC");

		// Binding is done based on the presence of the restriction of the professor
		if($cond['user'] != "")
		{
			$count_st->bind_param("iii", $cond['year1'], $cond['year2'], $_SESSION['id']);
			$class_st->bind_param("iiiii", $test, $class, $cond['year1'], $cond['year2'], $_SESSION['id']);
			$values_st->bind_param("iiii", $test, $cond['year1'], $cond['year2'], $_SESSION['id']);
		}
		else
		{	
			$count_st->bind_param("ii", $cond['year1'], $cond['year2']);
			$class_st->bind_param("iiii", $test, $class, $cond['year1'], $cond['year2']);
			$values_st->bind_param("iii", $test, $cond['year1'], $cond['year2']);
		}
	}
	else
	{
		// Statement to get the number of results for each test done by the class
		$count_st = prepare_stmt("SELECT test_fk, COUNT(*) AS n FROM results 
			WHERE test_fk IN ($testlist) 
			GROUP BY test_fk");
		
		// Statement to get the values of a class
		$class_st = prepare_stmt("SELECT $select AS header, date, value FROM results 
			JOIN instance ON instance_fk=instance_id 
			WHERE test_fk=? 
			AND $where=? 
			ORDER BY test_fk ASC, value ASC");
		$class_st->bind_param("ii", $test, $class);

		// Statement to get the count for each test
		$values_st = prepare_stmt("SELECT value, COUNT(*) AS percentile FROM results 
			WHERE test_fk=? 
			GROUP BY value 
			ORDER BY value ASC");
		$values_st->bind_param("i", $test);
	}
	
	// Gets the total count of tests done by the class
	$cnt = execute_stmt($count_st);
	$count_st->close();
	
	$empty = true;
	while($row = $cnt->fetch_assoc())
	{
		$empty = $empty && ($row['n'] == 0);
		$count[$row['test_fk']] = $row['n'];
	}

	// If no rows are returned the function ends
	if($empty)
	{
		$class_st->close();
		$values_st->close();
		
		return $rstud;
	}
	
	foreach($positive as $test => $greater)
	{
		$vals = execute_stmt($class_st);
		$tests = execute_stmt($values_st);

		$cur_count = 0;
		$prevval = null;
		
		// Initialization for the test scan
		$t = $tests->fetch_assoc();

		// Outer scan for student results
		while($val = $vals->fetch_assoc())
		{
			$curval = $val['value'];
			$instance = $val['header'];

			// When the student value changes, the test data is scanned
			// until the new value is reached, while counting the total
			// number of results
			if($curval !== $prevval)
			{
				// The difference for greater and lower values is done
				// so that in both cases the percentile calculation takes
				// in account up to the student's value
				if($greater)
					while($t !== null and $t['value'] <= $curval)
					{
						$cur_count += $t['percentile'];
						$t = $tests->fetch_assoc();
					}
				else
					while($t !== null and $t['value'] < $curval)
					{
						$cur_count += $t['percentile'];
						$t = $tests->fetch_assoc();
					}

				$prevval = $curval;
			}				

			// Percentile calculation
			if($greater)
				$p = $cur_count;
			else
				$p = $count[$test] - $cur_count;
				
			$percentile = number_format(($p / $count[$test]) * 100, 5);
			$rstud['val'][$instance][$test] = $percentile;
			$rstud['date'][$instance][$test] = $val['date'];
			$rstud['color'][$instance][$test] = color_from_val($percentile, $color, true);
		}
	}

	$class_st->close();
	$values_st->close();

	return $rstud;
}

// Function to get the standardized values for a class
function get_std($class, $cond = null, $forstud = false)
{
	$rstud['val'] = [];
	$rstud['date'] = [];
	$rstud['color'] = [];

	$color = get_color_std();

	$testinfo = get_tests($class, $forstud);

	if($testinfo === null)
		return $rstud;

	if($forstud)
	{
		$select = "class_fk";
		$where = "student_fk";
	}
	else
	{
		$select = "instance_fk";
		$where = "class_fk";
	}

	$testlist = "0";
	foreach($testinfo[0] as $id)
		$testlist .= ", $id";

	$positive = $testinfo[1];	

	if($cond)
	{
		if(!class_in_years($class, $cond['year1'], $cond['year2']))
			return null;
		
		$classlist = $cond['class'];
		$genderlist = $cond['gender'];
		$user = $cond['user'];

		// Statement to get the class's tests and their average and standard deviation, only for sets 
		// defined by the user
		$ctst_st = prepare_stmt("SELECT test_fk, AVG(value) AS avg, STD(value) AS std FROM results 
			JOIN instance ON instance_fk=instance_id
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk=class_id
			WHERE test_fk IN ($testlist)
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user
			GROUP BY test_fk");

		// Statement to get the class's results that fall in the categories selected by the user
		$res_st = prepare_stmt("SELECT $select AS header, test_fk, value FROM results 
			JOIN instance ON instance_fk=instance_id 
			JOIN student ON student_fk=student_id 
			JOIN class ON class_fk=class_id
			WHERE $where=?
			AND class_year BETWEEN ? AND ?
			AND class IN (0 $classlist)
			AND gender IN ('x' $genderlist)
			$user");
		
		if($cond['user'] != "")
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
		$ctst_st = prepare_stmt("SELECT test_fk, AVG(value) AS avg, STD(value) AS std FROM results 
			WHERE test_fk IN ($testlist)
			GROUP BY test_fk");

		// Statement to get the class's results
		$res_st = prepare_stmt("SELECT $select AS header, test_fk, value FROM results 
			JOIN instance ON instance_fk=instance_id 
			WHERE $where=?");
		$res_st->bind_param("i", $class);
	}
	
	$ctst = execute_stmt($ctst_st);
	$ctst_st->close();

	while($row = $ctst->fetch_assoc())
	{
		$avg[$row['test_fk']] = $row['avg'];
		$std[$row['test_fk']] = $row['std'];
	}

	$ret_res = execute_stmt($res_st);
	$res_st->close();

	while($row = $ret_res->fetch_assoc())
	{
		if($std[$row['test_fk']] != 0)
			$z = ($row['value'] - $avg[$row['test_fk']]) / $std[$row['test_fk']];
		else
			$z = 0;

		// Inverts the sign if to a better perfomance corresponds a lower value
		if(!$positive[$row['test_fk']])
			$z *= -1;
		
		$rstud['val'][$row['header']][$row['test_fk']] = number_format($z, 5);
		$rstud['color'][$row['header']][$row['test_fk']] = color_from_val($z, $color, false);
	}

	return $rstud;
}

// Function to get grades date and their color
function get_grades($class, $cond = null, $forstud = false)
{
	$gr_st = prepare_stmt("SELECT * FROM grade JOIN grading ON grade_fk=grade_id WHERE user_fk=?");
	$gr_st->bind_param("i", $_SESSION['id']);
	$ret = execute_stmt($gr_st);
	$gr_st->close();

	while($row = $ret->fetch_assoc())
		$grades[$row['percentile']] = $row['grade'];

	$rstud_perc = get_perc($class, $cond, $forstud);

	if($rstud_perc == null)
		return null;

	$rstud['date'] = $rstud_perc['date'];
	$rstud['color'] = $rstud_perc['color'];
	$rstud['val'] = [];

	foreach($rstud_perc['val'] as $instance => $arr)
		foreach($arr as $test => $val)
		{
			$base = floor($val);
			while(!isset($grades[$base]))
				$base++;
			$rstud['val'][$instance][$test] = $grades[$base];  
		}

	return $rstud;
}

// Function to check if a class is between the given years
function class_in_years($class, $year1, $year2)
{	
	$chk_st = prepare_stmt("SELECT * FROM class 
		WHERE class_id = ? 
		AND class_year BETWEEN ? AND ?");
	$chk_st->bind_param("iii", $class, $year1, $year2);	
	$ret = execute_stmt($chk_st);
	$chk_st->close();
	
	return $ret->num_rows == 1;
}

// Function to check whether a value belongs to the acceptance interval of a test.
// The function avg +- 10stddev is used as, per the Chebychev Inequality, 
// for each k at least the (1-1/(k^2))-th fraction of data falls in the interval avg +- k*std.
// With k = 10, 99% of the data is accepted
function is_accettable($test, $val)
{
	$chk_st = prepare_stmt("SELECT COUNT(*) AS n, AVG(value) AS avg, STD(value) AS std FROM results WHERE test_fk=?");
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

// Shows the modification form for a class
function show_cl_form($cl = 0, $section = "", $year = null)
{
	// Selection of the current class
	$nc = "";
	$c1 = "";
	$c2 = "";
	$c3 = "";
	$c4 = "";
	$c5 = "";
	switch($cl)
	{
		case 0:
			$nc = "<option selected disabled></option>";
			break;
		case 1:
			$c1 = " selected";
			break;
		case 2:
			$c2 = " selected";
			break;
		case 3:
			$c3 = " selected";
			break;
		case 4:
			$c4 = " selected";
			break;
		case 5:
			$c5 = " selected";
			break;		
	}

	// Construction of the year if not given
	if($year === null)
		$year = get_current_year();

	echo "Classe: 
		<select id='cl' class='form-control' name='cl' required>
			$nc
			<option value='1'$c1>Prima</option>
			<option value='2'$c2>Seconda</option>
			<option value='3'$c3>Terza</option>
			<option value='4'$c4>Quarta</option>
			<option value='5'$c5>Quinta</option>
  		</select> 
  	Sezione: <input type='text' id='section' class='smalltext' name='section' value='".htmlentities($section)."' required>
  	Anno: <input  type='text' id='year1' class='textright smalltext' name='class_year' value='$year' required>/<span id='year2'>"
	  .($year + 1)."</span>";
	
	return;
}
?>
