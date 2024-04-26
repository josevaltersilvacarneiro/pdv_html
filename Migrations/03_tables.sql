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

CREATE TABLE IF NOT EXISTS `database_pdv`.`suppliers` (
	supplier_id	TINYINT UNSIGNED	NOT NULL	AUTO_INCREMENT,

	name		VARCHAR(32)	NOT NULL,			-- vendor's name
	cnpj		CHAR(15)	NOT NULL	UNIQUE,	-- Brazil
	registration_date DATE	NOT NULL	DEFAULT (CURRENT_DATE),

	CONSTRAINT pk_suppliers
		PRIMARY KEY (supplier_id)
);


CREATE TABLE IF NOT EXISTS `database_pdv`.`loads` (
	load_id			INT UNSIGNED		        NOT NULL	AUTO_INCREMENT,
	supplier		TINYINT UNSIGNED	        NOT NULL,

	billet	VARCHAR(64),	-- IF THERE IS a billet to be paid

	purchase_cost	DECIMAL(10, 2)	NOT NULL,
	due_date		DATE			NOT NULL	DEFAULT (CURRENT_DATE),

	CONSTRAINT pk_loads
		PRIMARY KEY (load_id),
	CONSTRAINT fk2_loads
		FOREIGN KEY (supplier)		            REFERENCES `suppliers`	(supplier_id)
);
