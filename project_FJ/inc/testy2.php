<?php
	function FormAddLekTest2(){
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
    
    </div>
    
    <!-- Modal footer -->
    <div class="modal-footer">
    <button type="submit" class="btn btn-primary" name="wprowadz"'./* data-dismiss="modal" data-toggle="modal" data-target="#zatwierdzModal"*/'>Wyszukaj</button>
    </div>
    </form>
    </div>
    </div>
    </div>';
    echo $modalAddLek;
}

function FormAddLek2Test2(){
	$conn = DbConnect();
	
	if ($conn -> connect_error){                    //jesli sie nie połączy
        die("Brak połączenia:" . $conn->connect_error . "<br>");
    }
	
	$qry = 'SELECT';
	$result = $conn->query();
	
	$ean = $_POST['ean'];
	$modalAddLek = '
    <div class="modal fade" id="dodaj2Modal">
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
    
    <label for="ean2">Kod kreskowy:</label>
	';
	$modalAddLek .= '<select class="form-control" name="User">';
	$modalAddLek .= '<option value="0" selected>Wybierz...</option>';
	foreach($_SESSION['users'] as $name){
	    $modalAddLek .= '<option value="'.$name.'">'.$name.'</option>';
	}
	$modalAddLek .= '</select>';
	
	
    $modalAddLek .= '<input type="number" class="form-control" name="ean2" id="ean2" placeholder="EAN">
    
    </div>
    
    <!-- Modal footer -->
    <div class="modal-footer">
	<button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
    <button type="submit" class="btn btn-primary" name="wprowadz"'./* data-dismiss="modal" data-toggle="modal" data-target="#zatwierdzModal"*/'>Wyszukaj</button>
    </div>
    </form>
    </div>
    </div>
    </div>';
}

function FormSubtractLek($leki, $indeks){
	$nazwaUzytkownika = $_SESSION['user'];
	$modalSubtractLek = '
    <div class="modal fade" id="wydajModal">
    <div class="modal-dialog">
    <div class="modal-content">
    <form class="form-group" action="Apteczka.php" method="post">
    <!-- Modal Header -->
    <div class="modal-header">
    <h4 class="modal-title">Informacje o wydawanym leku</h4>
    </div>
    
    <!-- Modal body -->
    <div class="modal-body">';
	$lek = $leki[$indeks];
	'<ul class="list-group">
            <li class="list-group-item">Użytkownik: '.$nazwaUzytkownika.'</li>
            <li class="list-group-item">Nazwa: '.$lek['NazwaHandlowa'].'</li>
            <li class="list-group-item">Podmiot odpowiedzialny: '.$lek['PodmiotOdpowiedzialny'].'</li>
            <li class="list-group-item">Postać: '.$lek['Postac'].'</li>
			<li class="list-group-item">Ilość w apteczce: '.$lek['pozostalo'].'</li>
	</ul>';
    $modalSubtractLek .= '<input type="number" class="form-control" name="ilosc" placeholder="Ilość do wydania">
    </div>
    
    <!-- Modal footer -->
    <div class="modal-footer">
	<button type="submit" class="btn btn-primary" name="anuluj">Anuluj</button>
    <button type="submit" class="btn btn-primary" name="zatwierdzWydaj"'./* data-dismiss="modal" data-toggle="modal" data-target="#zatwierdzModal"*/'>Zatwierdź</button>
    </div>
    </form>
    </div>
    </div>
    </div>';
}
?>