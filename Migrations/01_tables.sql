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


USE database_pdv;


CREATE TABLE IF NOT EXISTS `database_pdv`.`positions` (
	position_id	TINYINT	UNSIGNED	NOT NULL	AUTO_INCREMENT,	-- 255 positions

	name	VARCHAR(32)		NOT NULL,		-- position's name
	salary	DECIMAL(10, 2)	NOT NULL,		-- employee's salary
	payday	VARCHAR(2)		CHECK ( payday = '10' OR payday = '15' ),

	CONSTRAINT pk_positions
		PRIMARY KEY (position_id)
);


CREATE TABLE IF NOT EXISTS `database_pdv`.`employees` (
	employee_id	SMALLINT UNSIGNED	NOT NULL	AUTO_INCREMENT,	-- 65,000 employees
	position	TINYINT	UNSIGNED	NOT NULL	DEFAULT 1,	-- employee's position

	fullname	VARCHAR(80)		NOT NULL,
	email		VARCHAR(255)	NOT NULL	UNIQUE,
	hash_code	VARCHAR(255)	CHECK ( CHAR_LENGTH(hash_code) > 40 ),
	active		BOOLEAN			NOT NULL	DEFAULT TRUE,

	CONSTRAINT pk_employees
		PRIMARY KEY (employee_id),
	CONSTRAINT fk1_employees
		FOREIGN KEY (position)	REFERENCES `positions`	(position_id)
);


CREATE TABLE IF NOT EXISTS `database_pdv`.`requests` (
	request_id	BIGINT UNSIGNED	NOT NULL	AUTO_INCREMENT, -- 18 * 10¹⁸ requests

	ip		    VARCHAR(39)	CHECK ( ip REGEXP '^(\\d{1,3}\\.){3}\\d{1,3}$|^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$' ),
	port		VARCHAR(5)	CHECK ( port REGEXP '^[0-9]{1,5}$' ),
	access_time	DATETIME	NOT NULL	DEFAULT CURRENT_TIMESTAMP,

	CONSTRAINT pk_requests
		PRIMARY KEY (request_id)
);


CREATE TABLE IF NOT EXISTS `database_pdv`.`sessions` (
	session_id	    VARCHAR(64)		    CHECK ( session_id REGEXP '^[0-9a-fA-F]{64}' ),	-- SHA256
	employee	    SMALLINT UNSIGNED,			    -- is NULL when the employee is no logged in
	last_request	BIGINT UNSIGNED		NOT NULL,	-- last access

	CONSTRAINT pk_sessions
		PRIMARY KEY (session_id),
	CONSTRAINT fk1_sessions
		FOREIGN KEY (employee)		REFERENCES `employees` (employee_id),
	CONSTRAINT fk2_sessions
		FOREIGN KEY (last_request)	REFERENCES `requests`	(request_id)
);
