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

GRANT INSERT ON database_pdv.suppliers TO 'app'@'%' WITH GRANT OPTION ;
GRANT INSERT ON database_pdv.loads TO 'app'@'%' WITH GRANT OPTION ;
GRANT INSERT, UPDATE ON database_pdv.types_of_product TO 'app'@'%' WITH GRANT OPTION ;
GRANT INSERT, UPDATE ON database_pdv.packages TO 'app'@'%' WITH GRANT OPTION ;
GRANT INSERT ON database_pdv.`orders` TO 'app'@'%' WITH GRANT OPTION ;
GRANT INSERT, UPDATE ON database_pdv.`order_items` TO 'app'@'%' WITH GRANT OPTION ;

INSERT INTO `database_pdv`.`positions` (name, salary, payday)
VALUES ("Operador de Caixa", 620, '10');

-- uefs@josevaltersilvacarneiro.net
-- TEST123456abcd

INSERT INTO `database_pdv`.`employees` (position, fullname, email, hash_code)
VALUES (1, "José Carneiro", "uefs@josevaltersilvacarneiro.net", "$argon2id$v=19$m=65536,t=4,p=1$bjRSdVVoL2VFNTZjL0dzdA$Q1rlcf8vsIriv+2voQHR2APyTer/Wst53KLfOyNiTT8");
