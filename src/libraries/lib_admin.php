<?php
// Collection of functions related to the administrative section

// Symbol for unordered lists
const UNORDEREDLIST = "-";
// Symbol for ordered lists
const ORDEREDLIST = "#";

// Function to get the text and color associated to a
// privilege level
function get_privilege($priv)
{
	switch($priv)
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
	$dgrade_st = prepare_stmt("SELECT priv, granted_by FROM PROFESSORI WHERE id_prof=?");
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
	if($row['priv'] > 0 or $row['granted_by'] == $_SESSION['id'])
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

		// If the line is a title the header is set
        // An error is thrown if there is only the opening tag
		if($line[0] == "<")
		    if(strpos($line, ">") !== false)
			    $line = preg_replace("/<(.*?)>/", "<h3>$1</h3>", $line);
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
?>
