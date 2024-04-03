/**
 * This script starts all necessary databases
 * and tables in the web project.
 *
 * Copyright (C) 2023, José V S Carneiro
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */


-- GRANT ALL PRIVILEGES ON *.* TO 'app1'@'%' WITH GRANT OPTION ;

GRANT CREATE TEMPORARY TABLES, SELECT ON *.* TO 'app'@'%' WITH GRANT OPTION ;

GRANT INSERT, UPDATE, DELETE ON database_pdv.positions TO 'app'@'%' WITH GRANT OPTION ;
GRANT INSERT, UPDATE, DELETE ON database_pdv.employees TO 'app'@'%' WITH GRANT OPTION ;
GRANT INSERT, UPDATE, DELETE ON database_pdv.requests TO 'app'@'%' WITH GRANT OPTION ;
GRANT INSERT, UPDATE, DELETE ON database_pdv.sessions TO 'app'@'%' WITH GRANT OPTION ;

GRANT EXECUTE ON database_pdv.* TO 'app'@'%' WITH GRANT OPTION ;

-- DROP USER 'app'@'%' ;