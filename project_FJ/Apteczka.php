<?php 
    include("./inc/nagl.php");
    include("./inc/funkcje.php");
	include("./inc/testy.php");
    session_start();
    //zapobiega wchodzeniu przez url do aplikacji
    if(empty($_SESSION['idApteczki']) || empty($_SESSION['user'])){     
        session_unset();
        session_destroy();
        header("Location: Login.php");
    }
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
        
        header("Location: wyloguj.php");
    }
    $_SESSION['LAST_ACTIVITY'] = time(); 
    
    //wcisniecie przycisku dodaj
    if (isset($_POST['dodaj'])){
        AddLek();
        session_start();
        unset($_SESSION['data']);
    }
    if(isset($_POST['wprowadz1'])){
        echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#dodajModal").modal("show");
              });
              </script>';
    }
    if(isset($_POST['usunLek'])){
        
        $dataWydania = date('Y-m-d H:i:s');
        $conn = DbConnect();
        $qry = 'INSERT INTO Log (Apteczka_id, Uzytkownik_id, Lek_id, operacja, ilosc, data_wydania, data_waznosci, seria, pozostalo, cena, dcena)
                VALUES ('.$_SESSION['idApteczki'].', '.$_SESSION['userId'].', "'.$_SESSION['choosenLekId'].'",
                         2, '.$_SESSION['choosenLekPozostalo'].', "'.$dataWydania.'",
                         "'.$_SESSION['choosenLekWaznosc'].'", "'.$_SESSION['choosenLekSeria'].'", 0, 0, '.$_SESSION['choosenLekCena'].' )';
        $result = $conn->query($qry);
        $conn->close();
        unset($_SESSION['choosenLekId']);
        unset($_SESSION['choosenLekSeria']);
        unset($_SESSION['choosenLekNazwa']);
        unset($_SESSION['choosenLekPozostalo']);
        unset($_SESSION['choosenLekWaznosc']);  
        unset($_SESSION['choosenLekCena']);
    }
    
    
    if(isset($_POST['wydajLek'])){
        if($_SESSION['choosenLekPozostalo']>=$_POST['iloscWydaj']){
        
        $dataWydania = date('Y-m-d H:i:s');
        $zostalo = $_SESSION['choosenLekPozostalo']-$_POST['iloscWydaj'];    
        $dla = $_POST['lekiWydaj'];
        //obliczenie wartosci wydanego leku
        $wartosc = $_POST['iloscWydaj'] * ($_SESSION['choosenLekCena'] / $_SESSION['choosenLekPozostalo']);
        $cena = $_SESSION['choosenLekCena'] - $wartosc;
        $conn = DbConnect();
        $qry0 = 'SELECT id FROM Uzytkownik WHERE nazwa ="'.$dla.'"';
        $result0 = $conn->query($qry0);
        $tmp = $result0->fetch_assoc();
        //wpisanie operacji do Logów        
        $qry = 'INSERT INTO Log (Apteczka_id, Uzytkownik_id, Lek_id, operacja, ilosc, data_wydania, data_waznosci, seria, pozostalo, cena, dcena)
                VALUES ('.$_SESSION['idApteczki'].', '.$tmp['id'].', "'.$_SESSION['choosenLekId'].'",
                         1, '.$_POST['iloscWydaj'].', "'.$dataWydania.'", "'.$_SESSION['choosenLekWaznosc'].'", 
                            "'.$_SESSION['choosenLekSeria'].'", '.$zostalo.', '.$cena. ', '.$wartosc.')';
        $result = $conn->query($qry);
        $conn->close();
        //wyczyszczenie sesji (sesja użyta aby przenosic informacje miedzy blokami html)
        unset($_SESSION['choosenLekId']);
        unset($_SESSION['choosenLekSeria']);
        unset($_SESSION['choosenLekNazwa']);
        unset($_SESSION['choosenLekPozostalo']);
        unset($_SESSION['choosenLekWaznosc']);
        unset($_SESSION['choosenLekCena']);
    }else{
        echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#zaduzo").modal("show");
              });
              </script>';
    }
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
        //Komunikat o przeterminowanych lekach lewy górny róg
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
            <li class="active"><a href="#">Przegląd <span class="sr-only">(current)</span></a></li>   
            <li><a href="Uzytkownicy.php">Użytkownicy</a></li>
            <li><a href="Historia.php">Historia</a></li>
            <li><a href="Raport.php">Raport</a></li>
            <li><a href="Zarzadzaj.php">Koszty</a></li>
            <li ><a href="Stan.php">Stan</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Lista leków</h1>
<?php
session_start();
//getApteczka wyciaga tablice lekow dla danej apteczki, ShowApteczka wyswietla wyciagniete leki
$leki = getApteczka($_SESSION['idApteczki']);
ShowApteczka($leki);

