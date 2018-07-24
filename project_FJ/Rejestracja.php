<?php 
    include("./inc/nagl.php");
    include("./inc/funkcje.php");

?>
<center>
<img src="./img/Logo.png" alt="Tu powinno byc logo iPteczki"> 

<?php 
    
    $conn = DbConnect();
    $email  = trim($_POST['email']);
    $haslo = trim($_POST['haslo']);
    $check = "SELECT * FROM Apteczka WHERE mail='$email'";
    $checkResult = $conn->query($check);
    
    if(isset($email) && !empty($email) && isset($haslo) && $haslo==$_POST['haslo2']){
        $email = mysql_escape_string($_POST['email']);
        $haslo = md5(mysql_escape_string($_POST['haslo']));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){                                                //sprawdzenie czy zmienna $email zawiera formę emaila (xxx@xx.xx)
            $msg = 0;
        }else{
            if ($checkResult->num_rows > 0){                                                            //jesli znajdzie rekord
                $msg = 1;
            }else{
                $msg = 2;
                $hash = md5(rand(0,1000));                                                              //tworzenie hasha w celu weryfikacji
                $qry = ("INSERT INTO Apteczka (mail, haslo, status, hash) VALUES(       
                '". mysql_escape_string($email) ."',
                '". mysql_escape_string($haslo) ."',       
                '". mysql_escape_string(0) ."',
                '". mysql_escape_string($hash) ."') ") or die(mysql_error());                           //escape_string() konwertuje text na taki aby był czytany przez baze czyli niweluje wszelkie przerwy itp
                SendMail($email,$haslo,$hash); 
                if($conn->query($qry)===TRUE){                                                          //sprawdzenie czy jest true i czy sie typ danych zgadza 
                }
            }
        }
        
    }else{

    }
?>
	<div class="container">
	<form class="form-signin" action="Rejestracja.php" method="post">
		<h2 class="form-signin-heading">Panel rejestracji</h2>
		<input type="text" class="form-control" name="email" maxlength="50" placeholder="E-mail" required>
        <input type="password" class="form-control" name="haslo" maxlength="50" placeholder="Hasło" required>
        <input type="password" class="form-control" name="haslo2" maxlength="50" placeholder="Powtórz hasło" required>
        <input button class="btn btn-lg btn-primary btn-block" type="submit" name="akceptuj" value="Zarejestruj">
    </form>
    </div> 
<?php
    if (isset($msg)){
        if ($msg == 0) {
            echo '<center><div class="container"><div class="alert alert-danger alert-dismissible">
                      Podany e-mail jest niepoprawny. <br>Spróbuj ponownie.
                      </div></div></center>';
        
        }elseif ($msg == 1) {
            echo '<center><div class="container"><div class="alert alert-danger alert-dismissible">
                      Taka iPteczka już istnieje.
                      </div></div></center>';
        }else{
            echo '<center><div class="container"><div class="alert alert-success alert-dismissible">
                      Wysłaliśmy link aktywacyjny na podany e-mail<br>
                      Prosimy o aktywacje konta poprzez kliknięcie w link.
                      </div></div></center>';
        }
    }
    echo '<a href="Login.php">Powrót do panelu logowania</a>';
    $conn->close();
    include("./inc/stopka.php");
?>

    </center>