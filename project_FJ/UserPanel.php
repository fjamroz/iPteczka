<center>
<?php 
    //wybor uzytkownika po zalogowaniu się
    include("./inc/nagl.php");
    include("./inc/funkcje.php");
    session_start();
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
        
        header("Location: wyloguj.php");
    }
    $_SESSION['LAST_ACTIVITY'] = time();
    if(empty($_SESSION['idApteczki'])){
        header("Location: Login.php");
    }
    
	$conn=DbConnect();
	$try = $conn->query('SELECT * FROM Uzytkownik WHERE Apteczka_id='.$_SESSION['idApteczki'].' AND dostep < 3');  
	$i=0;
	while($row = $try->fetch_assoc()){
	    $array[$i++]=$row['nazwa'];										//to jest brzydkie ale dziala, stworzenie tabeli globalnej przechowywujacej informacje o uzytkownikach
	}
    
    echo '<img src="img/Logo.png" alt="Tu powinno byc logo iPteczki">';
    
    
    $form = '<div class="container">';
    $form .= '<form class="form-signin" action="UserPanel.php" method="post">';
    $form .= '<h2 class="form-signin-heading">Wybierz użytkownika</h2>';
	$form .= '<select class="form-control" name="User">';
	$form .= '<option value="0" selected>Wybierz...</option>';
	foreach($array as $name){
	    $form .= '<option value="'.$name.'">'.$name.'</option>';
	}
	$form .= '</select>';
    $form .= '<label for="inputUserPassword" class="sr-only">Hasło</label>';
    $form .= '<input type="password" id="inputUserPassword" class="form-control" name="UserPass" placeholder="Hasło">';
    $form .= '<button class="btn btn-lg btn-primary btn-block" type="submit">Wejdź</button>';
    $form .= '</form></div>';  
    
    echo $form;
    
    
    if(!$_POST['User']==0){
        $User = $_POST['User'];
        $haslo = $_POST['UserPass'];
        $haslo = trim($haslo);
        $qry = ('SELECT * FROM Uzytkownik WHERE nazwa="'.$User.'" AND Apteczka_id ='.$_SESSION['idApteczki']);
        $result = $conn->query($qry);
        $row = $result->fetch_assoc();
        $conn->close();
        if($row['haslo'] == md5($haslo)){
            header("Location: Apteczka.php");
            $_SESSION['dostep'] = $row['dostep'];
            $_SESSION['user'] = $row['nazwa'];
            $_SESSION['userId'] = $row['id'];
            exit;            
        }else{
            echo '<div class="container"><div class="alert alert-danger alert-dismissible">
                  Nieprawidłowe hasło.
                  </div></div>';
        }
    }elseif($_POST['User']==0 && !empty($_POST['UserPass'])){
        echo '<font color="red">Wybierz użytkownika</font>';   
    }else{
        
    }
        
    echo '<a href="wyloguj.php">Powrót do strony logowania</a><br>';
    echo $_SESSION['login'];

    $conn->close();
    include("./inc/stopka.php");
?>
</center>