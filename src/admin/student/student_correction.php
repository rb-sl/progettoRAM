<?php
// Front end page to allow admins to correct wrong student instances
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Controllo istanze di studenti inconsistenti");
?>

<h2>Correzione profili degli studenti</h2>

<form action="student_merge.php" method="POST" class="flexform marginunder">
	<h3>
		Unione profili studenti
		<input type="reset" id="m_clear" class="btn btn-warning marginunder clear jQhidden" value="Annulla">
	</h3>
	<div id="m_stage0">
		<div class="flexrow">
			<div>
				Unisci studenti tramite:
			</div>

			<div class="form-check">
				<input id="m_id" class="form-check-input method" type="radio" name="m_method" value="id">
				<label class="form-check-label" for="m_id">Id studente</label>
			</div>

			<div class="form-check">
				<input id="m_name" class="form-check-input method" type="radio" name="m_method" value="name">
				<label class="form-check-label" for="m_name">Cognome e nome</label>
			</div>
		</div>

		<div id="m_stage1" class="jQhidden">
			<div class="flexrow marginunder">
				<div class="flexform">
					Studente 1:
					<div class="m_src_id jQhidden">
						<input type="number" class="form-control" id="m_id1" name="m_id1" placeholder="Id">
					</div>

					<div class="m_src_name jQhidden">
						<input type="text" class="form-control" id="m_surname1" name="surname1" placeholder="Cognome">
						<input type="text" class="form-control" id="m_name1" name="name1" placeholder="Nome">
					</div>
				</div>
				<div class="flexform">
					Studente 2:
					<div class="m_src_id jQhidden">
						<input type="number" class="form-control" id="m_id2" name="m_id2" placeholder="Id">
					</div>

					<div class="m_src_name jQhidden">
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

		<button type="button" id="m_go" class="btn btn-primary go marginunder">Continua</button>
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

<form action="student_split.php" method="POST" class="flexform marginunder">
	<h3>
		Separazione profili studenti
		<input type="reset" id="s_clear" class="btn btn-warning clear marginunder jQhidden" value="Annulla">
	</h3>
	<div id="s_stage0">
		<div class="flexrow">
			<div>
				Cerca studente tramite:
			</div>

			<div class="form-check">
				<input id="s_id" class="form-check-input method" type="radio" name="s_method" value="id">
				<label class="form-check-label" for="s_id">Id studente</label>
			</div>

			<div class="form-check">
				<input id="s_name" class="form-check-input method" type="radio" name="s_method" value="name">
				<label class="form-check-label" for="s_name">Cognome e nome</label>
			</div>
		</div>

		<div id="s_stage1" class="jQhidden">
			<div class="flexrow marginunder">
				<div class="flexform">
					Studente:
					<div class="s_src_id jQhidden">
						<input type="number" class="form-control" id="s_id1" name="s_id1" placeholder="Id">
					</div>

					<div class="s_src_name jQhidden">
						<input type="text" class="form-control" id="s_surname1" name="surname1" placeholder="Cognome">
						<input type="text" class="form-control" id="s_name1" name="name1" placeholder="Nome">
					</div>
				</div>
			</div>

			<button type="button" id="s_src" class="btn btn-primary search marginunder">Cerca</button>
		</div>
	</div>

	<div id="s_stage2" class="jQhidden">
		<div class="flexrow marginunder">
			<div id="s_info" class="flexform marginunder">
			</div>
		</div>

		<button type="button" id="s_go" class="btn btn-primary go marginunder">Continua</button>
	</div>

	<div id="s_stage3" class="jQhidden">
		<div id="s_choose" class="flexrow marginunder">
			<div class="flexform">
				Studente originale:
				<div id="classlist1">
				</div>
			</div>
			<div class="flexform">
				Nuovo studente:
				<div id="classlist2">
				</div>
			</div>
		</div>

		<input type="submit" class="btn btn-primary marginunder" value="Separa">
	</div>
</form>

<script src="/admin/js/student.js"></script>
<?php show_postmain(); ?>
