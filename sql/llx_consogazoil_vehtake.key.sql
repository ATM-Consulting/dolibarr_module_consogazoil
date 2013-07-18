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


ALTER TABLE llx_consogazoil_vehtake ADD INDEX idx_consogazoil_vehtake_fk_vehicule (fk_vehicule);
ALTER TABLE llx_consogazoil_vehtake ADD CONSTRAINT fk_consogazoil_vehtake_fk_vehicule  FOREIGN KEY (fk_vehicule)    REFERENCES llx_consogazoil_vehicule (rowid);

ALTER TABLE llx_consogazoil_vehtake ADD INDEX idx_consogazoil_vehtake_fk_station (fk_station);
ALTER TABLE llx_consogazoil_vehtake ADD CONSTRAINT fk_consogazoil_vehtake_fk_station FOREIGN KEY (fk_station)    REFERENCES llx_consogazoil_station (rowid);

ALTER TABLE llx_consogazoil_vehtake ADD INDEX idx_consogazoil_vehtake_fk_driver (fk_driver);
ALTER TABLE llx_consogazoil_vehtake ADD CONSTRAINT fk_consogazoil_vehtake_fk_driver FOREIGN KEY (fk_driver)    REFERENCES llx_consogazoil_driver (rowid);
