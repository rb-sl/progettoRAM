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

// Collection of functions related to the administrative section

// Symbol for unordered lists
const UNORDEREDLIST = "-";
// Symbol for ordered lists
const ORDEREDLIST = "#";

// Function to get the text and color associated to a
// privilege level
function get_privilege($privileges)
{
	switch($privileges)
	{
		case ADMINISTRATOR: 
			$ret['text'] = "Amministratore";
			$ret['color'] = "primarybg";
			break;
		case PROFESSOR_GRANTS: 
			$ret['text'] = "Professore (modifica test)";
			$ret['color'] = "infobg";
			break;
		case PROFESSOR: 
			$ret['text'] = "Professore";
			$ret['color'] = "successbg";
			break;
		case RESEARCH: 
			$ret['text'] = "Ricerca";
			$ret['color'] = "warningbg";
			break;
		case NONE:
			$ret['text'] = "Nessuno";
			$ret['color'] = "dangerbg";
			break;
		default:
			$ret = null;
			break;
	}
	return $ret;
}

// Function to know if a user can downgrade another one
function can_downgrade($id)
{
	global $dgrade_st;
	global $rec_id;

	// Query to retrieve the users whose status can be downgraded by the current one
	$dgrade_st = prepare_stmt("SELECT privileges, granted_by FROM user WHERE user_id=?");
	$dgrade_st->bind_param("i", $rec_id);
	$response = downgrade_rec($id);
	$dgrade_st->close();

	return $response;	
}

// Recursive search to find if the current admin can downgrade the 
// privileges of the selected user; the possibility is given
// for any non-admin user and for admins under the grant-tree of
// the current user
function downgrade_rec($cur_id) 
{
	// Global variables to avoid reopening the statement
	global $dgrade_st;
	global $rec_id;

	$rec_id = $cur_id;
	$dg = execute_stmt($dgrade_st);
	$row = $dg->fetch_assoc();
	if($row['privileges'] > 0 or $row['granted_by'] == $_SESSION['id'])
		return true;
	else if($row['granted_by'] === null)
		return false;

	return downgrade_rec($row['granted_by']);
}

// Function to transform the project text from the input markup to html
function compile_text($text) 
{
	// Stack of current nesting of ul/ol
	$list_stack = [];
	// Line number for errors
	$num = 1;

	$compiled = "";

	// Takes a line from the text
	$line = strtok($text, "\n");	
	while($line !== false)
	{
		// Stack of the current line
		$cur_list = [];

		// Removes any extra spaces at start and end
		$line = trim($line, " ");
		// Changes possible open tags to their html representation
		$line = str_replace("<", "&#60;", $line);
		
		// If the line is a title the header is set
		// An error is thrown if there is only the opening tag
		if(strpos($line, "&#60;") === 0)
			if(strpos($line, ">") !== false)
				$line = preg_replace("/&#60;(.*?)>/", "<h3>$1</h3>", $line);
			else
				return array($num, "Errore di titolo");
		// If the line is part of a list
		else if($line[0] == UNORDEREDLIST or $line[0] == ORDEREDLIST)
		{
			// Builds the current nesting level
			$list_mk = 0;
			while(isset($line[$list_mk]) and $line[$list_mk] == UNORDEREDLIST or $line[$list_mk] == ORDEREDLIST)
			{
				$cur_list[$list_mk] = $line[$list_mk];

				// If the nesting does not respect the previous line with at most one more element
				// an error occurs
				if(isset($list_stack[$list_mk]) and $list_stack[$list_mk] != $cur_list[$list_mk])
					return array($num, "Errore di lista");

				$list_mk++;
			}

			// Removes the markup
			$line = str_replace(array(UNORDEREDLIST, ORDEREDLIST), "", $line);

			$list_depth = count($list_stack);
			$cur_depth = count($cur_list);

			// An element in the same list is just appended
			if($cur_depth == $list_depth)
				$line = "<li>$line</li>";
			// If the nesting level increases a new element is emitted
			else if($cur_depth > $list_depth)
			{	
				if($cur_list[$cur_depth - 1] == UNORDEREDLIST)
					$line = "<ul><li>$line</li>";
				else
					$line = "<ol><li>$line</li>";
			}
			// If the nesting level decreases an element is closed
			else
			{
				$r = array_pop($list_stack);
				if($r == UNORDEREDLIST)
					$line = "</ul><li>$line</li>";
				else
					$line = "</ol><li>$line</li>";
			}
		}
		// If the element is neither a header nor a list element it is a
		// new paragraph
		else
		{
			// An escape at the start will cause to ignore the markup
			if($line[0] == "\\")
				$line = substr($line, 1);
			$line = "<p>$line</p>";
		}
		
		// If the new line is not in a list any previous list is closed 
		if(count($cur_list) == 0 and count($list_stack) > 0)
		{
			while($r = array_pop($list_stack))
				if($r == UNORDEREDLIST)
					$line = "</ul>".$line;
				else
					$line = "</ol>".$line;
		}

		// Stack update
		$list_stack = $cur_list;

		$compiled .= $line;
		
		// Fetches a new line to analyze
		$line = strtok("\n");
		$num++;
	}

	return $compiled;
}

