<?php
include("./inc/nagl.php");


echo 'Użytkownicy 
Strona umożliwia zarządzanie użytkownikami danej apteczki. Posiada funkcjonalność dodawania nowego użytkownika wyłącznie przez użytkownika ze statusem „Admin” ,aby zapobiec „samowolce”. W celu dodania należy podać nazwę nowego użytkownika, hasło jest opcjonalne oraz wybrać jedno z trzech podanych uprawnień: Admin, podstawowe, brak. Trzecia opcja jest dla użytkowników pasywnych, czyli niezdolnych do samodzielnego korzystania z apteczki. Zmienianie nazwy istniejącym użytkownikom, również przeznaczone jest dla użytkownika o statusie „Admin”. Zmiana hasła tylko swojemu użytkownikowi. Zmianę uprawnień danego użytkownika (tylko „Admin”, operacja chroniona hasłem). W danej apteczce może być wiele osób ze statusem „Admin”. Znajduje się również opcja usuwania użytkownika przez Admina apteczki, aby zapobiec sytuacji usunięcia wszystkich użytkowników, zablokowana jest opcja usuwania samego siebie.   
Historia
U góry strony znajduje się filtr, w którym internauta może wybrać danego użytkownika oraz okres dla którego chciałby zobaczyć historię pobranych leków. W momencie błędnie wybranego okresu aplikacja poinformuje użytkownika o zaistniałym problemie, w razie braku aktywności w danym okresie również zostanie wyświetlony komunikat. Historia wyświetlana jest w sekcjach oddzielających poszczególne  leki, aby łatwiej było odnaleźć konkretny specyfik, ponadto leki sortowane są alfabetycznie. Dla każdego leku zamieszczona jest informacja o ilości pobranego leku oraz data godzinna kiedy lek został pobrany. 
Raport
Zakładka przeznaczona do wyświetlania zestawienia przychodu oraz rozchodu specyfików w danym okresie czasowym. Transakcje grupowane są według miesięcy, w których zostały wykonane. Tak jak w zakładce „Historia” u góry znajduje się filtr, w którym ustawiany jest okres raportu. Możliwość sprawdzania raportów od roku 100 n.e.. Dla każdego miesiąca wyświetlane są specyfiki, które były używane oraz podana ilość użytych i dodanych specyfików.
Koszty
Zakładka, która zawiera zestawienie finansowe dla apteczki za zadany okres. Tradycyjnie należy wpierw ustawić filtr szukania. Użytkownik do dyspozycji ma 3 statusy leku: Dodane, Pobrane oraz Zutylizowane. Po wybraniu okresu i wciśnięciu przycisku „Pokaż” wyświetlany jest raport finansowy. U góry przedstawiony jest okres oraz łączna kwota zgormadzonych specyfików. Zestawienie pogrupowane jest w mniejsze tabele przedstawiające poszczególne medykamenty. W tabelach tych znajduje się kolumna „Ilość” informująca o ilości leku poddanej transakcji, data transakcji oraz cena transakcji.  
Stan
Zakładka w której użytkownik może sprawdzić stan apteczki. Po wybraniu odpowiedniego leku z listy rozwijanej wyświetlane są w tabeli operacje jakie zostały przeprowadzone na danym leku. Suma wartości dla tych transakcji jest sumowana, a następnie sprawdzana ze sanem leku według bazy danych. Jeśli obie liczby są sobie równe, ostatni wiersz podświetlany jest na kolor zielony, w przeciwnym razie przybierze on kolor czerwony. 
';
/*echo '<h3>System internetowy iPteczka.<br>
Jest to system pozwalający użytkownikowi przechowywać oraz monitorować leki wprowadzone do apteczki.<br>
Do głównych funkcjonalności systemu należą:</h3><br>
<h4>- dodawanie, pobieranie oraz utylizacja leków,<br>
- monitorowanie aktywności danego użytkownika,<br>
- generowanie raportów rozchodu i przychodu konkretnego leku w danym przedziale czasowym,<br>
- informowanie o stanie leków w apteczce (żółte: leki bliskie przeterminowania do 30dni. Czerwone: leki po terminie),<br>
- tworzenie wielu użytkowników w obrębie jednej apteczki,<br>
- możliwość założenia wielu apteczek,<br>
- porównywanie poszczególnych miesięcy pod względem użytych leków.</h4>';*/
include("./inc/stopka.php");
?>