Formularz resetowania haseł dla użytkowników Dovecot.

Stworzyłem ten projekt, ponieważ nie znalazłem podobnego rozwiązania w serwisach takich jak GitHub. Pozwala on użytkownikom poczty e-mail na serwerze z oprogramowaniem Dovecot resetować swoje hasła za pomocą dodatkowego pomocniczego adresu e-mail.

Instalacja:
1. Skopiuj pliki do wybranego katalogu WWW,
2. Utwórz klucz Google reCAPTCHA dla domeny serwera WWW gdzie będzie formularz,
3. Skonfiguruj wszystkie zmienne w pliku config.php,
4. curl -sS https://getcomposer.org/installer | php
5. php composer.phar update

Wymagania:
- backend postgresql
- aktualnie wymaga dovecot z pakietu iRedMail (ale można łatwo użyć innego pola zamiast employeeid z bazydanych),

Potrzebne do konfiguracji:
- konto mailowe do powiadomień,
- klucz google captcha,
- dane dostępowe do bazy danych,

Autor: Jarosław Kłopotek
Licencja: Projekt jest dostępny na licencji GPL v3.0.
