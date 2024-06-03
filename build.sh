#!/bin/bash
#
# Copyright (C) 2023, José Carneiro
# 
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by  
# the Free Software Foundation, either version 3 of the License, or
# any later version.
# 
# This program is distributed in the hope that it will be useful,    
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program. If not, see <http://www.gnu.org/licenses/>.

apt install git -y
cd Src/ && composer update --no-dev
apt purge git -y
cd ../

rm -r .git
rm -r .phpunit.cache
rm -r Migrations/
rm -r Tests/
rm .env
rm .gitignore
rm dev.sh
rm docker-composer.yaml
rm Dockerfile
rm example.env
rm phpunit.xml
rm requirements.txt
rm start_server.sh
rm unit_testing.sh
rm index.html

mkdir /var/cache/pdv/
chown -R www-data:www-data /var/cache/pdv/

REDIRECT="
<Directory /var/www/html/>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
"

echo "${REDIRECT}" >> /etc/apache2/sites-available/000-default.conf

service apache2 restart && rm build.sh