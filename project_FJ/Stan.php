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
        header("Location: Stan.php");
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
            <li><a href="Raport.php">Raport</a></li>
            <li><a href="Zarzadzaj.php">Koszty</a></li>
            <li class="active"><a href="Stan.php">Stan<span class="sr-only">(current)</span></a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<?php  
    $conn = DbConnect();
    //wyciagniecie z lekow ktore sa w apteczce
    $try1 = $conn->query('
            SELECT Log.Lek_id, Lek.Postac, Lek.NazwaHandlowa 
            FROM Log 
            INNER JOIN Lek ON Log.Lek_id=Lek.KodKreskowy 
            WHERE id IN 
            ( 
                SELECT MAX(id) 
                FROM Log 
                WHERE (Lek_id) NOT IN 
                ( 
                    SELECT Lek_id
                    FROM Log 
                    WHERE pozostalo=0 
                ) 
                AND Apteczka_id='.$_SESSION['idApteczki']. '
                GROUP BY Lek_id, seria
                ORDER BY id 
            ) 
            GROUP BY Lek_id
            ORDER BY Lek.NazwaHandlowa');
    
    
    
    $conn->close();
    if ($try1->num_rows > 0){
        $i=0;
        while($tmp = $try1->fetch_assoc()){
            $array[$i]=$tmp['NazwaHandlowa'];
            $array2[$i++]=$tmp['Lek_id'];            
        }
    }
if(isset($_POST['szukaj'])){
    $conn = DbConnect();
    //wyciagniecie danych z lekow ktore sa w apteczce
    $try2 = $conn->query('
            SELECT ilosc, pozostalo, operacja, data_wydania
            FROM Log
            WHERE Apteczka_id = '.$_SESSION['idApteczki'].' AND Lek_id = '.$_POST['lek'].'
            ORDER BY data_wydania');
    $result1 = $conn -> query('SELECT DISTINCT seria FROM Log WHERE Apteczka_id = '.$_SESSION['idApteczki'].' AND Lek_id = '.$_POST['lek']);
    $s=0;
    while($seria = $result1->fetch_assoc()){
        $ser[$s++]= $seria['seria'];
    }
    $stanapteczki=0;
    foreach($ser as $zap){
        // wyciaganie ostatniego rekordu z kolumny 'pozostalo' dla takich samych lekow ale z inna seria
        $result2 = $conn -> query('SELECT pozostalo FROM Log WHERE id = (SELECT Max(id) FROM Log WHERE seria = "'.$zap.'" AND Apteczka_id = '.$_SESSION['idApteczki'].')');
        $kwiatek = $result2->fetch_assoc();
        $stanapteczki += $kwiatek['pozostalo'];
    }
        if ($try2->num_rows > 0){
        $i=0;
        while($tmp = $try2->fetch_assoc()){
            $tab[$i]=$tmp['ilosc'];
            $tab2[$i]=$tmp['pozostalo'];
            $tab3[$i]=$tmp['operacja'];
            $tab4[$i++]=$tmp['data_wydania'];
        }
    }
}
            //tworzenie tabeli
          $menu = '<table>
                    <tr>
                        <th></th>
                        <th>Lek</th>
                    </tr>
                    <tr><form action="Stan.php" method="post">
                    <th>Filtr: &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th>
                        <th>
                            <select class="form-control" name="lek" style="width: 200px">';
          $j=0;
          foreach($array as $zmn){
              $menu .= '<option value="'.$array2[$j++].'">'.$zmn.'</option>';
          }   
                        $menu .= '</th>
                        <th>&nbsp&nbsp&nbsp&nbsp<button type="submit" class="btn btn-primary" name="szukaj">Pokaż</button></th>
                    </form></tr>
                </table>';
        echo $menu;
        echo '<br>
              <h1 class="page-header">Stan apteczki</h1><div class="table-responsive">';

        echo '<div class="table-responsive">
                    <table class="table table-striped" style="width: 30%">
                        <tbody>
                            <tr>
                                <td>Liczba różnych leków w apteczce</td>
                                <td>'.$j.'</td>
                            </tr>
                        </tbody>
                    </table><hr style="width: 100%; color: black; height: 1px; background-color:black;" />
                  </div>';
        $k=0;
        foreach($array2 as $index){
            if($index == $_POST['lek']){
                break;
            }
            $k++;
        }
        $razem = '<div class="table-responsive">
                <h3>'.$array[$k].'</h3>
                <table class="table table-striped" style="width: 30%">
                    <thead>
                        <tr>
                            <th style="width:75%">Data</th>
                            <th>Ilość</th>
                        </tr>
                    </thead>
                    <tbody>';
                       
         //sumowanie rozchodu i przychodu i sprawdzanie z baza czy sie zgadza               
        $f=0;
        $dodatnie=0;
        $ujemne=0;
        foreach($tab as $Fabian){
            $razem .= '<tr><td>'.$tab4[$f].'</td>';
            if($tab3[$f]==0){
                $razem .= '<td>+'.$Fabian.'</td>';
                $dodatnie += $Fabian;
            }else{
                $razem .= '<td>-'.$Fabian.'</td>';
                $ujemne += $Fabian;
            }
            $razem.= '</tr>';
            $f++;
        }
        $wynik = $dodatnie-$ujemne; 
        if($wynik==$stanapteczki){
            $razem .='<tr class="okey"><td><b>SUMA</b></td>
                            <td><b>'.$wynik.'</b></td>
                            </tr>';
        }else{
            $razem .='<tr class="superdanger"><td><b>SUMA</b></td>
                            <td><b>'.$wynik.'</b></td>
                            </tr>';
        }
              $razem .='
                    </tbody>
                </table>
            </div>';
              if(!empty($_POST['lek'])){
                  echo $razem;
              }else{
                  echo '<h3>Brak wyników</h3>';
              }
        
         echo '   
        </div>
      </div>
    </div>';

  include("./inc/stopka.php");
  ?>