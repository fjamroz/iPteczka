<center>
<?php 

    //wyswietlane tylko podczas pierwszego uruchomienia apteczki aby stworzyc uzytkownika (admina)
    include("./inc/nagl.php");
    include("./inc/funkcje.php");
    session_start();
    $conn=DbConnect();
    if(empty($_SESSION['idApteczki'])){
        header("Location: Login.php");
    }
    
    if(isset($_POST['UserName']) && !empty($_POST['UserName'])){
        $qry=("INSERT INTO Uzytkownik (Apteczka_id, dostep, nazwa, haslo) VALUES (
            '". mysql_escape_string($_SESSION['idApteczki'])."',
            '". mysql_escape_string(1)."',
            '". mysql_escape_string($_POST['UserName'])."',
            '". mysql_escape_string(md5($_POST['UserPass']))."')");
        $conn->query($qry);
        $_SESSION['dostep']=1;
        $_SESSION['user']=$_POST['UserName'];
        $qry2 = 'SELECT id FROM Uzytkownik WHERE Apteczka_id = "'.$_SESSION['idApteczki'].'" AND nazwa = "'.$_POST['UserName'].'"';
        $_SESSION['userId']=$conn->query($qry2);        
        header('Location: Apteczka.php');
        exit;
    }else{
        
    } 
    
    echo '<img src="./img/Logo.png" alt="Tu powinno byc logo iPteczki">';
    echo '<div class="container"><H2 class="form-signin-heading">Pierwszy użytkownik</H2>';
    $form .= '<form class="form-signin" action="UserCreator.php" method="post">';
        $form .= '<label for="inputEmail" class="sr-only">Użytkownik:</label>
                  <input type="text"  class="form-control" name="UserName" placeholder="Nazwa" required autofocus>';
        $form .= '<label for="inputEmail" class="sr-only">Hasło</label>
                  <input type="text" class="form-control" placeholder="Hasło*" name="UserPass">';
       
        $form .= '<input class="btn btn-lg btn-primary btn-block" type="submit" value="Wejdź">';
        $form .= '* - niewymagane<br>';
    $form .= '</form>';
    echo $form;

    echo '<a href="wyloguj.php">Powrót do strony logowania</a><br>
            </div>';
    echo $_SESSION['login'];
    
        $conn->close();
        include("./inc/stopka.php");
?>
</center>