// Function to show the guide in pages that use the simplified markup
function print_markup_guide()
{
?>
	<ul>
		<li>
			Racchiudendo una riga tra i simboli<samp> < > </samp>viene generato un titolo; es.
			<div class="flexrow">
				<div class="ulflexdiv">&#60;Titolo></div>
				<div>&rarr;</div>
				<div class="ulflexdiv"><h3 class="nomargin">Titolo</h3></div>
			</div>
		</li>
		<li>
			Il simbolo<samp> - </samp>permette di creare una lista non numerata; es.
			<div class="flexrow">
				<div class="ulflexdiv">
					- Elemento 1<br>
					- Elemento 2
				</div>
				<div>&rarr;</div>
				<div class="ulflexdiv">
					<ul class="nomargin">
						<li>Elemento 1</li>
						<li>Elemento 2</li>
					</ul>
				</div>
			</div>
		</li>
		<li>
			Il simbolo<samp> # </samp>permette di creare una lista numerata; es.
			<div class="flexrow">
				<div class="ulflexdiv">
					# Elemento 1<br>
					# Elemento 2
				</div>
				<div>&rarr;</div>
				<div class="ulflexdiv">
					<ol class="nomargin">
						<li>Elemento 1</li>
						<li>Elemento 2</li>
					</ol>
				</div>
			</div>
		</li>
		<li>
			I simboli<samp> - </samp>e<samp> # </samp>possono essere combinati per creare 
			liste annidate; es.
			<div class="flexrow">
				<div class="ulflexdiv">
					- Elemento 1<br>
					-# Elemento 2<br>
					- Elemento 3
				</div>
				<div>&rarr;</div>
				<div class="ulflexdiv">
					<ul class="nomargin">
						<li>Elemento 1</li>
						<ol class="nomargin">
							<li>Elemento 2</li>
						</ol>
						<li>Elemento 3</li>
					</ul>
				</div>
			</div>
		</li>
		<li>
			Un simbolo<samp> \ </samp>a inizio riga permette di ignorare le regole 
			precedenti e stampare il testo normalmente; es.
			<div class="flexrow">
				<div class="ulflexdiv">
					\&#60;Titolo><br>
					\- Elemento <br>
					\Elemento
				</div>
				<div>&rarr;</div>
				<div class="ulflexdiv">
					&#60;Titolo><br>
					- Elemento<br>
					Elemento
				</div>
			</div>
		</li>
	</ul>
<?php
}

// Function to print a hidden markup guide
function print_markup_menu()
{
?>
	<div class="testcard">
		<h3 id="guide_header" class="card-header textcenter"> 
			<button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#guide" 
				aria-expanded="false" aria-controls="#guide">Mostra guida</button>
		</h3>

		<div id="guide" class="collapse textcenter">
			<div class="card card-body">
				<?php print_markup_guide(); ?>
			</div>
		</div>
	</div>
<?php
}
?>
