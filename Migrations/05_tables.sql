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

CREATE TABLE IF NOT EXISTS `database_pdv`.`carts` (
	cart_id		SMALLINT UNSIGNED	NOT NULL	AUTO_INCREMENT, -- 65 * 10^3

	`order`		INT UNSIGNED		NOT NULL,			-- order in processing

	CONSTRAINT pk_carts
		PRIMARY KEY (cart_id),
	CONSTRAINT fk1_carts
		FOREIGN KEY (`order`)	REFERENCES `orders`	(order_id)
);
