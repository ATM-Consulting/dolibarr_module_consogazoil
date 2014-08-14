ALTER TABLE llx_consogazoil_vehtake ADD produit varchar(50) AFTER volume;
ALTER TABLE llx_consogazoil_vehtake ADD code_produit varchar(50) AFTER produit;
ALTER TABLE llx_consogazoil_vehtake ADD km_drive integer NULL AFTER km_controle;
ALTER TABLE llx_consogazoil_vehtake ADD amount real NULL AFTER conso_calc;
ALTER TABLE llx_consogazoil_driver ADD activ integer  NOT NULL DEFAULT 1 AFTER name;
ALTER TABLE llx_consogazoil_vehicule ADD activ integer  NOT NULL DEFAULT 1 AFTER fk_user_modif;

		,
