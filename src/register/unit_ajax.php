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

// Ajax script to return unit of measure's information given the test
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
if(!chk_access(PROFESSOR, false))
{
	echo "null";
	exit;
}
connect();

$data['simbolo'] = "";
$data['passo'] = "";

$unit_st = prepare_stmt("SELECT simbolo, passo FROM UNITA JOIN TEST ON fk_udm=id_udm
	JOIN TIPOTEST ON fk_tipot=id_tipot 
	WHERE id_test=?");
$unit_st->bind_param("i", $_GET['test']);

$udm = execute_stmt($unit_st);
$unit_st->close();
if($udm->num_rows > 0)
	$data = $udm->fetch_assoc();

echo json_encode($data);
?>
