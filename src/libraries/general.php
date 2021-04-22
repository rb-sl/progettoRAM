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

const JQUERY_PATH = "/libraries/ui/jquery-3.4.1.min.js";

const BOOTSTRAP_CSS_PATH = "/libraries/ui/bootstrap/css/bootstrap.min.css";
const BOOTSTRAP_JS_PATH = "/libraries/ui/bootstrap/js/bootstrap.min.js";

const FITTY_PATH = "/libraries/ui/fitty/fitty.min.js";
const PLOTLY_PATH = "/libraries/plotly.min.js";

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

// Debugging function, activated by the get
function errors()
{
	ini_set("display_errors", 1);
	ini_set("display_startup_errors", 1);
	error_reporting(E_ALL);
}
if(isset($_GET['e']))
	errors();

// Logging function, adds user and IP address
function writelog($action)
{
	file_put_contents(LOG_PATH."log_".date("Y-m-d").'.txt', 
		date("H:i:s", time())." [".$_SERVER['REMOTE_ADDR']."] - [".$_SESSION['user']."] $action\n\n", 
		FILE_APPEND);
}

// Access and privilege control, if needed stops
// the loading of the page
function chk_access($priv = NONE, $kill = true)
{
	// If an error is already set it is not overwritten
	if(!isset($_SESSION['err']) or $_SESSION['err'] == "")
	{
		if(!isset($_SESSION['user']))
			set_error(NEED_LOGIN);
		else if(!chk_auth($priv))
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
function chk_auth($priv)
{
	return (isset($_SESSION['priv']) and $_SESSION['priv'] <= $priv);
}

// Function to add an error to the session
function set_error($error)
{
	$_SESSION['err'] = $error;
}

// Checks if the user can access the class's register page 
function chk_prof($fk_prof)
{
	if($fk_prof and $fk_prof != $_SESSION['id'] and $_SESSION['priv'] != ADMINISTRATOR)
	{
		$_SESSION['alert'] = "Permessi insufficienti per visualizzare la classe";
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
	return "onclick=\"return confirm('".addslashes(quoteHTML($quest)).". Procedere?');\"";
}

// Changes " in &quot; for visualization purposes
function quoteHTML($str)
{
	return str_replace("\"", "&quot;", $str);
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
	$stmt = prepare_stmt("SELECT MIN(anno) AS y1, MAX(anno) AS y2 
		FROM PROVE JOIN ISTANZE ON id_ist=fk_ist 
		JOIN CLASSI ON fk_cl=id_cl");
	$ret = execute_stmt($stmt);
	$stmt->close();
	return $ret->fetch_assoc();
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
		echo "Execute failed: (".$stmt->errno.") ".$stmt->error."<br>";

	$res = $stmt->get_result();
	if($stmt->errno !== 0)
		echo "Getting result set failed: (".$stmt->errno.") ".$stmt->error."<br>";
		
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
										<?=(isset($_SESSION["user"]) ? $_SESSION["user"] : "Login")?> <span class="caret"></span>
									</a>
			
									<ul id="login-dp" class="dropdown-menu dropdown-menu-end" aria-labelledby="logindropdown">
<?php
	if(!isset($_SESSION['user'])) 
	{
?>										
										<li>
											<form class="form" role="form" method="POST" action="/user/login.php" accept-charset="UTF-8" id="login-nav">
												<div class="form-group">
													<input type="text" class="form-control" name="usr" placeholder="Username" required>
												</div>
												<div class="form-group">
													<input type="password" class="form-control" name="psw" placeholder="Password" required>
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
		if($_SESSION["priv"] == ADMINISTRATOR)
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
	// Prints errors and stops the loading of the page
	if(isset($_SESSION['err']) and $_SESSION['err'] !== ""
		and ($_SESSION['err'] != FIRST_ACCESS or basename($_SERVER['PHP_SELF']) != "profile.php"))
		{
			echo "<h3 class='dangercolor'>Accesso negato</h3>";
			switch($_SESSION['err'])
			{
				case NEED_LOGIN:
					echo "<h4>Effettuare il login</h4>";
					$_SESSION['err'] = "";
					break;
				case WRONG_LOGIN:
					echo "<h4>Login errato</h4>";
					$_SESSION['err'] = "";
					break;
				case UNAUTHORIZED:
					echo "<h4>Utente non autorizzato</h4>";
					$_SESSION['err'] = "";
					break;
				case LOGIN_DISABLED:
					echo "<h4>Login disabilitato</h4>";
					$_SESSION['err'] = "";
					break;
				case FIRST_ACCESS:
					echo "<h4>Primo accesso: <a href='/user/profile.php'>modificare la password</a></h4>";
					break;
				default:
					break;
			}
			show_postmain();
			exit;
		}

	if(isset($_SESSION['scad']) and $_SESSION['scad'])
  		echo "<h3 class='dangercolor'>Password in scadenza!</h3>
  		<h4>Accedere alla <a href='/user/profile.php'>pagina di profilo</a> per modificarla</h4>";
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
	if(isset($_SESSION['alert']) and $_SESSION['alert'] != "")
	{
?>
		<script>
			$(function() {
				$(document).ready(function() {
					alert("<?=addslashes($_SESSION['alert'])?>");
  				});
  	  		});
		</script>
<?php
		$_SESSION['alert'] = "";
	}
?>
	</body> 
</html>
<?php
}
?>
