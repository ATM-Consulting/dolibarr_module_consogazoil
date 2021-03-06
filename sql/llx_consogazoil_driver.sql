-- Consomation Gazoil 
-- Copyright (C) 2013 florian Henry <florian.henry@open-concept.pro>
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.

CREATE TABLE IF NOT EXISTS llx_consogazoil_driver (
  rowid 		integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity 		integer NOT NULL DEFAULT 1,
  ref			varchar(30) NULL,
  name			varchar(100) NULL,
  activ			integer  NOT NULL DEFAULT 1,
  datec			datetime NOT NULL,
  tms 			timestamp NOT NULL,
  fk_user_creat 	integer NOT NULL,
  fk_user_modif 	integer NOT NULL,
  import_key		varchar(14)
) ENGINE=InnoDB;
