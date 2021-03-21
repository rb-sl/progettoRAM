<?php
// Collection of functions related to the administrative section

// Function to get the text and color associated to a
// privilege level
function get_privilege($priv)
{
	switch($priv)
    {
        case 0: 
            $ret['text'] = "Amministratore";
            $ret['color'] = "primarybg";
            break;
        case 1: 
            $ret['text'] = "Professore (modifica test)";
            $ret['color'] = "infobg";
            break;
        case 2: 
            $ret['text'] = "Professore";
            $ret['color'] = "successbg";
            break;
        case 3: 
            $ret['text'] = "Ricerca";
            $ret['color'] = "warningbg";
            break;
        case 5:
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
?>