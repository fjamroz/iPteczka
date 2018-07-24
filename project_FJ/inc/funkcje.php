<?php 
/* Plik z funkcjami występującymi w apteczce.
 * Jesli jest jakas funkcja w programie to definicja znajduje sie własnie tutaj*/

    function DbConnect(){
        $servername = "*****";               //łączenie sie z baza danych
        $username = "****";
        $password = "*****";
        $database = "****";
        $conn = new mysqli($servername, $username, $password, $database);
        $conn->set_charset("utf8");
    
        return $conn;
    }

// Sprawdza czy podany login i hasło znajdują się w bazie danych
function CheckLoginInDb($login, $haslo){

    $conn = DbConnect();
    
    if ($conn -> connect_error){                    //jesli sie nie połączy
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
    
    $qry = "SELECT * FROM Apteczka WHERE mail='$login'";    //zapytanie pobierajace cały rekord o podanym loginie
    $result = $conn->query($qry);                           //pobieranie z bazy danych za pomocą zapytania
$conn->close();
    if ($result->num_rows > 0){                             //warunek sprawdzajacy czy "cos" zostalo pobrane
        $row = $result->fetch_assoc();                      //wpisywanie danych z objektu do tablicy
    }else{
        $_SESSION['info']="";
        return false;
    }
    if($login == $row['mail'] && $haslo == $row['haslo']){  //porownanie loginu i hasła z bazą danych
        if($row['status']==1){                              //sprawdzenie czy apteczka zostala aktywowana
            $_SESSION['idApteczki'] = $row['id'];           //dodanie do zmiennej globalnej id apteczki
            return true;
        }else{
            $info ='<font color="red">Twoja iPteczka nie została jeszcze aktywowana</font><br>';
            $info .= '<font color="green">Wysłaliśmy ponownie link aktywacyjny na podany adres: '.$row['mail'];
            $info .= '</font><br><br>';
            $_SESSION['info'] = $info;                      
            SendMail($row['mail'],$row['haslo'],$row['hash']);  //wyslanie emaila aktywacyjnego
            return false;
        } 
    }else{  
        return false;
    }
}

//logowanie do systemu. Zwraca true jesli logowanie sie powiodło, false jesli nie
function Login($login, $haslo){
    $login = trim($login);                  //usuwanie białych znaków przed i po stringu
    $haslo = md5(trim($haslo));
    if(empty($login)){                      //sprawdzanie czy login jest pusty
        return false;
    }
    if(empty($haslo)){
        return false;
    }
    if(CheckLoginInDb($login,$haslo)){       //sprawdzenie w bazie danych loginu i hasla
        return true;
    }
    return false;
}

//Mail aktywacyjny
function SendMail($adresat, $haslo ,$hash){
    $tytul = 'iPteczka | Aktywacja konta';
    $tresc = '
Dziękujemy za założenie iPteczki!

Twoje konto zostało stworzone, ale potrzebujemy jeszcze Twojego potwierdzenia.
Aby aktywować iPteczke wejdź w podany link: 
http://www.student.agh.edu.pl/~fbogusz/siwm/project_FJ/verify.php?email='.$adresat.'&hash='.$hash.'



Jeśli to nie Ty zakładałeś konto na iPteczka to zignoruj tą wiadomość.

Wiadomość została wygenerowana automatycznie, nie odpowiadaj na nią. 
';
    $headers = 'From:noreply@iPteczka.pl' . "\r\n";
    mail($adresat,$tytul,$tresc,$headers);
}

//Mail wysyłajacy przypomnienie hasla
function SendPass($adresat, $haslo){                    
    $tytul = 'Referat';
    $tresc = '
Oto dane, o które prosiłeś
    
-----------------------------------------------
E-mail: '.$adresat.'
Hasło:  '.$haslo.'
-----------------------------------------------

Wiadomość została wygenerowana automatycznie, nie odpowiadaj na nią.
';
    $headers = 'From:noreply@iPteczka.pl' . "\r\n";
    mail($adresat,$tytul,$tresc,$headers);
}

//Wypisywanie leków z apteczki
function GetApteczka($idApteczki){
    $conn = DbConnect();
    
    if ($conn -> connect_error){                    //jesli sie nie połączy
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
    $qry = 
    "SELECT Log.Lek_id, Lek.Postac, Lek.NazwaHandlowa, Log.pozostalo, Log.seria, Log.data_waznosci, Log.cena 
    FROM Log 
    INNER JOIN Lek ON Log.Lek_id=Lek.KodKreskowy 
    WHERE id IN 
    ( 
        SELECT MAX(id) 
        FROM Log 
        WHERE (Lek_id, seria) NOT IN 
        ( 
            SELECT Lek_id, seria
            FROM Log 
            WHERE pozostalo=0 
        ) 
        AND Apteczka_id=$idApteczki
        GROUP BY Lek_id, seria
        ORDER BY id 
    ) 
    ORDER BY Lek.NazwaHandlowa";
        
    //$qry = "SELECT Lek_id, pozostalo, seria, data_waznosci FROM Log WHERE Apteczka_id=$idApteczki";
    $result = $conn->query($qry);
    $conn->close();
    $out = array();
    while ($row = $result->fetch_assoc()) {
        $out[] = $row;
    }
    return $out;
}

//Ta funkcja już niepotrzebna, można sobie poradzić przy pomocy komend SQL, np. JOIN
function ConvertEAN($ean) {
    $conn = DbConnect();
    
    if ($conn -> connect_error){                    //jesli sie nie połączy
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
    $qry = "SELECT NazwaHandlowa, PodmiotOdpowiedzialny, Postac, Dawka, Opakowanie FROM Lek WHERE KodKreskowy LIKE '%".$ean."%'";
    $result = $conn->query($qry);
    $conn->close();
    if ($result) {
        $dane = $result->fetch_assoc();
        return $dane;
    }else{
        return 0;
    }
    
}

function ConvertName($name) {
    $conn = DbConnect();
    
    if ($conn -> connect_error){                    //jesli sie nie połączy
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
    $qry = "SELECT KodKreskowy, PodmiotOdpowiedzialny, Postac, Dawka, Opakowanie FROM Lek WHERE NazwaHandlowa LIKE '%".$name."%'";
    $result = $conn->query($qry);
    $conn->close();
    if ($result) {
        $dane = $result->fetch_assoc();
        return $dane;
    }else{
        return 0;
    }
    
}

function ShowApteczka($leki){
    $dateToday = date('Y-m-d');
    $table =
    '<div class="table-responsive">
        <table class="table table-striped">
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Ilość</th>
                <th>Seria</th>
                <th>Termin ważności</th>
                <th>Akcja</th>
            </tr>
        </thead>
        <tbody>';
	$k = 0;
	$i = 0;
    foreach($leki as $row){
        $termin = new DateTime($row['data_waznosci']);
        $dzis = new DateTime($dataToday);
        $diff = $dzis->diff($termin);
        $wynik = $diff->format('%R%a');
        $table .= '<form action="Apteczka.php" method="post">';
        $table .= '<tr';
        
        if ($wynik < 0){
            $table .= ' class="superdanger">';
            $i++;
        }elseif ($wynik < 30){
        $table .= ' class="superwarning">';}
        else{
            $table .= '>';
        }
        $_SESSION['uwaga']=$i;
        $table .=  '<td>'.$row['NazwaHandlowa'].' |  '.$row['Postac'].'</td>
                    <td>'.$row['pozostalo'].'</td>
                    <td>'.$row['seria'].'</td>
                    <td>'.$row['data_waznosci'].'</td>
                    <td><button class="btn btn-sm btn-primary" type="submit" name="wydaj'.$k.'" >Wydaj</button>
                    <button class="btn btn-sm btn-primary" type="submit" name="utylizuj'.$k++.'" >Utylizuj</button></td>
                   </tr></form>';
    }              
    $table .= '</tbody></table></div>';
    echo $table;
    
    //SELECT MAX(id), ilosc, seria, data_waznosci FROM Log WHERE (Lek_id, seria, data_waznosci) NOT IN (SELECT Lek_id, seria, data_waznosci FROM Log WHERE pozostalo=0 AND Lek_id IN (SELECT Lek_id FROM Log WHERE Apteczka_id=5)) GROUP BY Lek_id, seria, data_waznosci ORDER BY id
    
}

function FormAddLek(){
    $modalAddLek = '
    <div class="modal fade" id="dodajModal">
    <div class="modal-dialog">
    <div class="modal-content">
    <form class="form-group" action="Apteczka.php" method="post">
    <!-- Modal Header -->
    <div class="modal-header">
    <h4 class="modal-title">Informacje o dodawanym leku</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    
    <!-- Modal body -->
    <div class="modal-body">
    
    <label for="ean">Kod kreskowy:</label>
    <input type="number" class="form-control" name="ean" id="ean" placeholder="EAN">
    <label for="ilosc">Ilość:</label>
    <input type="number" class="form-control" name="ilosc" id="ilosc" placeholder="Ilość">
    <label for="seria">Seria:</label>
    <input type="text" class="form-control" name="seria" id="seria" placeholder="Seria">
    <label for="termin">Data ważności:</label>
    <input type="date" class="form-control" name="termin" id="termin" placeholder="Data ważności">
    <label for="cena">Cena:</label>
    <input type="text" class="form-control" name="cena" id="cena" placeholder="Cena [zł]">
    
    </div>
    
    <!-- Modal footer -->
    <div class="modal-footer">
    <button type="submit" class="btn btn-primary" name="wprowadz"'./* data-dismiss="modal" data-toggle="modal" data-target="#zatwierdzModal"*/'>Wprowadź</button>
    </div>
    </form>
    </div>
    </div>
    </div>';
    echo $modalAddLek;
}


function CountQuantity($idApteczki, $idUzytkownika, $seria, $ilosc, $operacja) {
    $conn = DbConnect();
    
    if ($conn -> connect_error){                    //jesli sie nie połączy
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
    
    $qry = "SELECT MAX(id) FROM Log WHERE Apteczka_id=".$idApteczki." AND Uzytkownik_id=".$idUzytkownika." AND seria='".$seria."'";
    $result = $conn->query($qry);
    $tmp = $result->fetch_assoc();
    $id = $tmp['MAX(id)'];
    
    $qry = "SELECT pozostalo FROM Log WHERE id=".$id;
    if($result = $conn->query($qry)){
        $tmp = $result->fetch_assoc();
        $pozostalo0 = $tmp['pozostalo'];
    
        switch ($operacja){
            case 0:
                $pozostalo = $pozostalo0 + $ilosc;
                break;
            default:
                $pozostalo = $pozostalo0 - $ilosc;
        }
        return $pozostalo;
    }
    return $ilosc;
}

function ShowInfo(){
    session_start();
    $idApteczki = $_SESSION['idApteczki'];
    
    $nazwaUzytkownika = $_SESSION['user'];
    $conn = DbConnect();
    
    if ($conn -> connect_error){                    //jesli sie nie połączy
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
    
    $qry = "SELECT id FROM Uzytkownik WHERE nazwa='$nazwaUzytkownika'";
    $result = $conn->query($qry);
    $conn->close();
    $tmp = $result->fetch_assoc();
    $idUzytkownika = $tmp['id'];
    
    $idLeku = $_POST['ean'];
    $operacja = 0;
    $ilosc = $_POST['ilosc'];
    $seria = $_POST['seria'];
    $dataWaznosci = $_POST['termin'];
    $cena = $_POST['cena'];
    $dataWydania = date('Y-m-d H:i:s');
    
    
    $lek = ConvertEAN($idLeku);

    echo '<ul class="list-group">
            <li class="list-group-item">Użytkownik: '.$nazwaUzytkownika.'</li>
            <li class="list-group-item">Nazwa: '.$lek['NazwaHandlowa'].'</li>
            <li class="list-group-item">Podmiot odpowiedzialny: '.$lek['PodmiotOdpowiedzialny'].'</li>
            <li class="list-group-item">Postać: '.$lek['Postac'].'</li>
            <li class="list-group-item">Dawka: '.$lek['Dawka'].'</li>
            <li class="list-group-item">Opakowanie: '.$lek['Opakowanie'].'</li>
            <li class="list-group-item">Ilość: '.$ilosc.'</li>
            <li class="list-group-item">Seria: '.$seria.'</li>
            <li class="list-group-item">Data ważności: '.$dataWaznosci.'</li>
            <li class="list-group-item">Cena: '.$cena.' zł</li>
          </ul>';
    if (!isset($_POST['dodaj'])) {
        $_SESSION['data'] = array($idLeku,$ilosc,$seria,$dataWaznosci,$cena);
    }
}

function AddLek() {
    session_start();
    $idApteczki = $_SESSION['idApteczki'];
    
    $nazwaUzytkownika = $_SESSION['user'];
    $conn = DbConnect();
    
    if ($conn -> connect_error){                    //jesli sie nie połączy
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
    
    $qry = "SELECT id FROM Uzytkownik WHERE nazwa='$nazwaUzytkownika'";
    $result = $conn->query($qry);
    $conn->close();
    $tmp = $result->fetch_assoc();
    $idUzytkownika = $tmp['id'];
    
    $idLeku = $_SESSION['data'][0];
    $operacja = 0;
    $ilosc = $_SESSION['data'][1];
    $seria = $_SESSION['data'][2];
    $dataWaznosci = $_SESSION['data'][3];
    $cena = $_SESSION['data'][4];

    $dataWydania = date('Y-m-d H:i:s');
    $pozostalo = CountQuantity($idApteczki, $idUzytkownika, $seria, $ilosc, $operacja);

    $conn = DbConnect();
    
    if ($conn -> connect_error){                    //jesli sie nie połączy
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
    
    $qry = "INSERT INTO Log (Apteczka_id, Uzytkownik_id, Lek_id, operacja, ilosc, data_wydania, data_waznosci, seria, pozostalo, cena, dcena)
            VALUES ($idApteczki, $idUzytkownika, '$idLeku', $operacja, $ilosc, '$dataWydania', '$dataWaznosci', '$seria', $pozostalo, $cena, $cena)";

    $result = $conn->query($qry);
    
    $conn->close();
}

function ShowUsers($id){
    session_start();
    $conn = DbConnect();
    
    if ($conn -> connect_error){                   
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
    $qry = "SELECT * FROM Uzytkownik WHERE Apteczka_id = $id AND dostep < 8";
    $result = $conn->query($qry);
    $conn->close();
    if ($result->num_rows > 0){                                                //jesli znajdzie w apteczce uzytkownikow
        $i=0;
        while($tmp = $result->fetch_assoc()){
            $array[$i]=$tmp['nazwa'];
            if($tmp['dostep']<2){
                $array2[$i++]="Admin";
            }elseif($tmp['dostep']==2){
                $array2[$i++]="Podstawowe";
            }else{
                $array2[$i++]="Brak";
            }
        }
    }
    $table =
    '<div class="table-responsive">
        <table class="table table-striped">
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Uprawnienia</th>
                <th>Zmień</th>
                <th>Usuń</th>
            </tr>
        </thead>
        <tbody>
        <form action="Uzytkownicy.php" method="post">';
    $k = 0;
    foreach($array as $row){
        $table .= '<tr>';
        $table .=  '<td>'.$array[$k].'</td>
                    <td>'.$array2[$k].'</td>
                    <td><button class="btn btn-sm btn-primary" type="submit" name="Enazwa'.$k.'">Nazwę</button>
                    <button class="btn btn-sm btn-primary" type="submit" name="Ehaslo'.$k.'">Hasło</button>
                    <button class="btn btn-sm btn-primary" type="submit" name="Edostep'.$k.'">Uprawnienia</button>
                    </td><td>
                   <button class="btn btn-sm btn-primary" type="submit" name="Eusun'.$k.'">Usuń</button></td>
                   </tr>'; 
        
        $k++;
    }
    $table .= '</form></tbody></table></div>';
    $_SESSION['btnUser']=$array;
    echo $table;  
}

?>