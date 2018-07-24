<?php 
    include("./inc/nagl.php");
    include("./inc/funkcje.php");
    session_start();   
    if(empty($_SESSION['idApteczki']) || empty($_SESSION['user'])){
        session_unset();
        session_destroy();
        header("Location: Login.php");
    }
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
        
        header("Location: wyloguj.php");
    }
    $_SESSION['LAST_ACTIVITY'] = time();
    if(isset($_POST['anuluj'])){
        header("Location: Historia.php");
    }
    // po wcisnięciu 'Pokaż'
    if(isset($_POST['szukaj'])){
        unset($_SESSION['nazwaHistoria']);
        unset($_SESSION['postacHistoria']);
        unset($_SESSION['lekidHistoria']);
        unset($_SESSION['lekid2Historia']);
        unset($_SESSION['iloscHistoria']);
        unset($_SESSION['dataHistoria']);
        unset($_SESSION['userPokaz']);
        unset($_SESSION['start']);
        unset($_SESSION['stop']);
        
        if(!empty($_POST['startOkres'] && $_POST['koniecOkres'])){
            if($_POST['startOkres'] <= $_POST['koniecOkres']){
                $start = $_POST['startOkres'].' 00:00:00';
                $koniec =  $_POST['koniecOkres'].' 23:59:59';
                $_SESSION['start']=$_POST['startOkres'];
                $_SESSION['stop']=$_POST['koniecOkres'];
                $conn=DbConnect();
                //wyciaga unikatowe leki z bazy
                $try = $conn->query('SELECT Lek.NazwaHandlowa, Lek.Postac, Log.Lek_id FROM Lek
                                    INNER JOIN Log ON Lek.KodKreskowy= Log.Lek_id
                                    WHERE Lek_id IN
                                                  (SELECT DISTINCT Lek_id FROM Log WHERE Uzytkownik_id ='.$_POST['userHistoria']. '
                                                   AND "'.$start .'" < data_wydania AND "'.$koniec.'" > data_wydania AND operacja = 1)
                                    GROUP BY Log.Lek_id ORDER BY Lek.NazwaHandlowa');
                //wyciaga informacje dla uzytkownika
                $try2 = $conn->query('SELECT Lek_id, ilosc, data_wydania FROM Log WHERE Uzytkownik_id ='.$_POST['userHistoria'].' AND
                                     Lek_id IN (SELECT DISTINCT Lek_id FROM Log WHERE Uzytkownik_id ='.$_POST['userHistoria']. '
                                                 AND "'.$start .'" < data_wydania AND "'.$koniec.'" > data_wydania AND operacja = 1)
                                     AND operacja = 1 AND "'.$start .'" < data_wydania AND "'.$koniec.'" > data_wydania');
                
                $try3 = $conn->query('SELECT nazwa FROM Uzytkownik WHERE id ='.$_POST['userHistoria']);
                
                $conn->close();
                $tmp3 = $try3->fetch_assoc();
                $_SESSION['userPokaz']=$tmp3['nazwa'];
                $i=0;
                $j=0;
                //wpisanie do sesji rzeczy wyciagnietych z zapytan
                if ($try->num_rows > 0){
                    while($tmp = $try->fetch_assoc()){
                        $array[$i]=$tmp['NazwaHandlowa'];
                        $array2[$i]=$tmp['Postac'];
                        $array3[$i++]=$tmp['Lek_id'];
                    }
                    $_SESSION['nazwaHistoria']=$array;
                    $_SESSION['postacHistoria']=$array2;
                    $_SESSION['lekidHistoria']=$array3;
                    
                    while($tmp2 = $try2->fetch_assoc()){
                        $tab[$j]=$tmp2['Lek_id'];
                        $tab2[$j]=$tmp2['ilosc'];
                        $tab3[$j++]=$tmp2['data_wydania'];
                    }
                    $_SESSION['lekid2Historia']=$tab;
                    $_SESSION['iloscHistoria']=$tab2;
                    $_SESSION['dataHistoria']=$tab3;
                }else{
                    $_SESSION['Brak']=1;
                }
                
               
            }else{
                echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#okresWrong").modal("show");
              });
              </script>';
            }
            
        }else{
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#okres").modal("show");
              });
              </script>';
        }
        
    }else{
        
    }
    
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="Apteczka.php">iPteczka</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
           <ul class="nav navbar-nav navbar-left">
<?php 
        if(!empty($_SESSION['uwaga'])){
            echo '<li><a class="navbar-link"><span class="label label-danger"> Leki po terminie: '.$_SESSION['uwaga'].'</span></a></li>';
        }        
