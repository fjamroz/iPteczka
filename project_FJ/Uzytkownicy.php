<?php 
    include("./inc/nagl.php");
    include("./inc/funkcje.php");
    // strona do zarządzania użytkownikami 
    
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
    $_SESSION['nazwaUser']= trim($_POST['nazwaUser']);
    $nazwa=trim($_POST['nazwaUser']);
    
    if(isset($_POST['dodajUser'])){
        if($_SESSION['dostep'] < 2){
            if(!empty($nazwa) && $_POST['Uprawnienia']!="111"){
                if($_POST['haslo']==$_POST['haslo2']){
                    $conn = DbConnect();
                    $qry2 = ('SELECT * FROM Uzytkownik WHERE nazwa= "'. $nazwa .'" AND Apteczka_id = "'. $_SESSION['idApteczki'].'"');
                    $result = $conn->query($qry2);
                    if ($result->num_rows == 0){ 
                        $qry=("INSERT INTO Uzytkownik (Apteczka_id, dostep, nazwa, haslo) VALUES (
                    '". mysql_escape_string($_SESSION['idApteczki'])."',
                    '". mysql_escape_string($_POST['Uprawnienia'])."',
                    '". mysql_escape_string($_POST['nazwaUser'])."',
                    '". mysql_escape_string(md5($_POST['haslo']))."')");
                        $conn->query($qry);
                        $conn->close();
                        header("Location: Uzytkownicy.php");
                    }else{
                        echo '<script>
                      $(document).ready(function(){
                      // Show the Modal on load
                      $("#zajety").modal("show");
                      });
                      </script>';
                    }
                }else{
                    echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#wrongPass").modal("show");
              });
              </script>';
                }
            }else{
                echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#wrongUser").modal("show");
              });
              </script>';
            } 
        }else{
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#uprawnienia").modal("show");
              });
              </script>';
        }
    }elseif(isset($_POST['anuluj'])){
        header("Location: Uzytkownicy.php");
    }
    
    
    if(isset($_POST['dodajUser2'])){
        echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#dodajUser").modal("show");
              });
              </script>';
    }
    
    //zmiana nazwy
    if(isset($_POST['changeName'])){
        if($_SESSION['dostep']<2){
            if(!empty(trim($_POST['nowaNazwa']))){
                $conn = DbConnect();
                $qry = ('UPDATE Uzytkownik SET nazwa ="'.$_POST['nowaNazwa'].'" WHERE nazwa = "'.$_SESSION['choosenUser'].'" AND Apteczka_id = "'. $_SESSION['idApteczki'].'"');
                $conn->query($qry);
                $conn->close();
                if($_SESSION['choosenUser']==$_SESSION['user']){
                    $_SESSION['user']=$_POST['nowaNazwa'];
                }
                header("Location: Uzytkownicy.php");
            }else{
                echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#wrongName").modal("show");
              });
              </script>';
            }
        }else{
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#uprawnienia").modal("show");
              });
              </script>';
        }
    }
    
    //zmiana hasla
    if(isset($_POST['changePass'])){
        if($_SESSION['dostep']<2 || $_SESSION['user']==$_SESSION['choosenUser']){
            $conn = DbConnect();
            $qry = ('SELECT haslo FROM Uzytkownik WHERE nazwa= "'.$_SESSION['choosenUser'].'" AND Apteczka_id = "'.$_SESSION['idApteczki'].'"');
            $result = $conn->query($qry);
            $tmp = $result->fetch_assoc();
            if(trim($tmp['haslo']) == md5(trim($_POST['obeHaslo']))){
                if(trim($_POST['noweHaslo']) == trim($_POST['noweHaslo2'])){
                    $qry2 = ('UPDATE Uzytkownik SET haslo ="'.md5(trim($_POST['noweHaslo'])).'" WHERE nazwa = "'.$_SESSION['choosenUser'].'" AND Apteczka_id = "'.$_SESSION['idApteczki'].'"');
                    $conn->query($qry2);
                    header("Location: Uzytkownicy.php");  
                }else{
                    echo '<script>
                      $(document).ready(function(){
                      // Show the Modal on load
                      $("#wrongHaslo2").modal("show");
                      });
                      </script>';
                }
            }else{
                echo '<script>
                  $(document).ready(function(){
                  // Show the Modal on load
                  $("#wrongHaslo").modal("show");
                  });
                  </script>';
            }
            $conn->close();   
        }else{
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#uprawnienia").modal("show");
              });
              </script>';
        }
    }
    //zmiana uprawnien
    if(isset($_POST['changeUprawnienia'])){
        if($_SESSION['choosenUser']==$_SESSION['user']){
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#blokada").modal("show");
              });
              </script>';
        }else{
            if($_SESSION['dostep']<2){
                $conn = DbConnect();
                $qry = ('SELECT haslo FROM Uzytkownik WHERE nazwa= "'.$_SESSION['user'].'" AND Apteczka_id = "'.$_SESSION['idApteczki'].'"');
                $result = $conn->query($qry);
                $tmp = $result->fetch_assoc();
                if(trim($tmp['haslo']) == md5(trim($_POST['werHaslo']))){
                    $qry2 = ('UPDATE Uzytkownik SET dostep = "'.$_POST['noweUprawnienia'].'" WHERE nazwa= "'.$_SESSION['choosenUser'].'" AND Apteczka_id = "'.$_SESSION['idApteczki'].'"');
                    $conn->query($qry2);
                }else{
                    echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#wrongHaslo").modal("show");
              });
              </script>';
                }
               $conn->close();
            }else{
                echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#uprawnienia").modal("show");
              });
              </script>';
            }
        }
        
    }
    
    if(isset($_POST['usun'])){
        if($_SESSION['dostep']<2){
            if($_SESSION['choosenUser']==$_SESSION['user']){
                echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#blokada").modal("show");
              });
              </script>';
            }else{
                $conn = DbConnect();
                $qry = ('UPDATE Uzytkownik SET dostep = "9" WHERE nazwa= "'.$_SESSION['choosenUser'].'" AND Apteczka_id = "'.$_SESSION['idApteczki'].'"');
                $result = $conn->query($qry);
                $conn->close();
            }
        }else{
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#uprawnienia").modal("show");
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
            <li class="active"><a href="#">Użytkownicy<span class="sr-only">(current)</span></a></li>
            <li><a href="Historia.php">Historia</a></li>
            <li><a href="Raport.php">Raport</a></li>
            <li><a href="Zarzadzaj.php">Koszty</a></li>
            <li ><a href="Stan.php">Stan</a></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Lista użytkowników</h1>

          <!-- <h2 class="sub-header">Section title</h2> -->
<?php
session_start();
    ShowUsers($_SESSION['idApteczki']);  // pokazanie uzytownikow dla danej apteczki
    $i=0;
    //sprawdzanie dla ktorego uzytkownika zostal wcisniety przycisk
    foreach($_SESSION['btnUser'] as $zmn){
        if(isset($_POST["Enazwa$i"])){
            $_SESSION['choosenUser']=$zmn;
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#zmienNazwa").modal("show");
              });
              </script>';
        }elseif(isset($_POST["Ehaslo$i"])){
            $_SESSION['choosenUser']=$zmn;
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#zmienHaslo").modal("show");
              });
              </script>';
        }elseif(isset($_POST["Edostep$i"])){
            $_SESSION['choosenUser']=$zmn;
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#zmienDostep").modal("show");
              });
              </script>';
        }elseif(isset($_POST["Eusun$i"])){
            $_SESSION['choosenUser']=$zmn;
            echo '<script>
              $(document).ready(function(){
              // Show the Modal on load
              $("#usun").modal("show");
              });
              </script>';
        }
        $i++;
    }
    
    echo '<button type="button" class="btn btn-md btn-primary" data-toggle="modal" data-target="#dodajUser">Dodaj użytkownika</button>';
    echo '<br>';
