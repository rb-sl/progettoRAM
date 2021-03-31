<?php
// Main library of the application, contains functions useful to many scripts
session_start();

// Path constants
const CONF_PATH = "C:\\xampp\\server_conf.json";
const LOG_PATH = "C:\\xampp\\htdocs\\log\\";

const BOOTSTRAP_CSS_PATH = "/libraries/ui/bootstrap.min.css";
const BOOTSTRAP_JS_PATH = "/libraries/ui/bootstrap.min.js";
const FITTY_PATH = "/libraries/ui/fitty/fitty.min.js";

const JQUERY_PATH = "/libraries/ui/jquery-3.4.1.min.js";
const JQUERY_UI_JS_PATH = "/libraries/ui/jquery-ui/jquery-ui.min.js";
const JQUERY_UI_CSS_PATH = "/libraries/ui/jquery-ui/jquery-ui.min.css";

const PLOTLY_PATH = "/libraries/plotly.min.js";

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

// Access and privilege control; the levels are
// 0 -> Administrator
// 1 -> Professor with grants to modify tests
// 2 -> Normal professor
// 3 -> Statistical access
function chk_access($priv = 5)
{
	if(!isset($_SESSION['err']) or $_SESSION['err'] == "")
	{
		if(!isset($_SESSION['user']))
		{
			$_SESSION['err'] = 1;
			header('Location: /');
			exit;
		}
		if(!isset($_SESSION['priv']) or $_SESSION['priv'] > $priv)
		{
			$_SESSION['err'] = 3;
			header('Location: /');
			exit;
		}
	}
}

