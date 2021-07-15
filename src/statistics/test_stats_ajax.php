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

// Script to answer to ajax request about test data
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
if(!chk_access(RESEARCH, false))
{
	echo "null";
	exit;
}
connect();

$cond = cond_builder();

$upvals = get_stats($_GET['id'], $cond);
$rec = get_records($_GET['id'], $cond);

switch($_GET['graph'])
{
	case "val":
	case "box":
		$graph['plot'] = graph_vals($_GET['id'], $cond);
		break;
	case "prc":
		$graph['plot'] = graph_prc($_GET['id'], $cond);
		break;
	case "cbox":
		$graph['plot'] = graph_multibox($_GET['id'], GRAPH_CLASS, $cond);
		break;
	case "sbox":
		$graph['plot'] = graph_multibox($_GET['id'], GRAPH_GENDER, $cond);
		break;
	case "trend":
		$graph['plot'] = graph_trend($_GET['id'], $cond);
		break;
}

header("Content-Type: application/json");
echo json_encode(array_merge($upvals, $rec, $graph));
?>
