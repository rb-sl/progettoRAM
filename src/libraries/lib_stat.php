<?php
// Funzione per il calcolo della media dato un array $vals
function arr_avg($vals, $dec = 0)
{
	if(sizeof($vals))
    {
		$s = array_sum($vals);
		return number_format($s / sizeof($vals), $dec);
	}
	return "-";
}

// Funzione per il calcolo della mediana dato un array $vals
function arr_med($vals, $dec)
{
	$size = sizeof($vals);

	if($size == 0)
		return "-";

	sort($vals);

	// To align with array indices, in the even case 1 is subtracted from the found
	// place, while in the odd case floor is used in place of ceil
	if($size % 2 == 0)
    	return number_format(($vals[$size / 2 - 1] + $vals[$size / 2]) / 2, $dec);
	else
    	return number_format($vals[floor($size / 2)], $dec);	
}

// Funzione per il calcolo della deviazione standard di un array
function arr_std($vals,$avg="")
{
	$n=sizeof($vals);
	if($n>2)
	{
		if(!$avg)
			$avg=arr_avg($vals);

		$sum=array_sum($vals);
		
		$sq=0;
		foreach($vals as $val)
			$sq+=pow(($val-$avg),2);

		return (sqrt((1/($n-1))*$sq));
	}
	return "-";
}

// Funzione che calcola il coefficiente di correlazione campionaria tra due test
// dati gli id
function calc_r($id1,$id2,$cond="")
{
	 $ret1=query("SELECT AVG(valore) AS avg,STD(valore) AS std FROM PROVE".$cond['tabs']." WHERE fk_test=$id1".$cond['rstr']);
	 $t1=$ret1->fetch_assoc();
	
	 $ret2=query("SELECT AVG(valore) AS avg,STD(valore) AS std FROM PROVE".$cond['tabs']." WHERE fk_test=$id2".$cond['rstr']);
     $t2=$ret2->fetch_assoc();
    
	$retvals=r_vals($id1,$id2,$cond);

	$r['n']=$retvals->num_rows;
    if($r['n']>30) // Non è indicativo con pochi dati
    {
    	// Calcolo del coefficiente di correlazione campionaria r
    	// s=sum((x-avgx)*(y-avg(y))
    	$s=0;
    	while($row=$retvals->fetch_assoc())
        	$s+=($row['v1']-$t1['avg'])*($row['v2']-$t2['avg']);
        
        $r['r']=number_format($s/(($retvals->num_rows-1)*$t1['std']*$t2['std']),5);
    }
    else
      	$r['r']="-";

	return $r;
}

// Construction of additional restrictions based on GET data
function cond_builder()
{
	// Constuction of the class list
	$cond['class'] = "";
	for($i = 1; $i <= 5; $i++)
    	if(isset($_GET['c'.$i]))
       		$cond['class'] .= ", $i";

	// Construction of the gender list
	$cond['sex'] = "";
	if(isset($_GET['m']))
    	$cond['sex'] .= ", 'm'";
	if(isset($_GET['f']))
    	$cond['sex'] .= ", 'f'";
	
	// Year-related restrictions
	$cond['year1'] = $_GET['year1'];
	$cond['year2'] = $_GET['year2'];

	// Restriction on the teacher
	if(isset($_GET['rstr']))
    	$cond['prof'] = "AND fk_prof=?";
	else
		$cond['prof'] = "";

	return $cond;
}

// Funzione per ottenere i record e la lista di studenti che hanno realizzato il record positivo
function get_records($cond="")
{
	$ret=query("SELECT nomet,passo,pos,MAX(valore) AS max,MIN(valore) AS min FROM PROVE,TEST,TIPOTEST".$cond['tabs']." WHERE fk_test=id_test AND fk_tipot=id_tipot AND id_test=".$_GET['id'].$cond['rstr']);
	$row=$ret->fetch_assoc();

	if($row['pos']=="Maggiori")
	{
		$rcr['best']=$row['max'];
		$rcr['worst']=$row['min'];
	}
	else
	{
		$rcr['best']=$row['min'];
		$rcr['worst']=$row['max'];
	}

	// Controllo se sono stati selezionati dei dati, altrimenti la query crasha
	if($rcr['best']) 
    {
    	// Rimozione della doppia join su classi
    	$cond['tabs']=str_replace(",ISTANZE,CLASSI", "", $cond['tabs']);
		// Questa query viene fatta prima di modificare $rcr, valori utilizzati in seguito per la tabella
		$ret=query("SELECT nomescuola,id_cl,classe,sez,anno,fk_prof FROM PROVE,ISTANZE,CLASSI,SCUOLE".$cond['tabs']." WHERE fk_ist=id_ist AND fk_cl=id_cl AND fk_scuola=id_scuola AND fk_test=".$_GET['id'].$cond['rstr']." AND valore=".$rcr['best']." ORDER BY anno ASC");
    }
	if($rcr['best'] and $row['passo']<1)
	{
		$rcr['best']=number_format($rcr['best'],2);
		$rcr['worst']=number_format($rcr['worst'],2);
	}

	$rcr['list']="<table id='tbest' class='table table-striped'>";
	while($rcp=$ret->fetch_assoc())
	{
		$rcr['list'].="<tr><td>".$rcp['nomescuola']."</td><td>";
		if($rcp['fk_prof']==$_SESSION['id'])
  		{
    		$rcr['list'].="<a href='/registro/show_classe.php?id=".$rcp['id_cl']."'>";
    		$fl="</a>";
  		}
  		else
    		$fl="";
		$rcr['list'].=$rcp['classe'].$rcp['sez']." ".$rcp['anno']."/".($rcp['anno']+1)."$fl</td></tr>";
	}
	$rcr['list'].="</table>";

	return $rcr;
}

