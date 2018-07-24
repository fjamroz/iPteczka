<?php 
    include("./inc/nagl.php");
    include("./inc/funkcje.php");
    session_start();                //poczatek sesji
    $conn = DbConnect();
    
    if(isset($_POST['login']) && !empty($_POST['login']) && isset($_POST['haslo']) && !empty($_POST['haslo'])){     //wczytanie strony za pierwszym razem
        if(Login($_POST['login'], $_POST['haslo'])){                                                                //wywolanie funkcji Login i sprawdzenie czy zwróci true
            $try = $conn->query('SELECT * FROM Uzytkownik WHERE Apteczka_id='.$_SESSION['idApteczki']);             //jesli tak to pobieramy wiersz z tabeli uzytkownicy o zadanym id_apteczki z tabeli Apteczka
            if ($try->num_rows > 0){                                                //jesli znajdzie w apteczce uzytkownikow
                header("Location: UserPanel.php");                                  //przerzuca na UserPanel
                exit;
            }else{
                header("Location: UserCreator.php");                                //jesli nie ma uzytkowników to wrzuca na UserCreator aby stworzyc pierwszego
                exit;
            }
        }else{
            $info2 = 1;
            
            //$info2 = '<br><font color="red">Logowanie nie powiodło się</font>';     //informacja dla uzytkownika
            //$info2 .= '<br><font color="red">Błędny e-mail lub hasło<br></font>';
            $_SESSION['info2'] = $info2;                                            //wpisanie do zmiennej globalnej poniewaz chcemy wyswietlic pod html-em
        }
    }else{
        $_SESSION['info2']=0;
    }

?>

<center>
<img src="img/Logo.png" alt="Tu powinno byc logo iPteczki">
</center>

<?php 
    echo $_SESSION['info'];             //wyswietlanie informacji o wyslaniu e-maila, uzycie zmiennej globalnej poniewaz definiowana jest w funkcji Login wywolywanej wyzej a chcemy informacje wyswietlac tutaj
?>

<div class="container">
    <form class="form-signin" action="Login.php" method="post">
      <center>
      <h2 class="form-signin-heading">Panel logowania</h2>
      </center>
      <label for="inputEmail" class="sr-only">Email</label>
      <input type="email" id="inputEmail" class="form-control" name="login" placeholder="E-mail" required autofocus>
      <label for="inputPassword" class="sr-only">Hasło</label>
      <input type="password" id="inputPassword" class="form-control" name="haslo" placeholder="Hasło" required>
          
      <button class="btn btn-lg btn-primary btn-block" type="submit">Zaloguj</button>
      
    </form>
        
        
</div>

<?php 
    
    //echo $_SESSION['info2'];                //wyswietlenie informacji o zlym loginie 
    if ($_SESSION['info2']) {
        echo '<center><div class="container"><div class="alert alert-danger alert-dismissible">
                  Nieprawidłowy login lub hasło.
                  </div></div></center>';
    }
    echo '<div class="container"><center><a href="Rejestracja.php">Nie masz jeszcze iPteczki? Załóż ją!</a><br>';
    //<a href="Przypomnij.php">Nie pamiętasz hasła? Przypomnimy Ci!</a><center></div>';
    $conn->close();
    include("./inc/stopka.php");

?>