?>
          </ul>
          <ul class="nav navbar-nav navbar-right">
          	<li><a><?php echo 'Zalogowano jako ' . $_SESSION['user'];?></a></li>
          	<li><a href="UserPanel.php">Zmień użytkownika</a></li>
          	<li><a href="wyloguj.php">Wyloguj</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li><a href="Apteczka.php">Przegląd</a></li>
            <li><a href="Uzytkownicy.php">Użytkownicy</a></li>
            <li class="active"><a href="#">Historia <span class="sr-only">(current)</span></a></li>
            <li><a href="Raport.php">Raport</a></li>
            <li><a href="Zarzadzaj.php">Koszty</a></li>
            <li ><a href="Stan.php">Stan</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<?php  
        $conn=DbConnect();
        //wyviagniecie uzytkownikow ktoryz nie zostali usunieci
        $try = $conn->query('SELECT * FROM Uzytkownik WHERE Apteczka_id='.$_SESSION['idApteczki'].' AND dostep < 4');
        $i=0;
        $e=0;
        while($row = $try->fetch_assoc()){
            $array[$i]=$row['nazwa'];
            $array2[$i++]=$row['id'];
        }
        $conn->close();
        //tabeli filtru
        $menu = '<table>
                    <tr>
                        <th>Uzytkownik</th>
                        <th></th>
                        <th>Okres</th>
                    </tr>
                    <tr><form action="Historia.php" method="post">
                        <th>
                            <select class="form-control" name="userHistoria">';
                                foreach($array as $name){
	                            $menu .= '<option value="'.$array2[$e++].'">'.$name.'</option>';
	                            }
                        $menu .= '</th>
                        <th>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp   od&nbsp&nbsp</th>
                        <th><input type="date" name="startOkres" /></th>
                        <th>&nbsp&nbsp&nbsp&nbsp   do&nbsp&nbsp</th>
                        <th><input type="date" name="koniecOkres"/></th>
                        <th>&nbsp&nbsp&nbsp&nbsp<button type="submit" class="btn btn-primary" name="szukaj">Pokaż</button></th>
                    </form></tr>                
                </table>';
        echo $menu;

?>
		<br>
          <h1 class="page-header">Historia użytkownika <?php echo $_SESSION['userPokaz'] ?></h1>
            
<?php 

$i=0;
//jesli nie ma aktywnosci uzytkownika w danym okresie
if(empty($_SESSION['lekidHistoria'])){
    echo '<hr style="width: 100%; color: black; height: 1px; background-color:black;" /><h3>Brak aktywności</h3>';
}else{
    echo '<h4 >Okres od <b>'. $_SESSION['start'].'</b> do <b>'. $_SESSION['stop'].'</b></h4>';
}
//tabel z historia uzytkownika
foreach($_SESSION['lekidHistoria'] as $lek){
    $historia = '<div class="table-responsive">
            <table class="table table-striped" style="width: 30%">
            
            <hr style="width: 100%; color: black; height: 1px; background-color:black;" />
            <h3>'.$_SESSION['nazwaHistoria'][$i].'  |  '.$_SESSION['postacHistoria'][$i++].'</h3>
              <thead>
                <tr >
                  <th>Pobrał</th>
                  <th style="width: 45%">Data</th>
                </tr>
              </thead>
              <tbody>';
    $j=0;
    foreach($_SESSION['lekid2Historia'] as $lek2){
        if($lek2 == $lek){
          $historia .='<tr >
                        <td>'.$_SESSION['iloscHistoria'][$j].'</td>
                        <td>'.$_SESSION['dataHistoria'][$j++].'</td>
                    </tr>';
        }else{
            $j++;
        }
    }
 
  $historia .='</tbody>
            </table>
          </div>';
    echo $historia;
}

?>
    
        </div>
      </div>
      
    </div>
    <div class="modal fade" id="okres">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<form class="form-group" action="Historia.php" method="post">
                        <!-- Modal Header -->
        				<div class="modal-header">
        					<h4 class="modal-title"></h4>
        				</div>
        
                        <!-- Modal body -->
        				<div class="modal-body">
        					<h3 class="modal-title">Proszę wybrać okres</b></h3> 
        				</div>
        
                        <!-- Modal footer -->
        				<div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="anuluj">OK</button>
        				</div>
        			</form>
        		</div>
        	</div>
        </div>
        
        <div class="modal fade" id="okresWrong">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<form class="form-group" action="Historia.php" method="post">
                        <!-- Modal Header -->
        				<div class="modal-header">
        					<h4 class="modal-title"></h4>
        				</div>
        
                        <!-- Modal body -->
        				<div class="modal-body">
        					<h3 class="modal-title">Koniec okresu nie może być wcześniej niż początek</b></h3> 
        				</div>
        
                        <!-- Modal footer -->
        				<div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="anuluj">OK</button>
        				</div>
        			</form>
        		</div>
        	</div>
        </div>
  <?php
  include("./inc/stopka.php");
  ?>