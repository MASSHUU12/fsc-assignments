# FSC Assignments

## Zadanie 1. Parsowanie/szukanie danych (wo_for_parse.html):

Na podstawie dostarczonego pliku (HTML) przygotuj parser.

Dane które potrzebujemy wyciągnąć do pliku CSV:

- Tracking Number
- PO Number
- Data `Scheduled` w formacie daty i godziny (Y-m-d H:i)
- Customer
- Trade
- NTE (jako liczba float - bez formatowania)
- Store ID
- Adres z rozbiciem na:
  - ulica
  - miasto,
  - stan (2 litery)
  - kod pocztowy
- Telefon (jako liczba float - bez formatowania)