// Checks if the user can access the class's register page 
function chk_prof($fk_prof)
{
	if($fk_prof and $fk_prof != $_SESSION['id'] and $_SESSION['priv'] != 0)
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
		$title .= " -";

	echo "<!DOCTYPE html> 
<html> 
	<head> 
    	<meta charset='utf-8'>
	    <meta name='viewport' content='width=device-width, initial-scale=1'>

		<!-- jQuery and jQuery UI -->
        <script src='".JQUERY_PATH."'></script>
        <script src='".JQUERY_UI_JS_PATH."'></script>
        <link rel='stylesheet' href='".JQUERY_UI_CSS_PATH."'>
		<script src='".FITTY_PATH."'></script>

		<!-- Bootstrap and custom graphical elements -->
        <link rel='stylesheet' href='".BOOTSTRAP_CSS_PATH."'>
    	<script src='".BOOTSTRAP_JS_PATH."'></script>
        
        <link rel='stylesheet' href='/libraries/ui/custom.css' type='text/css' media='all'> 
                
		<!-- Graph and custom scripts -->
        <script src='".PLOTLY_PATH."'></script>
		".($stat ? "<script src='/libraries/stat_menu.js'></script>" : "")."
		<title>$title Progetto RAM</title>
	</head> 
  	
    <body> 
  		<div id='wrapper'>
      		<nav id='nav1' class='pg-head navbar navbar-inverse big-topfix'>
  				<div class='container-fluid'>
    				<div class='navbar-header'>
      					<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#myNavbar'>
        					<span class='icon-bar'></span>
        					<span class='icon-bar'></span>
        					<span class='icon-bar'></span>                        
      					</button>
      					<a class='navbar-brand' href='/'>Progetto RAM</a>
    				</div>
    				<div class='collapse navbar-collapse' id='myNavbar'>
      					<ul class='nav navbar-nav'>
        					<li><a href='/project.php'>Il progetto</a></li>
        					<li><a href='/register/register.php'>Registro</a></li>
        					<li><a href='/test/test.php'>Test e valutazioni</a></li>
        					<li><a href='/statistics/statistics.php'>Statistica</a></li>
        					<li><a href='/guide/guide.php'>Manuale</a></li>
      					</ul>
      
      					<!-- Login -->
      					<ul class='nav navbar-nav navbar-right'>
        					<li class='dropdown'>
          						<a href='#' class='dropdown-toggle' data-toggle='dropdown'> <b>".(isset($_SESSION['user']) ? $_SESSION['user'] : "Login")."</b> <span class='caret'></span></a>
								<ul id='login-dp' class='dropdown-menu'>
								<li>
					 				<div class='row'>
										<div class='col-md-12'>";
	if(!isset($_SESSION['user']))
		echo "<span>Login</span>
				<form class='form' role='form' method='post' action='/user/login.php' accept-charset='UTF-8' id='login-nav'>
					<div class='form-group'>
	  					<input type='text' class='form-control' name='usr' placeholder='Username' required>
					</div>
					<div class='form-group'>
	  					<input type='password' class='form-control' name='psw' placeholder='Password' required>
					</div>
					<div class='form-group'>
	  					<button type='submit' class='btn btn-warning'>Accedi</button>
					</div>
  				</form>"; 
	else
	{ 
  		echo "<div class='form-group'>
      			<a href='/user/profile.php' class='btn btn-primary btn-warning'>Profilo</a><br>
    		</div>";

  		if($_SESSION['priv'] == 0)
    		echo "<div class='form-group'>
      			<a href='/admin/admin.php' class='btn btn-primary btn-warning'>Amministrazione</a>
    		</div>";

  		echo "<div class='form-group'>
    		<a href='/user/logout.php' class='btn btn-primary btn-warning'>Log Out</a>
  		</div>"; 
	}
	
	echo "							</div>
								</div>
							</li>
						</ul>
       				 </li>
      			</ul> <!-- End of login -->
    		</div>
  		</div>
	</nav> ";
	
	if($stat)
    {
    	$margin = "statwide";
		$years = year_span();
		
        echo " <nav id='nav2' class='pg-head navbar-inverse big-topfix'>
		<div class='container-fluid'>
			<div class='navbar-header'>
				<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#myNavbar2'>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>                        
				</button>
				<span id='statmenu' class='navbar-brand'>Menu statistico</span>
			</div>
			<div class='collapse navbar-collapse' id='myNavbar2'>
				<ul class='nav navbar-nav'>
					<li class='li-stat'>Anni da 
                    	<input type='text' id='y1' class='menuyear' name='y1' value='".$years['y1']."' required>
                        /
                        <span id='flwy1'>".($years['y1'] + 1)."</span>
						a 
                        <input type='text' id='y2' class='menuyear' name='y2' value='".$years['y2']."' size='4' required>
                        /
                        <span id='flwy2'>".($years['y2'] + 1)."</span>
                    </li>
					<li class='li-stat'>
                    	Classi:
                        <button id='c1' class='btn btn-primary overpad stat' value='on'>1</button>
                        <button id='c2' class='btn btn-primary overpad stat' value='on'>2</button>
                        <button id='c3' class='btn btn-primary overpad stat' value='on'>3</button>
                        <button id='c4' class='btn btn-primary overpad stat' value='on'>4</button>
                        <button id='c5' class='btn btn-primary overpad stat' value='on'>5</button>
                    </li>
					<li class='li-stat'>
                    	Sesso:
                        	<button id='m' class='btn btn-primary overpad stat' value='on'>M</button>
                        	<button id='f' class='btn btn-primary overpad stat' value='on'>F</button>      
                    </li>
					<li class='li-stat'>
                    	<button id='rstr' class='btn overpad stat' value='off'>Solo personali</button>
                    </li>
                    <li class='li-stat lastmenu'>
                    	<button id='update' class='btn btn-primary overpad trigger'>Aggiorna</button>
                    </li>
      			</ul>
            </div>
        </div>    
    </nav>";
    }
	else
		$margin = "nostatwide";
	
	if($fullwidth)
		$widthcl = "fullwidth";
	else
		$widthcl = "";

	echo "<main class='$margin $widthcl'>";
	
	// Prints errors and stops the loading of the page
	if(isset($_SESSION['err']) and $_SESSION['err'] != "")
		if($_SESSION['err'] != 5 or basename($_SERVER['PHP_SELF']) != "profile.php")
		{
  			echo "<h3 class='dangercolor'>Accesso negato</h3>";
			switch($_SESSION['err'])
			{
				case 1:
					echo "<h4>Effettuare il login</h4>";
					$_SESSION['err'] = "";
					break;
				case 2:
					echo "<h4>Login errato</h4>";
					$_SESSION['err'] = "";
					break;
				case 3:
					echo "<h4>Utente non autorizzato</h4>";
					$_SESSION['err'] = "";
					break;
				case 4:
					echo "<h4>Login disabilitato</h4>";
					$_SESSION['err'] = "";
					break;
				case 5:
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
	global $mysqli;
	if(isset($_SESSION['alert']) and $_SESSION['alert'] != "")
	{
		echo "<script>
    		$(function(){
    			$(document).ready(function(){
    				alert(\"".addslashes($_SESSION['alert'])."\");
  				});
  	  		});
		</script>";
    	$_SESSION['alert'] = "";
	}

	if(isset($_SESSION['msg']) and $_SESSION['msg'] != "")
	{
    	echo "<h3>".$_SESSION['msg']."</h3>";
    	$_SESSION['msg'] = "";
	}
	
	echo "			<div id='dialog'></div>
				</main>
			</div>
		<footer>
			<div id='container'>
				&nbsp;ITIS Fauser Novara - 2017
			</div>
		</footer>
	</body> 
</html>";
}
