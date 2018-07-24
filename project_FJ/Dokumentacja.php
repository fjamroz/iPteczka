<?php
include("./inc/nagl.php");
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

        </div>
      </div>
    </nav>

    <main role="main">

      <!-- Main jumbotron for a primary marketing message or call to action -->
      <div class="jumbotron">
        <div class="container">
          <h1 class="display-3">System internetowy iPteczka</h1>
          <p>Jest to system pozwalający użytkownikowi przechowywać oraz monitorować leki wprowadzone do apteczki.<br></p>
          <p><button data-toggle="collapse" class="btn btn-lg btn-primary" data-target="#demo1">Więcej</button>
			<div id="demo1" class="collapse">
			<h4>Do głównych funkcjonalności systemu należą:</h4>
			<ul class="list-group list-group-flush">
  			<li class="list-group-item">Dodawanie, pobieranie oraz utylizacja leków</li>
  			<li class="list-group-item">Monitorowanie aktywności danego użytkownika</li>
  			<li class="list-group-item">Generowanie raportów rozchodu i przychodu konkretnego leku w danym przedziale czasowym</li>
  			<li class="list-group-item">Informowanie o stanie leków w apteczce (żółte: leki bliskie przeterminowania do 30dni. Czerwone: leki po terminie)</li>
			<li class="list-group-item">Tworzenie wielu użytkowników w obrębie jednej apteczki</li>			
			<li class="list-group-item">Możliwość założenia wielu apteczek</li>		
			<li class="list-group-item">Porównywanie poszczególnych miesięcy pod względem użytych leków</li>	
			</ul>
			<img src="./img/relacje.PNG" class="center" alt="Baza">
			</div>
		  </p>
        </div>
      </div>

      <div class="container">
            <h2>Przegląd</h2>
            Strona umożliwia przegląd aktualnego stanu apteczki, tj. ilości danych leków w apteczce z serią opakowania oraz datą ważności. W przypadku kiedy data ważności leku jest mniejsza niż 30 dni, dany wiersz jest podświetlany na żółto. Jeśli lek jest już po terminie przydatności do spożycia dany wiersz jest podświetlany na czerwono, a na górnym pasku jest wyświetlana informacja o ilości leków przeterminowanych w apteczce. Leki są wyświetlane w kolejności alfabetycznej. Wyświetlanie leków w apteczce jest realizowane poprzez wyszukanie ostatnich operacji na unikalnych lekach (kod kreskowy oraz numer serii), których własność `pozostalo` nie jest równa zero.<br>
			<ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#dodaj">Dodaj</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#wydaj">Wydaj</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#utylizuj">Utylizuj</a>
              </li>
            </ul>
            <div id="myTabContent" class="tab-content">
              <div class="tab-pane container active" id="dodaj">
                <p>Pozwala na dodawanie leku do apteczki, w pierwszej kolejności należy wpisać nazwę lub kod kreskowy produktu. W przypadku kiedy nazwa jest niejednoznaczna, tzn. w bazie znajduje się powyżej 20 leków o podobnej nazwie, to wtedy należy bardziej sprecyzować o jaki lek chodzi, bądź wpisać kod kreskowy. Po wyborze pożądanego leku z listy rozwijalnej zawierającej informacje o nazwie, ilości leku w opakowaniu oraz formie leku w celu dodania należy wpisać dodatkowo ilość leku, serię, datę ważności oraz cenę. Po wprowadzeniu tych danych wyświetlana jest informacja o cechach leku, który zostanie dodany do apteczki, jeśli wszystko się zgadza należy zatwierdzić operację dodawania leku poprzez kliknięcie przycisku Zatwierdź.</p>
              </div>
              <div class="tab-pane container fade" id="wydaj">
                <p>Umożliwia wydawanie leków z apteczki sobie, bądź użytkownikowi niesamodzielnemu. Po wyborze specyfiku należy wprowadzić ilość pobranych dawek (tabletek, ml, szt.) oraz wybrać czy leki są wydawane dla siebie, czy dla jednego z użytkowników niesamodzielnych. Nie ma możliwości wydania większej ilości leku niż znajduje się aktualnie w apteczce, w przypadku próby zostaje wyświetlony komunikat o braku takiej możliwości.</p>
              </div>
              <div class="tab-pane container fade" id="utylizuj">
                <p>Zapewnia funkcjonalność utylizacji leków w apteczce. Przed operacją utylizacji wyświetlany jest komunikat, w którym należy dokonać potwierdzenia. W tym przypadku własność `pozostalo` jest zerowana.</p>
              </div>
            </div>
            <h2>Użytkownicy</h2>
            Strona umożliwia zarządzanie użytkownikami danej apteczki. Posiada funkcjonalność dodawania nowego użytkownika wyłącznie przez użytkownika ze statusem „Admin” ,aby zapobiec „samowolce”. W celu dodania należy podać nazwę nowego użytkownika, hasło jest opcjonalne oraz wybrać jedno z trzech podanych uprawnień: Admin, podstawowe, brak. Trzecia opcja jest dla użytkowników pasywnych, czyli niezdolnych do samodzielnego korzystania z apteczki. Zmienianie nazwy istniejącym użytkownikom, również przeznaczone jest dla użytkownika o statusie „Admin”. Zmiana hasła tylko swojemu użytkownikowi. Zmianę uprawnień danego użytkownika (tylko „Admin”, operacja chroniona hasłem). W danej apteczce może być wiele osób ze statusem „Admin”. Znajduje się również opcja usuwania użytkownika przez Admina apteczki, aby zapobiec sytuacji usunięcia wszystkich użytkowników, zablokowana jest opcja usuwania samego siebie.<br>  
			<h2>Raport</h2>
			Zakładka przeznaczona do wyświetlania zestawienia przychodu oraz rozchodu specyfików w danym okresie czasowym. Transakcje grupowane są według miesięcy, w których zostały wykonane. Tak jak w zakładce „Historia” u góry znajduje się filtr, w którym ustawiany jest okres raportu. Możliwość sprawdzania raportów od roku 100 n.e.. Dla każdego miesiąca wyświetlane są specyfiki, które były używane oraz podana ilość użytych i dodanych specyfików.        
        	<h2>Koszty</h2>
        	Zakładka, która zawiera zestawienie finansowe dla apteczki za zadany okres. Tradycyjnie należy wpierw ustawić filtr szukania. Użytkownik do dyspozycji ma 3 statusy leku: Dodane, Pobrane oraz Zutylizowane. Po wybraniu okresu i wciśnięciu przycisku „Pokaż” wyświetlany jest raport finansowy. U góry przedstawiony jest okres oraz łączna kwota zgormadzonych specyfików. Zestawienie pogrupowane jest w mniejsze tabele przedstawiające poszczególne medykamenty. W tabelach tych znajduje się kolumna „Ilość” informująca o ilości leku poddanej transakcji, data transakcji oraz cena transakcji.  
        	<h2>Stan</h2>
        	Zakładka w której użytkownik może sprawdzić stan apteczki. Po wybraniu odpowiedniego leku z listy rozwijanej wyświetlane są w tabeli operacje jakie zostały przeprowadzone na danym leku. Suma wartości dla tych transakcji jest sumowana, a następnie sprawdzana ze stanem leku według bazy danych. Jeśli obie liczby są sobie równe, ostatni wiersz podświetlany jest na kolor zielony, w przeciwnym razie przybierze on kolor czerwony. 
        </div>

        <hr>

      </div> <!-- /container -->

    </main>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="../../assets/js/vendor/popper.min.js"></script>
    <script src="../../dist/js/bootstrap.min.js"></script>

<?php 
include("./inc/stopka.php");
?>
