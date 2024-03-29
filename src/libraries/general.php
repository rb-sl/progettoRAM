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

// Main library of the application, contains functions useful to many scripts
session_start();

// Path constants
const CONF_PATH = "C:\\xampp\\server_conf.json";
const LOG_PATH = "C:\\xampp\\htdocs\\log\\";

const JQUERY_PATH = "/vendor/components/jquery/jquery.min.js";

const BOOTSTRAP_CSS_PATH = "/vendor/twbs/bootstrap/dist/css/bootstrap.min.css";
const BOOTSTRAP_JS_PATH = "/vendor/twbs/bootstrap/dist/js/bootstrap.min.js";

const FITTY_PATH = "/vendor/rikschennink/fitty/dist/fitty.min.js";
const PLOTLY_PATH = "/vendor/plotly/plotly.js/dist/plotly.min.js";

// Constants for access control
// They must reflect those in the database
const ADMINISTRATOR = 0;
const PROFESSOR_GRANTS = 1;
const PROFESSOR = 2;
const RESEARCH = 3;
const NONE = 5;

// Constants for errors
const NEED_LOGIN = 0;
const WRONG_LOGIN = 1;
const UNAUTHORIZED = 2;
const LOGIN_DISABLED = 3;
const FIRST_ACCESS = 4;

// Constants for positive or negative data, according to the database's enum
const GREATER = "Maggiori";
const LOWER = "Minori";

// Debugging function, activated by the get
function errors()
{
	ini_set("display_errors", 1);
	ini_set("display_startup_errors", 1);
	error_reporting(E_ALL);
}
if(isset($_GET['e']))
	errors();

// Logging function, adds username and IP address
function writelog($action)
{
	file_put_contents(LOG_PATH."log_".date("Y-m-d").'.txt', 
		date("H:i:s", time())." [".$_SERVER['REMOTE_ADDR']."] - [".$_SESSION['username']."] $action\n\n", 
		FILE_APPEND);
}

// Access and privilege control, if needed stops
// the loading of the page
function chk_access($privileges = NONE, $kill = true)
{
	// If an error is already set it is not overwritten
	if(!isset($_SESSION['err']) or $_SESSION['err'] == "")
	{
		if(!isset($_SESSION['username']))
			set_error(NEED_LOGIN);
		else if(!chk_auth($privileges))
			set_error(UNAUTHORIZED);
		else
			return true;
		
		// If an error was discovered actions are
		// taken based on the parameter
		if(!$kill)
			return false;

		header("Location: /");
		exit;
	}

	return true;
}

// Function to check if a user has the given privilege level
function chk_auth($privileges)
{
	return (isset($_SESSION['privileges']) and $_SESSION['privileges'] <= $privileges);
}

// Checks if the user can access the class's register page 
function chk_prof($user_fk)
{
	if($user_fk and $user_fk != $_SESSION['id'] and $_SESSION['privileges'] != ADMINISTRATOR)
	{
		set_alert("Permessi insufficienti per visualizzare la classe");
		header("Location: /register/register.php");
		exit;
	}
}

// Obtains the server's database logging information
function get_server_conf()
{
	$conf = fopen(CONF_PATH, "r") or die("Unable to open configuration file");
	$serv = fread($conf, filesize(CONF_PATH));
	fclose($conf);
	return json_decode($serv);
}

// Connection to MySQL DB and error handling
function connect()
{
	global $mysqli;
	$conf = get_server_conf();
	$mysqli = new mysqli("localhost", $conf->dbuser, $conf->dbpass, $conf->dbname);
	if ($mysqli->connect_errno) 
	{
		echo "Connection error ".$mysqli->connect_errno.": ".$mysqli->connect_error;
		writelog("[conn_err] ".$mysqli->connect_errno.": ".$mysqli->connect_error);
		show_postmain();
		exit();
	}
}  

// Function to request the confirmation of a client-side action
function confirm($quest)
{
	return " onclick=\"return confirm('".addslashes(htmlentities($quest)).". Procedere?');\"";
}

// Function to store a message to be shown to the user
// on the next page
function set_alert($msg)
{
	$_SESSION['alert'][] = $msg;
}

