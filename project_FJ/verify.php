<center>
<?php 
    include("./inc/nagl.php");
    include("./inc/funkcje.php");
    $conn = DbConnect();
    if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){      //wczytanie strony za pierwszym razem
        $email = mysql_escape_string($_GET['email']);                   //przypisanie zmiennym wartosci aby łatwiej operować
        $hash = mysql_escape_string($_GET['hash']);
        
        $qry = ("SELECT * FROM Apteczka WHERE mail='".$email."' AND hash='".$hash."' AND status='0'") or die($conn->error());   //Tworzenie zapytania czy istnieje rekord o danym emialu, hashu i statusie=0
        $search = $conn->query($qry);                           //wywolywanie zapytania
        if ($search->num_rows > 0){                             //jesli znajdzie taki rekord to aktywuje apteczke jesli nie to poda informacje ze jest juz aktywowana
            $conn->query("UPDATE Apteczka SET status='1' WHERE mail='".$email."' AND hash='".$hash."' AND status='0'") or die($conn->error());
            echo '<img src="./img/Goodjob.png" alt="tu powinno byc zdjecie"><br><br>';
            echo '<div class="container"><div class="alert alert-success alert-dismissible">
             Twoja iPteczka została aktywowana</div></div>';
            echo '<a href="Login.php">Powrót do panelu logowania</a>';
        }else{
            echo '<img src="./img/Logo.png" alt="tu powinno być zdjęcie"><br><br><br>';   
            echo '<div class="container"><div class="alert alert-warning">
            Twoja iPteczka została już aktywowana wcześniej albo nasi informatycy znowu nawalili...<br>';
            echo '<a href="Login.php" style="text-decoration: none">Przejdź do panelu logowania i spróbuj się zalogować</a>, jeśli się nie da, napisz do naszego admina na e-mail: fjamroz@student.agh.edu.pl</div></div>';
        }

    }else{
        echo '<img src="./img/angry_pill.jpg" alt="tu miało być zdjęcie, ale nie ma"><h2>Nasi informatycy znowu nawalili</h2>';
        echo '<br><a href="Login.php" style="text-decoration: none">Przejdź do panelu logowania</a>';
    }
    $conn->close();
    include("./inc/stopka.php");
?>
</center>