<?php
// Front end page to allow admins to correct wrong student instances
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Controllo istanze di studenti inconsistenti");
?>

<h2>Correzione errori su istanze di studenti</h2>

<h3>Studenti registrati contemporaneamente a più classi</h3>
<?php
$err_st = prepare_stmt("SELECT fk_stud FROM ISTANZE
	JOIN CLASSI ON fk_cl=id_cl 
	WHERE anno=? 
	GROUP BY fk_stud 
	HAVING COUNT(*)>1");
$err_st->bind_param("i", $cur_year);
?>

<form action="student_merge.php" method="POST" class="flexform marginunder">
	<h3>
		Unione profili studenti
		<input type="reset" id="m_clear" class="btn btn-warning marginunder jQhidden" value="Annulla">
	</h3>
	<div id="m_stage0">
		<div class="flexrow">
			<div>
				Unisci studenti tramite:
			</div>

			<div class="form-check">
				<input id="m_id" class="form-check-input" type="radio" name="method" value="id">
				<label class="form-check-label" for="m_id">Id studente</label>
			</div>

			<div class="form-check">
				<input id="m_name" class="form-check-input" type="radio" name="method" value="name">
				<label class="form-check-label" for="m_name">Cognome e nome</label>
			</div>
		</div>

		<div id="m_stage1" class="jQhidden">
			<div class="flexrow marginunder">
				<div class="flexform">
					Studente 1:
					<div class="src_id jQhidden">
						<input type="number" class="form-control" id="m_id1" name="m_id1" placeholder="Id">
					</div>

					<div class="src_name jQhidden">
						<input type="text" class="form-control" id="m_surname1" name="surname1" placeholder="Cognome">
						<input type="text" class="form-control" id="m_name1" name="name1" placeholder="Nome">
					</div>
				</div>
				<div class="flexform">
					Studente 2:
					<div class="src_id jQhidden">
						<input type="number" class="form-control" id="m_id2" name="m_id2" placeholder="Id">
					</div>

					<div class="src_name jQhidden">
						<input type="text" class="form-control" id="m_surname2" name="surname2" placeholder="Cognome">
						<input type="text" class="form-control" id="m_name2" name="name2" placeholder="Nome">
					</div>
				</div>
			</div>

			<button type="button" id="m_src" class="btn btn-primary search marginunder">Cerca</button>
		</div>
	</div>

	<div id="m_stage2" class="jQhidden">
		<div class="flexrow marginunder">
			<div id="m_info1" class="flexform">
			</div>
			<div id="m_info2" class="flexform">
			</div>
		</div>

		<button type="button" id="m_go" class="btn btn-primary search marginunder">Continua</button>
	</div>

	<div id="m_stage3" class="jQhidden">
		<div id="m_choose" class="flexform marginunder">
			Mantieni anagrafica: 
			<div class="flexrow">
				<div class="form-check flexrow studentcard">
					<input id="keep1" class="form-check-input" type="radio" name="keep" value="1" required>
					<label class="form-check-label" for="keep1">Studente 1</label>
				</div>
				<div class="form-check flexrow studentcard">
					<input id="keep2" class="form-check-input" type="radio" name="keep" value="2" required>
					<label class="form-check-label" for="keep2">Studente 2</label>
				</div>
			</div>
		</div>

		<input type="submit" class="btn btn-primary marginunder" value="Unisci">
	</div>
</form>


<?php
$err_st = prepare_stmt("SELECT fk_stud FROM ISTANZE
	JOIN CLASSI ON fk_cl=id_cl 
	WHERE anno=? 
	GROUP BY fk_stud 
	HAVING COUNT(*)>1");
$err_st->bind_param("i", $cur_year);

$show_st = prepare_stmt("SELECT * FROM ISTANZE
	JOIN CLASSI ON fk_cl=id_cl 
	WHERE fk_stud=? 
	AND anno=? 
	ORDER BY anno");
$show_st->bind_param("ii", $stud, $cur_year);
?>

<script src="/admin/js/student.js"></script>
<?php show_postmain(); ?>
