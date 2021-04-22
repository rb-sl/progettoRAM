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

// Back end script to answer to ajax queries on correlation statistics
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
if(!chk_access(RESEARCH, false))
{
	echo "null";
	exit;
}
connect();

$cond = cond_builder();
open_rvals_stmt($cond);

// Computes the matrix
$testinfo = get_test_correlation($cond);
$test = $testinfo['names'];
$positive = $testinfo['positive'];
$stats = $testinfo['statistics'];
$testlist = $testinfo['list'];

foreach($test as $idc => $colname)
	foreach($test as $idr => $rowname)
		if($rowname <= $colname)
		{
			// Simmetric construction
			$data['matrix'][$idc][$idr] = calc_r($idc, $stats[$idc], $idr, $stats[$idr], $cond);
			$data['matrix'][$idr][$idc] = $data['matrix'][$idc][$idr];
		}

$data['testInfo'] = test_graph($testlist, $cond);
$rval_st->close();

echo json_encode($data)
?>
