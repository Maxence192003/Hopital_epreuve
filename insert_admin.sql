USE app;
DELETE FROM profil WHERE id_utilisateur=1;
DELETE FROM utilisateur WHERE id_utilisateur=1;
DELETE FROM login WHERE id_login=1;

INSERT INTO login (id_login, mail, password) VALUES (1, 'admin@hopital.fr', '$2y$12$hFevCDUp3ukTLhk23E1SE.ky20uqYet3XcOaUxb0mZIlZ2hvWcH4C');
INSERT INTO utilisateur (id_utilisateur, nom, prenom, ville_res, cp, id_login) VALUES (1, 'Admin', 'Admin', 'Limoges', '87000', 1);
INSERT INTO profil (id_profil, role, id_utilisateur) VALUES (1, 'ROLE_ADMIN', 1);

SELECT id_login, mail, password, LENGTH(password) as pwd_length FROM login WHERE mail='admin@hopital.fr';
