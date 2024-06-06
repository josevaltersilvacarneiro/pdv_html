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

docker rmi josevaltersilvacarneiro/database_pdv:latest 2> /dev/null
docker rm -f pdv_mysql > /dev/null 2>&1
docker build -t josevaltersilvacarneiro/database_pdv:latest .
docker run --name pdv_mysql -e MYSQL_ROOT_PASSWORD="admin" -e MYSQL_DATABASE=database_pdv -p 3306:3306 -d josevaltersilvacarneiro/database_pdv:latest --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci
