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

FROM ubuntu/apache2
LABEL net.josevaltersilvacarneiro.author="José Carneiro <git@josevaltersilvacarneiro.net>"

WORKDIR /var/www/html

RUN apt update && apt upgrade -y
RUN apt install php8.3 -y
RUN apt install php8.3-mysql php8.3-curl php8.3-gd php8.3-intl php8.3-xsl php8.3-mbstring -y
RUN apt autoremove -y
RUN a2enmod rewrite && service apache2 restart

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY . .
