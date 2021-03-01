<?php
session_start();

// La funzione errors permette di visualizzare tutti gli errori generati
// da php per debugging
function errors()
{
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
if($_GET['e'])
	errors();

// La funzione permette di scrivere una stringa sui log applicativi, aggiungendo ora e giorno
function writelog($action)
{
	file_put_contents($_SERVER['DOCUMENT_ROOT']."/logs/log_".date("Y-m-d").'.txt', date('H:i:s',time())." - [".$_SESSION['usr']."] $action\n\n", FILE_APPEND);
}

// Controllo accessi più privilegi
// 0 -> amministrarore
// 1 -> professore
// 2 -> statistica
// Blocca se un utente tenta l'accesso a una pagina non sua
function chk_access($priv=5)
{
	if(!isset($_SESSION['usr']))
    {
    	$_SESSION['err']=1;
    	header('Location: /');
    	exit;
    }
	if($_SESSION['priv']>$priv)
    {
    	$_SESSION['err']=3;
    	header('Location: /');
    	exit;
    }
}

// Controlla il privilegio di accesso a una classe
function chk_prof($fk_prof)
{
	if($fk_prof and $fk_prof!=$_SESSION['id'] and $_SESSION['priv']!=0)
	{
		$_SESSION['alert']="Permessi insufficienti per visualizzare la classe";
		header("Location: /registro/registro.php");
		exit;
	}
}

//Funzione per la connessione al DB + controllo errori, fa escape del POST
function connect()
{
    $_SESSION['sql']=new mysqli("localhost", "", "","");
	if ($_SESSION['sql']->connect_errno) 
    {
    	echo "Errore di connessione ".$_SESSION['sql']->connect_errno.": ".$_SESSION['sql']->connect_error;
    	writelog("[conn_err]".$_SESSION['sql']->connect_errno.": ".$_SESSION['sql']->connect_error);
		show_postmain();
        exit();
	}    
	escape($_POST);
}     

function confirm($quest)
{
	echo "onclick=\"return confirm('$quest. Procedere?');\"";
}

// Funzioni per la sanificazione di $_POST. Da chiamare su tutte le pagine che fanno operazioni
// sul db; non chiamata subito per effettuare prima la connessione
function escape(&$array)
{
	foreach($array as $key => &$item)
	{
		if(is_array($item))
			escape($item);
		else
			$array[$key]=$_SESSION['sql']->real_escape_string($item);
	}
}

// Permette di rendere maiuscole le stringhe, compresi i caratteri accentati
function maiuscolo($stringa)
{
	$accentate = array("à","è","é","ì","ò","ù");
	$sostituzioni = array("À","È","É","Ì","Ò","Ù");
	return strtoupper(str_replace($accentate,$sostituzioni,$stringa));
}

// Funzione che raggruppa l'esecuzione di query sql e il controllo errori per debugging
function query($query)
{
	if(!($res=$_SESSION['sql']->query($query)))                                                                                                                                                                                                         
    {
    	echo "<div class='row border'>".$_SESSION['sql']->errno."<br>".$_SESSION['sql']->error."<br>$query</div>";
    	writelog("[query_err] [".$_SESSION['sql']->errno."] ".$_SESSION['sql']->error."\n>>$query");
    }
	return $res;
}

// Funzione base per ogni pagina front-end
// Se $stat è settato mostra le opzioni statistiche. In particolare in campi di $stat sono
// anno1 -> il primo anno da cui fare le query
// anno2 -> il secondo anno per le query
function show_premain($title="",$stat=false)
{
	echo "<!DOCTYPE html> 
<html> 
	<head> 
    	<meta charset='utf-8'>
	    <meta name='viewport' content='width=device-width, initial-scale=1'>
    	
        <!-- jquery e jquery ui -->
        <script src='/librerie/jquery-3.4.1.min.js'></script>
        <script src='/librerie/jquery-ui/jquery-ui.min.js'></script>
        <link rel='stylesheet' href='/librerie/jquery-ui/jquery-ui.min.css'>
        
        <!-- Grafica bootstrap e custom -->
        <link rel='stylesheet' href='/librerie/grafica/bootstrap.min.css'>
    	<script src='/librerie/grafica/bootstrap.min.js'></script>
        
        <link rel='stylesheet' href='/librerie/grafica/custom.css' type='text/css' media='all'> 
		
		<!-- Script grafici e custom -->
        <script src='/librerie/plotly-latest.min.js'></script>
        <script src='/librerie/ram.js'></script>
        
    	<title>".($title ? "$title - Progetto RAM" : "Progetto RAM")."</title>
  	</head> 
  	
    <body> 
  		<div id='wrapper'>
      		<nav id='nav1' class='pg-head navbar navbar-inverse navbar-fixed-top'>
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
        					<li><a href='/progetto.php'>Il progetto</a></li>
        					<li><a href='/registro/registro.php'>Registro</a></li>
        					<li><a href='/test/test.php'>Test e valutazioni</a></li>
        					<li><a href='/statistica/statistica.php'>Statistica</a></li>
        					<li><a href='/manuale.php'>Manuale</a></li>
      					</ul>
      
      					<!-- Login -->
      					<ul class='nav navbar-nav navbar-right'>
        					<li class='dropdown'>
          						<a href='#' class='dropdown-toggle' data-toggle='dropdown'> <b>".($_SESSION['usr'] ? $_SESSION['usr'] : "Login")."</b> <span class='caret'></span></a>
								<ul id='login-dp' class='dropdown-menu'>
								<li>
					 				<div class='row'>
										<div class='col-md-12'>";
	if(!isset($_SESSION['usr']))
		echo "<span>Login</span>
				<form class='form' role='form' method='post' action='/librerie/login.php' accept-charset='UTF-8' id='login-nav'>
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
      			<a href='/librerie/profilo.php' class='btn btn-primary btn-warning'>Profilo</a><br>
    		</div>";
  		if($_SESSION['priv']==0)
    		echo "<div class='form-group'>
      			<a href='/adm/amministrazione.php' class='btn btn-primary btn-warning'>Amministrazione</a>
    		</div>";
  		echo "<div class='form-group'>
    		<a href='/librerie/logout.php' class='btn btn-primary btn-warning'>Log Out</a>
  		</div>"; 
	}
	
	echo "							</div>
								</div>
							</li>
						</ul>
       				 </li>
      			</ul> <!-- FINISCE LA PARTE DI LOGIN -->
    		</div>
  		</div>
	</nav> ";
	$px="60px";
	
	if($stat)
    {
    	$px="94px";
    	$ret=query("SELECT MIN(anno) AS anno1,MAX(anno) AS anno2 FROM PROVE,ISTANZE,CLASSI WHERE id_ist=fk_ist AND fk_cl=id_cl");
    	$anni=$ret->fetch_assoc();
        echo " <nav id='nav2' class='pg-head navbar-inverse navbar-fixed-top' style='margin-top: 50px'>
		<div class='container-fluid'>
			<div class='navbar-header'>
				<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#myNavbar2'>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>                        
				</button>
			</div>
			<div class='collapse navbar-collapse' id='myNavbar2'>
				<ul class='nav navbar-nav'>
					<li class='li-stat'>Anni da 
                    	<input type='text' id='a1' class='anno' name='anno1' value='".$anni['anno1']."' style='text-align:right;width:40px' required>
                        /
                        <span id='flwa1'>".($anni['anno1']+1)."</span>
						a 
                        <input type='text' id='a2' class='anno' name='anno2' value='".$anni['anno2']."' size='4' style='text-align:right;width:40px' required>
                        /
                        <span id='flwa2'>".($anni['anno2']+1)."</span>
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
                    <li class='li-stat'>
                    	<button id='update' class='btn btn-primary overpad'>Aggiorna</button>
                    </li>
      			</ul>
            </div>
        </div>    
    </nav>";
    }
	echo "<main style='margin-top:$px'>";
	
	//In caso di errori di autenticazione si mostra un messaggio e si termina il caricamento della pagina
	if($_SESSION['err'])
	{
  		echo "<h3 style='color:red'>Accesso negato</h3>";
  		switch($_SESSION['err'])
  		{
    		case 1:
      			echo "<h4>Effettuare il login</h4>";
      			break;
    		case 2:
      			echo "<h4>Login errato</h4>";
      			break;
    		case 3:
      			echo "<h4>Utente non autorizzato</h4>";
     			break;
        	case 4:
      			echo "<h4>Login disabilitato</h4>";
     			break;
    		default:
      			break;
  		}
    	$_SESSION['err']="";
    	show_postmain();
    	exit;
	}

	if($_SESSION['scad'])
  		echo "<h3 style='color:red'>Password in scadenza!</h3>
  		<h4>Accedere alla <a href='/librerie/profilo.php'>pagina di profilo</a> per modificarla</h4>";
}

//Parti statiche finali delle pagine front-end
function show_postmain()
{
	if($_SESSION['alert'])
	{
		echo "<script>
    		$(function(){
    			$(document).ready(function(){
    				alert('".$_SESSION['alert']."');
  				});
  	  		});
		</script>";
    	$_SESSION['alert']="";
	}

	if($_SESSION['msg'])
	{
    	echo "<h3>".$_SESSION['msg']."</h3>";
    	$_SESSION['msg']="";
	}
	
	echo "			<div id='dialog'></div>
				</main>
			</div> <!-- container -->
		<footer>
			<div id='container'>
				&nbsp;ITIS Fauser Novara - 2017
			</div>
		</footer>
	</body> 
</html>";
}