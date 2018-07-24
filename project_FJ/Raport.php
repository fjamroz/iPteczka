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
        header("Location: Raport.php");
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
            <li ><a href="Uzytkownicy.php">Użytkownicy</a></li>
            <li><a href="Historia.php">Historia</a></li>
            <li class="active"><a href="Raport.php">Raport<span class="sr-only">(current)</span></a></li>
            <li><a href="Zarzadzaj.php">Koszty</a></li>
            <li ><a href="Stan.php">Stan</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<?php  
        //stworzenie filtra 
        $menu = '<table>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Okres</th>
                    </tr>
                    <tr><form action="Raport.php" method="post">
                    <th>Filtr:</th>
                        <th>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp   od&nbsp&nbsp</th>
                        <th>
                            <select class="form-control" name="startMiesiac">       
	                            <option value="01">Styczeń</option>
                                <option value="02">Luty</option>
                                <option value="03">Marzec</option>
                                <option value="04">Kwiecień</option>  
                                <option value="05">Maj</option>  
                                <option value="06">Czerwiec</option>  
                                <option value="07">Lipiec</option>  
                                <option value="08">Sierpień</option>  
                                <option value="09">Wrzesień</option>  
                                <option value="10">Październik</option>  
                                <option value="11">Listopad</option>  
                                <option value="12">Grudzień</option>            
                        </th>
                        <th><input type="number" name="startRok" placeholder="Rok..."</th>
                        <th>&nbsp&nbsp&nbsp&nbsp   do&nbsp&nbsp</th>
                        <th>
                            <select class="form-control" name="koniecMiesiac">       
	                            <option value="01">Styczeń</option>
                                <option value="02">Luty</option>
                                <option value="03">Marzec</option>
                                <option value="04">Kwiecień</option>  
                                <option value="05">Maj</option>  
                                <option value="06">Czerwiec</option>  
                                <option value="07">Lipiec</option>  
                                <option value="08">Sierpień</option>  
                                <option value="09">Wrzesień</option>  
                                <option value="10">Październik</option>  
                                <option value="11">Listopad</option>  
                                <option value="12">Grudzień</option>            
                        </th>
                        <th><input type="number" name="koniecRok" placeholder="Rok..."</th>
                        <th>&nbsp&nbsp&nbsp&nbsp<button type="submit" class="btn btn-primary" name="szukaj">Pokaż</button></th>
                    </form></tr>                
                </table>';
        echo $menu;
        
        //po wcisnieciu przycisku 'pokaz'
        if(isset($_POST['szukaj'])){
            $start = $_POST['startRok'].'-'.$_POST['startMiesiac'].'-01 00:00:00';      //zmienne pomocnicze
            $stop = $_POST['koniecRok'].'-'.$_POST['koniecMiesiac'].'-31 23:59:59';
            if(!empty($_POST['startRok'] && $_POST['koniecRok'] && $_POST['startMiesiac'] && $_POST['koniecMiesiac'])){
                if($start <= $stop){
                    $conn=DbConnect();
                    //zapytanie wyciagajace rozne leki dla danego czasu
                    $try0 = $conn->query('SELECT Lek.NazwaHandlowa, Lek.Opakowanie, Log.Lek_id FROM Lek
                                    INNER JOIN Log ON Lek.KodKreskowy= Log.Lek_id
                                    WHERE Lek_id IN
                                                  (SELECT DISTINCT Lek_id FROM Log WHERE Apteczka_id ='.$_SESSION['idApteczki']. '
                                                   AND "'.$start .'" < data_wydania AND "'.$stop.'" > data_wydania)
                                    GROUP BY Log.Lek_id ORDER BY Lek.NazwaHandlowa');
                    //zapytanie wyciagajace ilosci dla danych lekow z przedzialu czasowego
                    $try1 = $conn->query('SELECT Lek_id, ilosc, data_wydania, operacja FROM Log WHERE Apteczka_id='.$_SESSION['idApteczki']. ' AND
                                     Lek_id IN (SELECT DISTINCT Lek_id FROM Log WHERE Apteczka_id='.$_SESSION['idApteczki']. '
                                                 AND "'.$start .'" < data_wydania AND "'.$stop.'" > data_wydania)
                                      AND "'.$start .'" < data_wydania AND "'.$stop.'" > data_wydania');
                    $conn->close();
                    //wyciagniecie do tabeli tych danych
                    if ($try0->num_rows > 0){
                        $i=0;
                        $j=0;
                        while($tmp = $try0->fetch_assoc()){
                            $array[$i]=$tmp['NazwaHandlowa'];
                            $array3[$i][0]=$tmp['Lek_id'];
                            $array3[$i][1]=0;
                            $array3[$i][2]=0;
                            $array2[$i++]=$tmp['Opakowanie'];
                            
                        } 
                        while($tmp2 = $try1->fetch_assoc()){
                            $tab[$j]=$tmp2['Lek_id'];
                            $tab2[$j]=$tmp2['ilosc'];
                            $tab4[$j]=$tmp2['operacja'];
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
              <h1 class="page-header">Raport przychodu/rozchodu</h1><div class="table-responsive">';
        if(empty($_POST['startRok'])){
           echo '<hr style="width: 100%; color: black; height: 1px; background-color:black;" /><h3>Brak wyników</h3>';
        }
        

        $i=0;
        $j=1;
        
        $zmnRok = $_POST['startRok'];
        $zmn = $_POST['startMiesiac'];
        $miesiac = array("Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień");
       
        while($start<$stop)
        {
            $spr=0;
            //zapisanie zmiennych w formie znormalizowanej zamiast '1' to '01'
            $pomoc1= $zmnRok.'-'.str_pad($zmn, 2, "0", STR_PAD_LEFT).'-01 00:00:00';
            $pomoc2= $zmnRok.'-'.str_pad($zmn+1, 2, "0", STR_PAD_LEFT).'-01 00:00:00';
            $historia = '<div class="table-responsive">
            <table class="table table-striped" style="width: 50%">
                            
            <hr style="width: 100%; color: black; height: 1px; background-color:black;" />
            <h3>'.$miesiac[$zmn-1].'  |  '.$zmnRok.'</h3>
              <thead>
                <tr >
                  <th style="width: 60%">Lek</th>
                  <th>Przychód</th>
                  <th>Rozchód</th>
                </tr>
              </thead>
              <tbody>';
            
            $l=0;
            foreach($array as $lek){
                $k=0;
                foreach($tab as $lek2){
                    if($lek2 == $array3[$l][0]){
                        if($tab3[$k]>$pomoc1 && $tab3[$k]<$pomoc2){
                            if($tab4[$k]==0){                       //sumowanie rozchodu i przychodu
                                $array3[$l][1] += $tab2[$k];
                            }else{
                                $array3[$l][2] += $tab2[$k];
                            }
                        }
                    }
                    $k++;
                }
                if($array3[$l][1]==0 && $array3[$l][2]==0){
                    
                }else{ //wypisanie rekordow
                    $historia .='<tr >
                        <td>'.$array[$l].'   |   '.$array2[$l].'</td>
                        <td>'.$array3[$l][1].'</td>
                        <td>'.$array3[$l][2].'</td>
                    </tr>';
                    $array3[$l][1]=0;
                    $array3[$l][2]=0;
                    $spr++;    
                }
                $l++;
            }
            if($spr==0){
                $historia .='<tr >
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>';
            }
            $historia .='</tbody>
            </table>
          </div>';
            echo $historia;
            //przekrecenie roku
            if($zmn == 12 ){
                $zmnRok++;
                $zmn = 0;
                $i=0;
            }
            $zmn+=1;
             $start = $zmnRok.'-'.str_pad($zmn, 2, "0", STR_PAD_LEFT).'-01 00:00:00';
             $i++;
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
        			<form class="form-group" action="Raport.php" method="post">
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
        			<form class="form-group" action="Raport.php" method="post">
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