echo '<button type="button" class="btn btn-md btn-primary" data-toggle="modal" data-target="#dodajModal">Dodaj</button>';
echo '<br>';

	$i=0;
	//pętla odpowiedzialna za sprawdzanie dla ktorego leku jest wcisniety przycisk 'wydaj' lub 'utylizuj'
	foreach($leki as $zmn){
	    if(isset($_POST["wydaj$i"])){
	        $_SESSION['choosenLekId']=$zmn['Lek_id'];
	        $_SESSION['choosenLekSeria']=$zmn['seria'];
	        $_SESSION['choosenLekPozostalo']=$zmn['pozostalo'];
	        $_SESSION['choosenLekWaznosc']=$zmn['data_waznosci'];
	        $_SESSION['choosenLekNazwa']=$zmn['NazwaHandlowa'];
	        $_SESSION['choosenLekCena']=$zmn['cena'];
	        echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#wydajLek").modal("show");
              });
              </script>';
	    }elseif(isset($_POST["utylizuj$i"])){
	        $_SESSION['choosenLekId']=$zmn['Lek_id'];
	        $_SESSION['choosenLekSeria']=$zmn['seria'];
	        $_SESSION['choosenLekNazwa']=$zmn['NazwaHandlowa'];
	        $_SESSION['choosenLekPozostalo']=$zmn['pozostalo'];
	        $_SESSION['choosenLekWaznosc']=$zmn['data_waznosci'];
	        $_SESSION['choosenLekCena']=$zmn['cena'];
	        echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#usunLek").modal("show");
              });
              </script>';
	    }
	    $i++;
	}
?>	
        <div class="modal fade" id="dodajModal">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<form class="form-group" action="Apteczka.php" method="post">
                        <!-- Modal Header -->
        				<div class="modal-header">
        					<h4 class="modal-title">Wyszukaj lek jaki chcesz dodać</h4>
        				</div>
        
                        <!-- Modal body -->
        				<div class="modal-body">
        					<label for="ean">Nazwa leku:</label>
        					<input type="text" class="form-control" name="ean" id="ean" placeholder="Nazwa leku lub ean">
       					</div> 
       				
                        <!-- Modal footer -->
        				<div class="modal-footer">
        					<button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
        					<button type="submit" class="btn btn-primary" name="wprowadz0" autofocus>Wprowadź</button>
        				</div>
        			</form>
        		</div>
        	</div>
        </div>
        
        <div class="modal fade" id="wydajLek">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<form class="form-group" action="Apteczka.php" method="post">
                        <!-- Modal Header -->
        				<div class="modal-header">
        					<h4 class="modal-title">Wydawanie leku <br><b><?php echo $_SESSION['choosenLekNazwa'];?></b></h4>
        				</div>
        
                        <!-- Modal body -->
        				<div class="modal-body">
        					<label>Ilość:</label>
        					<input type="number" class="form-control" name="iloscWydaj" id="ilosc" placeholder="tab./gram/ml..." required>
        					<label>Dla kogo:</label>
<?php 
                            $conn = DbConnect();
                            // select dla kogo 
                            if ($conn -> connect_error){
                                die("Brak połączenia:" . $conn->connect_error . "<br>");
                            }
                            $qry = 'SELECT * FROM Uzytkownik WHERE Apteczka_id ='. $_SESSION['idApteczki'].' AND dostep = 3';
                            $result = $conn->query($qry);
                            $conn->close();
                            if ($result->num_rows > 0){                                                //jesli znajdzie w apteczce uzytkownikow
                                $i=0;
                                while($tmp = $result->fetch_assoc()){
                                    $array[$i]=$tmp['nazwa'];
                                   
                                }
                            }
        					$form = '<select class="form-control" name="lekiWydaj">';
        					$form .= '<option value='.$_SESSION['user'].' selected>'.$_SESSION['user'].'</option>';
        					foreach($array as $name){
        					    $form .= '<option value="'.$name.'">'.$name.'</option>';
        					}
        					$form .= '</select>';
        					echo $form;