?>
        </div>
      </div>
    </div>
    
    <div class="modal fade" id="dodajUser">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				<h4 class="modal-title">Dodaj użytkownika</h4>
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<label for="ean">*Nazwa:</label>
        				<input type="text" class="form-control" name="nazwaUser" id="nazwaUser" placeholder="Nazwa użytkownika"><br>
        				<label for="ean">Hasło:</label>
        				<input type="password" class="form-control" name="haslo" id="haslo" placeholder="Hasło"><br>
        				<label for="ean">Powtórz hasło:</label>
        				<input type="password" class="form-control" name="haslo2" id="haslo2" placeholder="Powtórz hasło"><br>
        				<label for="ean">*Uprawnienia:</label>
        				<select class="form-control" name="Uprawnienia"><br>
        					<option value="111" selected>Wybierz uprawnienia...</option>
        					<option value="1">Admin</option>
        					<option value="2">Zwykły użytkownik</option>
        					<option value="3">Pasywny użytkownik </option>
        				</select>
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                        <button type="submit" class="btn btn-primary" name="dodajUser">Dodaj użytkownika</button>
                        <h5>* - wymagane</h5	>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="wrongUser">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<h4 class="modal-title">Podaj nazwę użytkownika oraz/lub jego uprawnienia.</h4>
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                        <button type="submit" class="btn btn-primary" name="dodajUser2">Dodaj użytkownika</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="wrongPass">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<h4 class="modal-title">Hasła się nie zgadzają.</h4>
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                        <button type="submit" class="btn btn-primary" name="dodajUser2">Dodaj użytkownika</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="uprawnienia">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<h4 class="modal-title">Dodawać, zmieniać i usuwać użytkowników może jedynie Admin.</h4>
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">OK</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="zajety">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<h4 class="modal-title">Taki użytkownik już istnieje.</h4>
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                        <button type="submit" class="btn btn-primary" name="dodajUser2">Dodaj użytkownika</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="zmienNazwa">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				<h4 class="modal-title"><?php echo 'Użytkownik: '.$_SESSION['choosenUser'];?></h4>
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<label for="ean">Zmień nazwę na:</label>
        				<input type="text" class="form-control" name="nowaNazwa" id="nowaNazwa" placeholder="Nowa nazwa..." ><br> 				
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                        <button type="submit" class="btn btn-primary" name="changeName">Wykonaj</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="wrongName">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<h4 class="modal-title">Nazwa użytkownika nie może być pusta.</h4>
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">OK</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="zmienHaslo">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				<h4 class="modal-title"><?php echo 'Zmień hasło dla: '.$_SESSION['choosenUser'];?></h4>        				
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<label for="ean">Podaj obecne hasło</label>
        				<input type="password" class="form-control" name="obeHaslo" id="obeHaslo" placeholder="Obecne hasło..." ><br>
        				<label for="ean">Nowe hasło</label>
        				<input type="password" class="form-control" name="noweHaslo" id="noweHaslo" placeholder="Nowe hasło..." ><br>
        				<label for="ean">Powtórz nowe hasło</label>
        				<input type="password" class="form-control" name="noweHaslo2" id="noweHaslo" placeholder="Powtórz nowe hasło..." ><br>   
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                        <button type="submit" class="btn btn-primary" name="changePass">Zmień hasło</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="wrongHaslo">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<h4 class="modal-title">Hasło jest niepoprawne.</h4>
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">OK</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="wrongHaslo2">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<h4 class="modal-title">Wprowadzone hasła różnią się.</h4>
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">OK</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="zmienDostep">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				<h4 class="modal-title"><?php echo 'Zmień uprawnienia dla: '.$_SESSION['choosenUser'];?></h4>
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<label for="ean">Uprawnienia:</label>
        				<select class="form-control" name="noweUprawnienia"><br>
        					<option value="1">Admin</option>
        					<option value="2" selected>Zwykły użytkownik</option>
        					<option value="3">Pasywny użytkownik </option>
        				</select><br>
        				<h5>W celu weryfikacji, proszę wprowadzić hasło dla użytkownika: <?php echo $_SESSION['user'];?></h5>
        				<label for="ean">Hasło</label>
        				<input type="password" class="form-control" name="werHaslo" id="werHaslo" placeholder="Hasło..." ><br>
        				
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                        <button type="submit" class="btn btn-primary" name="changeUprawnienia">Zmień uprawnienia</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="blokada">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<h4 class="modal-title">Zmienianie własnych uprawnień jest zablokowane.</h4>
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">OK</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
	
	<div class="modal fade" id="usun">
        <div class="modal-dialog">
        	<div class="modal-content">
        		<form class="form-group" action="Uzytkownicy.php" method="post">
                    <!-- Modal Header -->
        			<div class="modal-header">
        				
        			</div>
        
                    <!-- Modal body -->
        			<div class="modal-body">
        				<h3 class="modal-title">Czy na pewno chcesz usunąć: <b><?php echo $_SESSION['choosenUser'];?></b></h3>
        			</div>
        
                    <!-- Modal footer -->
        			<div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
                        <button type="submit" class="btn btn-primary" name="usun">Usuń</button>
        			</div>
        		</form>
        	</div>	
        </div>
	</div>
  <?php
  include("./inc/stopka.php");
  ?>