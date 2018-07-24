<center>
<?php 
    include("./inc/nagl.php");
    include("./inc/funkcje.php");
    $conn = DbConnect();
    
?>
<!--  <img src="./img/Logo.png" alt="Tu powinno byc logo iPteczki">
<div class="container">
<form class="form-signin" action="Przypomnij.php" method="post">
	<h2 class="form-signin-heading">Odzyskiwanie hasła</h2>
	<input type="text" class="form-control" name="email" maxlength="50" placeholder="E-mail">
	<input button class="btn btn-lg btn-primary btn-block" type="submit" value="Wyślij">
</form>
</div>-->
<?php 

    if(isset($_POST['email']) && !empty($_POST['email'])){              //wczytanie strony za pierwszym razem
        $email = $_POST['email'];                                       //dla ułatwienia przyjecie zmiennej $email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {               //sprawdzenie czy zmienna $email zawiera formę emaila (xxx@xx.xx)
            echo '<font color="red">E-mail który podałeś jest niepoprawny, spróbuj ponownie<br>    </font>';
        }else{
            $qry = "SELECT * FROM Apteczka WHERE mail='$email'";        //tworzymy zapytanie wyciagające z bazy caly rekord dla podanego emaila
            $result = $conn->query($qry);                               //wykonanie zapytania
            if ($result->num_rows > 0){                                 //true jesli znajdzie
                $row = $result->fetch_assoc();                          //wpisanie calego rekordu do tablicy?
                SendPass($row['mail'],$row['haslo']);                   //funkcja wysylajaca haslo na podany email                 
            }else{
            }
            echo '<center><div class="container"><div class="alert alert-success alert-dismissible">
                  Wysłaliśmy dane na podany e-mail: '.$email.'
                  </div></div></center>'; //informacja dla uzytkownika (nawet jesli podal zły mail to sie wyswietli, zabezpieczenie przed włamaniami)
        }
    }
    $conn->close();
    echo '<a href="Login.php">Powrót do panelu logowania</a>';
    include("./inc/stopka.php");
?>
</center>