?>    
        				</div>
        
                        <!-- Modal footer -->
        				<div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                            <button type="submit" class="btn btn-primary" name="wydajLek">Wydaj lek</button>
        				</div>
        			</form>
        		</div>
        	</div>
        </div>
        
        <div class="modal fade" id="usunLek">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<form class="form-group" action="Apteczka.php" method="post">
                        <!-- Modal Header -->
        				<div class="modal-header">
        					<h4 class="modal-title"></h4>
        				</div>
        
                        <!-- Modal body -->
        				<div class="modal-body">
        					<h3 class="modal-title">Czy na pewno chcesz zutylizować <br><b><?php echo $_SESSION['choosenLekNazwa'];?>?</b></h3> 
        				</div>
        
                        <!-- Modal footer -->
        				<div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                            <button type="submit" class="btn btn-primary" name="usunLek">Usuń</button>
        				</div>
        			</form>
        		</div>
        	</div>
        </div>
        
        <div class="modal fade" id="wrongModal">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<form class="form-group" action="Apteczka.php" method="post">
                        <!-- Modal Header -->
        				<div class="modal-header">
        					<h4 class="modal-title"></h4>
        				</div>
        
                        <!-- Modal body -->
        				<div class="modal-body">
        					<label >Podana nazwa jest za mało dokładna</label>    
        				</div>
        
                        <!-- Modal footer -->
        				<div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                            <button type="submit" class="btn btn-primary" name="wprowadz1">Wprowadź ponownie</button>
        				</div>
        			</form>
        		</div>
        	</div>
        </div>
        
        <div class="modal fade" id="zaduzo">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<form class="form-group" action="Apteczka.php" method="post">
                        <!-- Modal Header -->
        				<div class="modal-header">
        					<h4 class="modal-title"></h4>
        				</div>
        
                        <!-- Modal body -->
        				<div class="modal-body">
        					<label >Nie możesz wybrać więcej leku niż jest na stanie</label>    
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
    //dodawanie leku po eanie albo po nazwie
    if (isset($_POST['wprowadz0'])) {
        $conn = DbConnect();
        if(is_numeric($_POST['ean'])){
            $try = $conn->query('SELECT NazwaHandlowa, Postac, Opakowanie, KodKreskowy FROM Lek WHERE KodKreskowy like "%'.$_POST['ean'].'%"');
        }else{
            $try = $conn->query('SELECT NazwaHandlowa, Postac, Opakowanie, KodKreskowy FROM Lek WHERE NazwaHandlowa like "%' . $_POST['ean'] . '%"');
        }
        //jesli mniej niz 21 to nie pokazuje tylko odsyla do ponownego wpisania
        if ($try->num_rows > 0 && $try->num_rows < 21){
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#dodajModal2").modal("show");
              });
              </script>';  
        }else{
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#wrongModal").modal("show");
              });
              </script>'; 
        }       
    }elseif(isset($_POST['anuluj'])){
            header("Location: Apteczka.php");
    }
    
    
    
    
?>

    	<div class="modal fade" id="dodajModal2">
        	<div class="modal-dialog">
        		<div class="modal-content">
        			<form class="form-group" action="Apteczka.php" method="post">
                    <!-- Modal Header -->
        				<div class="modal-header">
        					<h4 class="modal-title">Informacje o dodawanym leku</h4>
        				</div>
            
                    <!-- Modal body -->
        				<div class="modal-body">
        					<label for="ean">Wynik wyszukiwania dla <?php echo $_POST['ean'];?></label>
<?php
                            //wypisywanie znalezionyc lekow
                            if ($try->num_rows > 0 && $try->num_rows < 21){                                                
                                $i=0;
                                $k=0;
                                while($row = $try->fetch_assoc()){
                                    $array[$i]=$row['NazwaHandlowa'];                     
                                    $array2[$i]=$row['Opakowanie'];
                    				$array3[$i]=$row['KodKreskowy'];
                    				$array4[$i]=$row['Postac'];
                    				$i++;
                                }
                                
                                $form = '<select class="form-control" name="LekiTest">';
                                $form .= '<option value="0" selected>Wybierz lek...</option>';
                                foreach($array as $name){
                                    $form .= '<option value="'.$array3[$k].'">'.$name.'    '.$array4[$k].'    '.$array2[$k++].'</option>';
                                }
                                
                                $form .= '</select>';
                                echo $form;
                            }
?>
                            <label for="ilosc">Ilość:</label>
                            <input type="number" class="form-control" name="ilosc" id="ilosc" placeholder="tab./gram/ml..." required>
                            <label for="seria">Seria:</label>
                            <input type="text" class="form-control" name="seria" id="seria" placeholder="Seria" required>
                            <label for="termin">Data ważności:</label>
                            <input type="date" class="form-control" name="termin" id="termin" placeholder="Data ważności" required>
                            <label for="cena">Cena:</label>
                            <input type="text" class="form-control" name="cena" id="cena" placeholder="Cena [zł]" required>
                            <!--  <input type="number" class="form-control" name="ean" id="ean" placeholder="EAN">-->
						</div>
            
                        <!-- Modal footer -->
        				<div class="modal-footer">
                        	<button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                            <button type="submit" class="btn btn-primary" name="wprowadz">Wprowadź</button>
        				</div>
        			</form>
        		</div>
        	</div>
        </div>
	  	  
<?php 
    


    if (isset($_POST['wprowadz'])) {
        echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#zatwierdzModal").modal("show");
              });
              </script>';
    }elseif(isset($_POST['anuluj'])){
		header("Location: Apteczka.php");
	}
?>
					<div class="modal fade in" id="zatwierdzModal">
 		  				<div class="modal-dialog">
    	  					<div class="modal-content">
							<form class="form-group" action="Apteczka.php" method="post">
                                <!-- Modal Header -->
          	  					<div class="modal-header">
              						<h4 class="modal-title">Informacje o dodawanym leku</h4>
              					</div>
    
                                <!-- Modal body -->
              					<div class="modal-body">
<?php 
    showInfoTest2();
?>
         	  					</div>
    
    	                       <!-- Modal footer -->
        	  				   <div class="modal-footer">
    						   		<button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
              						<button type="submit" class="btn btn-primary" name="dodaj">Zatwierdź</button>
      	  				   	</form>
      	  				   	</div>
   		  				   </div>
  		  			    </div>
	  	  			</div>
                </div>
              </div>
            </div>
  <?php
    include("./inc/stopka.php");
  ?>