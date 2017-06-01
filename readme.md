# DocMan Dokumentenverwaltung

Online-Verwaltung von PDF-Dokumenten mit Metadaten und Suche. Unterstützt Tagging und öffentliches Teilen von
Dokumenten. Import über die Weboberfläche, per E-Mail-Anhang oder aus einem Ordner im Dateisystem.


## Deployment

Datenbank:

```
CREATE DATABASE docman;
CREATE USER 'docman'@'localhost' IDENTIFIED BY 'changeme1';
GRANT ALL PRIVILEGES ON docman.* TO 'docman'@'localhost';
FLUSH PRIVILEGES
```

Webserver (nginx, PHP-FPM):

```
server {
  listen 443 ssl;
  listen [::]:443 ssl;
  include snippets/ssl.conf;

  server_name docs.your-server.de;

  root /path/to/repo/public;
  index index.php index.html;

  location / {
    try_files $uri $uri/ /index.php?$query_string;
    auth_basic "Dokumente";
    auth_basic_user_file /path/to/your/.htpasswd;
  }

  location ~ /shared|/style {
    try_files $uri $uri/ /index.php?$query_string;
    auth_basic off;
  }

  location ~ \.php$ {
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    try_files $fastcgi_cript_name =404;
    set $path_info $fastcgi_path_info;
    fastcgi_param PATH_INFO $path_info;
    fastcgi_index index.php;
    include fastcgi.conf;
  }
}
```

Abhängigkeiten:

```
apt-get install imagemagick pdftk ghostscript php5-mcrypt
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
composer install
```

Konfiguration:

```
cp .env.example .env
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan migrate
chown -R root:www-data .
chmod -R g+w storage
htpasswd -c .htpasswd mustermann
```

In der php.ini die Funktionen `exec` und `shell_exec` erlauben.

Tests:

```
vendor/bin/phpunit
```


## Tag-Liste benutzen

Tag-Liste links auf der Startseite füllen / aktualisieren:

```
php artisan docman:updatetags
```

Alternativ oben rechts auf den entsprechenden Button klicken.


## Dokumente mit dritten Personen teilen

Auf der Detailseite eines Dokuments wird unter dem Speichern-Button ein Share-Link angezeigt. Diesen kann man
weitergeben, um lesenden Zugriff ohne Eingabe eines Passwortes auf genau dieses Dokument zu geben. Die originale
PDF-Datei kann mit diesem Link nicht heruntergeladen werden.


## Import aus dem Dateisystem

Docman unterstützt den Bulk-Import von PDF-Dateien aus einem lokalen Verzeichnis auf dem Server. Dazu muss das
Verzeichnis in `.env` konfiguriert sein.

Anschließend können PDF-Dateien z.B. mit `scp` auf den Server hochgeladen werden. Für den Import wird anschließend
die `docman:importdirectory`-Action verwendet. Es existiert jedoch ein Wrapper-Skript. Als root ausführen:

```
script/docman-import-directory.sh
```

Es ist sicherlich nützlich, sich für das Skript einen Alias oder Link zu setzen, zum Beispiel:

```
sudo ln -s /path/to/repo/script/docman-import-directory.sh /usr/local/bin/docman-import
docman-import
```


## E-Mail-Empfang einrichten

Docman unterstützt den Import von Dokumenten durch das Empfangen von E-Mails. Dafür auf dem Mailserver folgendes tun:

```
$ cat /etc/aliases
docs: "|/usr/local/bin/docman-mail-receiver.pl"

$ sudo cp script/docman-mail-receiver.pl /usr/local/bin
```

Die folgenden Perl-Module müssen installiert sein:

 - Email::MIME
 - LWP::UserAgent
 - HTTP::Request
 - MIME::Base64

In der Perl-Datei müssen die URL sowie HTTP-Auth-Zugangsdaten für den Docman-Server eingetragen werden. Am besten
ein eigenes Set Credentials verwenden. Anschließend kann man PDF-Dokumente als Anhang an `docs@your-mail-server.de`
schicken.

Die Dokumente werden ohne Datum importiert, man kann über das Tool "Import" rechts oben für alle neuen Dokumente
Datum, Titel und Tags festlegen.


## Lizenz

Copyright (c) 2015-2017 Max Weller, Johannes Lauinger

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.


### Laravel PHP Framework

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
