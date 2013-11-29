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

ALTER TABLE llx_consogazoil_vehicule ADD UNIQUE uk_consogazoil_vehicule_ref (ref);

ALTER TABLE llx_consogazoil_vehicule ADD INDEX idx_consogazoil_vehicule_immat_veh (immat_veh);
