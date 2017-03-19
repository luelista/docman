# DocMan Dokumentenverwaltung

## Deployment

Repository:

```
git clone ...
```

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

  location ~ \.php$ {
    fastcgi_pass unix:/var/run/php5-fpm.sock;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    try_files $fastcgi_script_name =404;
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
apt-get install nodejs nodejs-legacy npm
npm install -g gulp
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

```
composer install
composer dumpautoload -o
gulp
```

Konfiguration:

```
cp .env.example .env
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


## Benutzung

Tag-Liste links auf der Startseite füllen / aktualisieren:

```
php artisan docman:updatetags
```


## Lizenz


Copyright (c) 2015 luelista

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
