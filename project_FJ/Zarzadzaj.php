<?php 
    include("./inc/nagl.php");
    include("./inc/funkcje.php");
    session_start();
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
        
        header("Location: wyloguj.php");
    }
    $_SESSION['LAST_ACTIVITY'] = time();
    if(empty($_SESSION['idApteczki']) || empty($_SESSION['user'])){
        session_unset();
        session_destroy();
        header("Location: Login.php");
    }
    if(isset($_POST['anuluj'])){
        header("Location: Zarzadzaj.php");
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
            <li><a href="Historia.php">Historia</a></li>            
            <li><a href="Raport.php">Raport</a></li>
            <li class="active"><a href="#">Koszty<span class="sr-only">(current)</span></a></li>
            <li ><a href="Stan.php">Stan</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<?php  
        $menu = '<table>
                    <tr>
                        <th></th>
                        <th>Leki</th>
                        <th></th>
                        <th>Okres</th>
                    </tr>
                    <tr><form action="Zarzadzaj.php" method="post">
                    <th>Filtr: &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th>
                        <th>
                            <select class="form-control" name="operacja">       
	                            <option value="0">Dodane</option>
                                <option value="1">Pobrane</option>
                                <option value="2">Zutylizowane</option>          
                        </th>
                        <th>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp   od&nbsp&nbsp</th>
                        <th><input type="date" name="startOkres" /></th>
                        <th>&nbsp&nbsp&nbsp&nbsp   do&nbsp&nbsp</th>
                        <th><input type="date" name="koniecOkres"/></th>
                        <th>&nbsp&nbsp&nbsp&nbsp<button type="submit" class="btn btn-primary" name="szukaj">Pokaż</button></th>
                    </form></tr>                
                </table>';
        echo $menu;
        
        if(isset($_POST['szukaj'])){
            if(!empty($_POST['startOkres'] && $_POST['koniecOkres'])){
                if($_POST['startOkres'] <= $_POST['koniecOkres']){
                    $start = $_POST['startOkres'].' 00:00:00';
                    $koniec =  $_POST['koniecOkres'].' 23:59:59';
                    $conn=DbConnect();
                    $try0 = $conn->query('SELECT Lek.NazwaHandlowa, Lek.Postac, Log.Lek_id FROM Lek
                                    INNER JOIN Log ON Lek.KodKreskowy= Log.Lek_id
                                    WHERE Lek_id IN
                                                  (SELECT DISTINCT Lek_id FROM Log WHERE Apteczka_id ='.$_SESSION['idApteczki']. '
                                                   AND "'.$start .'" < data_wydania AND "'.$koniec.'" > data_wydania AND operacja = '.$_POST['operacja'].')
                                    GROUP BY Log.Lek_id ORDER BY Lek.NazwaHandlowa');
                    $try1 = $conn->query('SELECT Lek_id, ilosc, data_wydania, dcena, pozostalo FROM Log WHERE Apteczka_id='.$_SESSION['idApteczki']. ' AND
                                     Lek_id IN (SELECT DISTINCT Lek_id FROM Log WHERE Apteczka_id='.$_SESSION['idApteczki']. '
                                                 AND "'.$start .'" < data_wydania AND "'.$koniec.'" > data_wydania AND operacja = '.$_POST['operacja'].')
                                     AND operacja = '.$_POST['operacja'].' AND "'.$start .'" < data_wydania AND "'.$koniec.'" > data_wydania');
                    $_SESSION['test']='SELECT Lek_id, ilosc, data_wydania, cena, pozostalo FROM Log WHERE Apteczka_id='.$_SESSION['idApteczki']. ' AND
                                     Lek_id IN (SELECT DISTINCT Lek_id FROM Log WHERE Apteczka_id='.$_SESSION['idApteczki']. '
                                                 AND "'.$start .'" < data_wydania AND "'.$koniec.'" > data_wydania AND operacja = '.$_POST['operacja'].')
                                     AND operacja = '.$_POST['operacja'].' AND "'.$start .'" < data_wydania AND "'.$koniec.'" > data_wydania';
                    $conn->close();
                    if ($try0->num_rows > 0){
                        $i=0;
                        $j=0;
                        while($tmp = $try0->fetch_assoc()){
                            $array[$i]=$tmp['NazwaHandlowa'];
                            $array2[$i]=$tmp['Postac'];
                            $array3[$i++]=$tmp['Lek_id'];
                        } 
                        while($tmp2 = $try1->fetch_assoc()){
                            $tab[$j]=$tmp2['Lek_id'];
                            $tab2[$j]=$tmp2['ilosc'];
                            $tab4[$j]=$tmp2['dcena'];
                            $cena += $tmp2['dcena'];
                            $tab3[$j++]=$tmp2['data_wydania'];
                        }
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
        
        
        echo '<br>
              <h1 class="page-header">Raport finansowy</h1><div class="table-responsive">
                ';
        if(empty($array3)){
            echo '<hr style="width: 100%; color: black; height: 1px; background-color:black;" /><h3>Brak wyników</h3>';
        }else{
            echo '<h4 >Okres od <b>'. $_POST['startOkres'].'</b> do <b>'. $_POST['koniecOkres'].'</b></h4>';
           $cena =  number_format((float)$cena, 2, '.', '');
           switch ($_POST['operacja']){
               case 0:
                    $operacja = 'dodane';
                    break;
               case 1:
                   $operacja = 'pobrane';
                   break;
               case 2:
                   $operacja = 'zutylizowane';
                   break;
           }
            echo '<h3>Leki '.$operacja.'. Wartość: '.$cena.' zł</h3>';
            
        }
        //echo $_SESSION['test'];
        $i=0;
        foreach($array3 as $lek){
            $historia = '<div class="table-responsive">
            <table class="table table-striped" style="width: 30%">
                            
            <hr style="width: 100%; color: black; height: 1px; background-color:black;" />
            <h3>'.$array[$i].'  |  '.$array2[$i++].'</h3>
              <thead>
                <tr >
                  <th>Ilość</th>
                  <th>Data</th>
                  <th>Cena</th>
                </tr>
              </thead>
              <tbody>';
            $j=0;
            foreach($tab as $lek2){
                if($lek2 == $lek){
                    $historia .='<tr >
                        <td>'.$tab2[$j].'</td>
                        <td>'.$tab3[$j].'</td>
                        <td>'.$tab4[$j++].' zł</td>
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
         echo '   
        </div>
      </div>
    </div>';
?>


          <!-- <h2 class="sub-header">Section title</h2> -->
          
    
    <div class="modal fade" id="okres">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<form class="form-group" action="Zarzadzaj.php" method="post">
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
        			<form class="form-group" action="Zarzadzaj.php" method="post">
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