// Function to display alerts from set_alert; the messages
// are consumed
function display_alerts()
{
	if(isset($_SESSION['alert']) and count($_SESSION['alert']) > 0)
	{
		$alerts = "";
		foreach($_SESSION['alert'] as $k => $msg)
		{
			if($alerts != "")
				$alerts .= "\\n\\n";

			$alerts .= addslashes(htmlentities($msg));

			unset($_SESSION['alert'][$k]);
		}

		echo "<script>
			alert(\"$alerts\");
		</script>";
	}
}

// Function to add an error to the session
function set_error($error)
{
	$_SESSION['err'] = $error;
}

// Prints errors and returns a value to decide if
// the loading must be stopped
function display_errors()
{
	if(isset($_SESSION['err']) and 
		($_SESSION['err'] != FIRST_ACCESS or basename($_SERVER['PHP_SELF']) != "profile.php"))
	{
		echo "<h3 class='dangercolor'>Accesso negato</h3><h4>";
		switch($_SESSION['err'])
		{
			case NEED_LOGIN:
				echo "Effettuare il login";
				unset($_SESSION['err']);
				break;
			case WRONG_LOGIN:
				echo "Login errato";
				unset($_SESSION['err']);
				break;
			case UNAUTHORIZED:
				echo "Utente non autorizzato";
				unset($_SESSION['err']);
				break;
			case LOGIN_DISABLED:
				echo "Login disabilitato";
				unset($_SESSION['err']);
				break;
			case FIRST_ACCESS:
				echo "Primo accesso: <a href='/user/profile.php'>modificare la password</a>";
				break;
			default:
				break;
		}
		echo "</h4>";

		return true;
	}

	return false;
}

// strtoupper enriched with accents for italian letters
function maiuscolo($str)
{
	$lower = array("à","è","é","ì","ò","ù");
	$upper = array("À","È","É","Ì","Ò","Ù");
	return strtoupper(str_replace($lower, $upper, $str));
}

// Function to get the minimum and maximum years in the system
function year_span()
{
	$stmt = prepare_stmt("SELECT MIN(class_year) AS y1, MAX(class_year) AS y2 
		FROM results JOIN instance ON instance_id=instance_fk 
		JOIN class ON class_fk=class_id");
	$ret = execute_stmt($stmt);
	$stmt->close();
	$years = $ret->fetch_assoc();

	// When no data is present in the database only the current
	// scholastic year is shown
	if(!$years['y1'])
	{	
		$curyear = get_current_year();

		$years['y1'] = $curyear;
		$years['y2'] = $curyear;
	}

	return $years;
}

// Function to get the current school year; for the first quadrimester it is the current one,
// while for the second it is -1. The change of year is done in august
function get_current_year()
{
	$year = date("Y");
	if(date("m") < 8)
		$year--;
	return $year;
}

// Statement preparation and error handling
function prepare_stmt($query)
{
	global $mysqli;

	if(!($stmt = $mysqli->prepare($query)))                                                                                                                                                                                                    
		query_error("prepare", $query);
	
	return $stmt;
}

// Statement execution and error handling
function execute_stmt($stmt)
{
	$stmt->execute();
	if($stmt->errno !== 0)
	{
		echo "Execute failed (".$stmt->errno."): ".$stmt->error."<br>";
		return;
	}

	$res = $stmt->get_result();
	if($stmt->errno !== 0)
	{
		echo "Getting result set failed (".$stmt->errno."): ".$stmt->error."<br>";
		return;
	}

	return $res;
}

// Function to show errors on queries
function query_error($stage, $query)
{
	global $mysqli;
	echo "<div class='row bordermenu'>".$mysqli->errno."<br>".$mysqli->error."<br>$query</div>";
	writelog("[query_err] [$stage] [".$mysqli->errno."] ".$mysqli->error."\n>>$query");
	show_postmain();
	exit();
}

// Base function for frontend pages; shows the static information of the page
// If $stat is set it shows the statistical menu
function show_premain($title = "", $stat = false, $fullwidth = false)
{
	if($title != "")
		$title .= " - ";
?>
<!DOCTYPE html> 
<html> 
	<head> 
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- jQuery -->
		<script src="<?=JQUERY_PATH?>"></script>
		
		<!-- Bootstrap and custom graphical elements -->
		<link rel="stylesheet" href="<?=BOOTSTRAP_CSS_PATH?>">
		<script src="<?=BOOTSTRAP_JS_PATH?>"></script>
				
		<!-- Plots and table text width -->
		<script src="<?=PLOTLY_PATH?>"></script>
		<script src="<?=FITTY_PATH?>"></script>

		<!-- Custom CSS -->
		<link rel="stylesheet" href="/libraries/ui/custom.css" type="text/css" media="all">
<?php 
	if($stat)
	{
?>
		<script src="/libraries/stat_menu.js"></script>
<?php
	}
?>
		<title><?=$title?>Progetto RAM</title>
	</head> 
  	
	<body> 
  		<div id="wrapper">
		  	<div class="navcontainer">
				<nav id="nav1" class="navbar-expand-lg navbar navbar-dark bg-dark big-topfix">
					<div class="container-fluid">
						<a class="navbar-brand" href="/">Progetto RAM</a>
						<button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#myNavbar" aria-controls="#myNavbar" aria-expanded="false" aria-label="Toggle navigation">
							<span class="navbar-toggler-icon"></span>
						</button>

						<div class="collapse navbar-collapse" id="myNavbar">
							<ul class="navbar-nav me-auto mb-2 mb-lg-0">
								<li class="nav-item"><a href="/project.php" class="nav-link">Il progetto</a></li>
								<li class="nav-item"><a href="/register/register.php" class="nav-link">Registro</a></li>
								<li class="nav-item"><a href="/test/test.php" class="nav-link">Test e valutazioni</a></li>
								<li class="nav-item"><a href="/statistics/statistics.php" class="nav-link">Statistica</a></li>
								<li class="nav-item"><a href="/guide/guide.php" class="nav-link">Manuale</a></li>
							</ul>
							
							<!-- Login -->
							<ul class="navbar-nav ms-auto" aria-labelledby="navbarDropdown">	
								<li class="nav-item dropdown">
									<a id="logindropdown" href="#" class="dropdown-toggle nav-link" role="button" data-bs-toggle="dropdown" aria-expanded="false"> 
										<?=(isset($_SESSION["username"]) ? htmlentities($_SESSION["username"]) : "Login")?> <span class="caret"></span>
									</a>
			
									<ul id="login-dp" class="dropdown-menu dropdown-menu-end" aria-labelledby="logindropdown">
<?php
	if(!isset($_SESSION['username'])) 
	{
?>										
										<li>
											<form class="form" role="form" method="POST" action="/user/login.php" accept-charset="UTF-8" id="login-nav">
												<div class="form-group">
													<input type="text" class="form-control" name="user" placeholder="Username" required>
												</div>
												<div class="form-group">
													<input type="password" class="form-control" name="password" placeholder="Password" required>
												</div>
												<div class="form-group">
													<button type="submit" class="btn btn-warning">Accedi</button>
												</div>
											</form>
										</li>
<?php 
	} 
	else
	{
?> 
  										<li>
	  										<a href="/user/profile.php" class="btn btn-warning">Profilo</a>
<?php
		if($_SESSION["privileges"] == ADMINISTRATOR)
		{
?> 
											<div class="form-group">
												<a href="/admin/admin.php" class="btn btn-warning">Amministrazione</a>
											</div>
<?php
		}
?>
  											<div class="form-group">
												<a href="/user/logout.php" class="btn btn-warning">Log Out</a>
											</div>						
										</li>
<?php
	}
?>
									</ul>
								</li>
							</ul> <!-- End of login -->
						</div>
					</div>
				</nav>
<?php
	if($stat)
	{
		$margin = "statwide";
		$years = year_span();
?>
				<nav id="nav2" class="navbar-expand-lg navbar navbar-dark bg-dark big-topfix">
					<div class="container-fluid">
						<span id="statmenu" class="navbar-brand">Menu statistico</span>
						<button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#myNavbar2" aria-controls="#myNavbar2" aria-expanded="false" aria-label="Toggle navigation">
							<span class="navbar-toggler-icon"></span>                      
						</button>

						<div class="collapse navbar-collapse" id="myNavbar2">
							<ul class="navbar-nav me-auto mb-2 mb-lg-0">
								<li class="li-stat nav-item">Anni da 
									<input type="text" id="y1" class="menuyear" name="y1" value="<?=$years["y1"]?>" size="4" required>
									/
									<span id="flwy1"><?=($years["y1"] + 1)?></span>
									a 
									<input type="text" id="y2" class="menuyear" name="y2" value="<?=$years["y2"]?>" size="4" required>
									/
									<span id="flwy2"><?=($years["y2"] + 1)?></span>
								</li>
								<li class="li-stat nav-item">
									Classi:
									<button id="c1" class="btn btn-primary overpad stat" value="on">1</button>
									<button id="c2" class="btn btn-primary overpad stat" value="on">2</button>
									<button id="c3" class="btn btn-primary overpad stat" value="on">3</button>
									<button id="c4" class="btn btn-primary overpad stat" value="on">4</button>
									<button id="c5" class="btn btn-primary overpad stat" value="on">5</button>
								</li>
								<li class="li-stat nav-item">
									Sesso:
										<button id="m" class="btn btn-primary overpad stat" value="on">M</button>
										<button id="f" class="btn btn-primary overpad stat" value="on">F</button>      
								</li>
								<li class="li-stat nav-item">
									<button id="rstr" class="btn btn-secondary overpad stat" value="off">Solo personali</button>
								</li>
								<li class="li-stat">
									<button id="update" class="btn btn-primary overpad">Aggiorna</button>
								</li>
							</ul>
						</div>
					</div>    
				</nav>
<?php
	}
	else
		$margin = "nostatwide";

	if($fullwidth)
		$widthcl = "mainfullwidth";
	else
		$widthcl = "";
?>
			</div>
			<main class="<?=$margin." ".$widthcl?>">
<?php
	if(display_errors())
	{
		show_postmain();
		exit;
	}
}

// Shows the final static elements 
function show_postmain()
{
?>	
			</main>
		</div>
		<footer><a href="/guide/guide.php#code">
			<div class="license">
				<img src="/agpl.svg" alt="agpl license">
			</div>
			Applicazione rilasciata con licenza AGPLv3</a>
		</footer>
<?php
	display_alerts();
?>
	</body> 
</html>
<?php
}
?>
