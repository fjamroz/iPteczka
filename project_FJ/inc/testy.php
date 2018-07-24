<?php 
function FormAddLekTest(){
    $modalAddLek = '
    <div class="modal fade" id="dodajModal">
    <div class="modal-dialog">
    <div class="modal-content">
    <form class="form-group" action="Apteczka.php" method="post">
    <!-- Modal Header -->
    <div class="modal-header">
    <h4 class="modal-title">Informacje o dodawanym leku</h4>
    </div>
    
    <!-- Modal body -->
    <div class="modal-body">
    
    <label for="ean">Kod kreskowy:</label>
    <input type="text" class="form-control" name="ean" id="ean" placeholder="EAN">

    
    </div>
    
    <!-- Modal footer -->
    <div class="modal-footer">
	<button type="submit" class="btn btn-primary" name="anuluj"'./* data-dismiss="modal" data-toggle="modal" data-target="#zatwierdzModal"*/'>Anuluj</button>
    <button type="submit" class="btn btn-primary" name="wprowadz0"'./* data-dismiss="modal" data-toggle="modal" data-target="#zatwierdzModal"*/'>Wprowadz0</button>
    </div>
    </form>
    </div>
    </div>
    </div>';
    echo $modalAddLek;
    $_SESSION['ean'] = $_POST['ean'];
}

function FormAddLekTest2(){
    $modalAddLek = '
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
        
    <label for="ean">Kod dupa:</label>
    <input type="number" class="form-control" name="ean" id="ean" placeholder="EAN">
        
        
    </div>
        
    <!-- Modal footer -->
    <div class="modal-footer">
	<button type="submit" class="btn btn-primary" name="anuluj"'./* data-dismiss="modal" data-toggle="modal" data-target="#zatwierdzModal"*/'>Anuluj</button>
    <button type="submit" class="btn btn-primary" name="wprowadz"'./* data-dismiss="modal" data-toggle="modal" data-target="#zatwierdzModal"*/'>Wprowadź</button>
    </div>
    </form>
    </div>
    </div>
    </div>';
    echo $modalAddLek;
}

function ShowInfoTest(){
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
function ShowInfoTest2(){
    session_start();
    $idApteczki = $_SESSION['idApteczki'];
    
    $nazwaUzytkownika = $_SESSION['user'];
    $conn = DbConnect();
    
    if ($conn -> connect_error){                    //jesli sie nie połączy
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
    
    $qry = "SELECT id FROM Uzytkownik WHERE nazwa='$nazwaUzytkownika'";
    $result = $conn->query($qry);
	$qry2 = 'SELECT NazwaHandlowa FROM Lek WHERE KodKreskowy='.$_POST["LekiTest"].'';
	$result2 = $conn->query($qry2);
    $conn->close();
    $tmp = $result->fetch_assoc();
	$tmp2 = $result2->fetch_assoc();
    $idUzytkownika = $tmp['id'];
    

    $idLeku = $_POST['LekiTest'];
	$nazwaLeku = $tmp2['NazwaHandlowa'];
    $operacja = 0;
    $ilosc = $_POST['ilosc'];
    $seria = $_POST['seria'];
    $dataWaznosci = $_POST['termin'];
    $cena = $_POST['cena'];
    $dataWydania = date('Y-m-d H:i:s');
    
    
    $lek = ConvertEAN($idLeku);
    
    echo '<ul class="list-group">
            <li class="list-group-item">Użytkownik: <b>'.$nazwaUzytkownika.'</b></li>
            <li class="list-group-item">Nazwa: <b>'.$nazwaLeku.'</b></li>
            <li class="list-group-item">Podmiot odpowiedzialny: <b>'.$lek['PodmiotOdpowiedzialny'].'</b></li>
            <li class="list-group-item">Postać: <b>'.$lek['Postac'].'</b></li>
            <li class="list-group-item">Dawka: <b>'.$lek['Dawka'].'</b></li>
            <li class="list-group-item">Opakowanie: <b>'.$lek['Opakowanie'].'</b></li>
            <li class="list-group-item">Ilość: <b>'.$ilosc.'</b></li>
            <li class="list-group-item">Seria: <b>'.$seria.'</b></li>
            <li class="list-group-item">Data ważności: <b>'.$dataWaznosci.'</b></li>
            <li class="list-group-item">Cena: <b>'.$cena.' zł</b></li>
          </ul>';
    if (!isset($_POST['dodaj'])) {
        $_SESSION['data'] = array($idLeku,$ilosc,$seria,$dataWaznosci,$cena);
    }
}
?>