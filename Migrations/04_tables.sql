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


CREATE TABLE IF NOT EXISTS `database_pdv`.`types_of_product` (
	type_of_product_id  SMALLINT UNSIGNED	NOT NULL	AUTO_INCREMENT,

	title		VARCHAR(32)		NOT NULL,
	price		DECIMAL(10, 2)	NOT NULL,

	CONSTRAINT pk_types_of_product
		PRIMARY KEY (type_of_product_id)
);


CREATE TABLE IF NOT EXISTS `database_pdv`.`packages` (
	package_id		INT UNSIGNED		NOT NULL	AUTO_INCREMENT,
	type_of_product	SMALLINT UNSIGNED   NOT NULL,

	bar_code		VARCHAR(13)		    NOT NULL	UNIQUE,

	number_of_items_purchased SMALLINT UNSIGNED	NOT NULL	DEFAULT 1,	-- quant. of prod. in pack
	number_of_items_sold	SMALLINT UNSIGNED	NOT NULL	DEFAULT 0,	-- quant. of prod. sold
	validity		        DATE			    NOT NULL,

	CONSTRAINT pk_packages
		PRIMARY KEY (package_id),
	CONSTRAINT fk2_packages
		FOREIGN KEY (type_of_product)	REFERENCES `types_of_product`	(type_of_product_id)
);


CREATE TABLE IF NOT EXISTS `database_pdv`.`orders` (
	order_id	INT UNSIGNED	NOT NULL	AUTO_INCREMENT,	-- 4 billion of orders

	order_date	DATETIME		NOT NULL	DEFAULT	CURRENT_TIMESTAMP,

	CONSTRAINT pk_orders
		PRIMARY KEY (order_id)
);


CREATE TABLE IF NOT EXISTS `database_pdv`.`order_items` (
	`order`	        INT UNSIGNED NOT NULL,
	package         INT UNSIGNED NOT NULL,

	amount	SMALLINT UNSIGNED    NOT NULL   DEFAULT 1,
	price	DECIMAL(10, 2)	     NOT NULL,

	CONSTRAINT pk_order_items
		PRIMARY KEY (`order`, package),
	CONSTRAINT fk1_orders
		FOREIGN KEY (`order`)	REFERENCES `orders`	(order_id),
	CONSTRAINT fk2_packages_items
		FOREIGN KEY (package)	REFERENCES `packages`	(package_id)
);
