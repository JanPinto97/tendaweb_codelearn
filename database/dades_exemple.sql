-- database/dades_exemple.sql
-- Dades de prova per a desenvolupament (no usar a producció)

INSERT INTO categories (nom) VALUES ('Roba'), ('Calçat'), ('Accessoris');

INSERT INTO productes (nom, descripcio, preu, estoc, categoria_id) VALUES
('Samarreta codelearn', 'Samarreta de cotó 100%. Augmenta en un 15% la teva capacitat de programació', 9.99, 50, 1),
('Pantalons texans', 'Texans slim fit', 29.99, 20, 1),
('Sabates esportives', 'Sabates per córrer', 49.99, 0, 2),
('Cinturó de cuir', 'Cinturó negre de cuir genuí', 14.99, 15, 3);