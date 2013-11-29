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

CREATE TABLE IF NOT EXISTS llx_consogazoil_tmp (
  	rowid 		integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  
	contrat varchar(255),
	carte_vehicule  varchar(255),
	carte_driver varchar(255),
	code_produit varchar(255),
	produit varchar(255),
	volume_gaz varchar(255),
	dt_take varchar(255),
	hour_take varchar(255),
	country varchar(255),
	id_station varchar(255),
	label_station varchar(255),
	dt_invoice varchar(255),
	num_invoice varchar(255),
	tva_tx_invoice varchar(255),
	currency_take varchar(255),
	amount_ht_take varchar(255),
	amount_tva_take varchar(255),
	amount_ttc_take varchar(255),
	currency_payment varchar(255),
	amount_ht_payment varchar(255),
	amount_tva_payment varchar(255),
	amount_ttc_payment varchar(255),
	km_take varchar(255),
	immat_veh varchar(255),
	gear_type varchar(255),

	veh_conflit smallint DEFAULT NULL,
	veh_conflit_action varchar(30) DEFAULT NULL,
	station_conflit smallint DEFAULT NULL,
	station_conflit_action varchar(30) DEFAULT NULL,
	driver_conflit smallint DEFAULT NULL,
	driver_conflit_action varchar(30) DEFAULT NULL,

	pb_quality text DEFAULT NULL

) ENGINE=InnoDB;