// Ottiene le statistiche aggiornate con la condizione
function get_stats($idtest,$cond="")
{
	$r=query("SELECT COUNT(valore) as n, ROUND(AVG(valore),2) AS avg, ROUND(STD(valore),2) AS std
	FROM PROVE".$cond['tabs']."
	WHERE fk_test=$idtest".$cond['rstr']);
	$ret=$r->fetch_assoc();

	$med=query("SELECT ROUND(AVG(T.valore),2) as med
		FROM (
			SELECT PROVE.valore, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum
  			FROM PROVE".$cond['tabs'].", (SELECT @rownum:=0) r
  			WHERE fk_test=$idtest".$cond['rstr']."
  			ORDER BY valore
		) as T
		WHERE T.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2))");
	return array_merge($ret,$med->fetch_assoc());
}

function graph_multibox($group,$cond="")
{
	// Rimozione della doppia join su classi
	$cond['tabs']=str_replace(",ISTANZE,CLASSI", "", $cond['tabs']);
	
	$ret=query("SELECT $group,valore FROM PROVE,ISTANZE,CLASSI".$cond['tabs']." WHERE fk_ist=id_ist AND fk_cl=id_cl AND fk_test=".$_GET['id'].$cond['rstr']." ORDER BY $group,valore ASC");
	while($row=$ret->fetch_assoc())
    	$graph[$row[$group]][]=$row['valore'];
	
	return $graph;
}

function graph_prc($cond="")
{
	$ret=query("SELECT COUNT(*) AS n,pos FROM PROVE,TEST".$cond['tabs']." WHERE fk_test=id_test AND id_test=".$_GET['id'].$cond['rstr']);
	$test=$ret->fetch_assoc();

	if($test['n']==0)
    {
    	$graph['lbls'][]=null;
    	return $graph;
    }

	$passo=100/$test['n'];
    if($test['pos']=="Maggiori")
    	$order="ASC";
	else
		$order="DESC";

	$i=1;
	$retvals=query("SELECT valore FROM PROVE".$cond['tabs']." WHERE fk_test=".$_GET['id'].$cond['rstr']." ORDER BY valore $order");
	while($val=$retvals->fetch_assoc())
    {
    	$graph['lbls'][]=number_format(($i/$test['n'])*100,2);
    	$graph['vals'][]=$val['valore'];
    	
    	$i++;
    }
	
	return $graph;
}

function graph_vals($cond="")
{
	$ret=query("SELECT valore FROM PROVE".$cond['tabs']." WHERE fk_test=".$_GET['id'].$cond['rstr']." ORDER BY valore ASC");
	while($row=$ret->fetch_assoc())
    	$graph['vals'][]=$row['valore'];
	return $graph;
}

//Appoggio per la funzione calc_r, query usata anche in altri casi
function r_vals($id1,$id2,$cond="")
{
	$cond['tabs']=str_replace(",ISTANZE,CLASSI","",$cond['tabs']);

    return query("SELECT DISTINCT (P1.fk_stud),P1.anno,v1,v2  FROM 
		(SELECT valore AS v1,anno,fk_stud from PROVE,ISTANZE,CLASSI".$cond['tabs']." WHERE fk_ist=id_ist AND fk_cl=id_cl AND fk_test=$id1".$cond['rstr']." ) AS P1 
		INNER JOIN 
		(SELECT valore AS v2,anno,fk_stud from PROVE,ISTANZE,CLASSI".$cond['tabs']." WHERE fk_ist=id_ist AND fk_cl=id_cl AND fk_test=$id2".$cond['rstr']." ) AS P2 
		USING (fk_stud,anno)");
}

?>