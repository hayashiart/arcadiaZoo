-- data.sql
-- Utilisateurs
INSERT INTO user (email, password, roles, created_at) 
VALUES ('jose@arcadia.com', '$2y$13$aSpUdgtZoUXfH02uJ2Nfge3t1Js2U6UHc5akFK/wW..FPlRnuefKO', '["ROLE_ADMIN"]', NOW());

INSERT INTO user (email, password, roles, created_at) 
VALUES ('vet@arcadia.com', '$2y$13$DHttDJ1InPMp6qob73BtEeuPZNUKRNujCuR3DKqmiiRwaxsKjMb8y', '["ROLE_VET"]', NOW());

INSERT INTO user (email, password, roles, created_at) 
VALUES ('employee@arcadia.com', '$2y$13$uM4s4u66Hs7VTQNYw4IWT.xCAfYuCFMRgd9FJTfqKcUmb5y6v97FS', '["ROLE_EMPLOYEE"]', NOW());

INSERT INTO habitat (id, name, description, images) 
VALUES (1, 'Savane', 'Vaste plaine africaine avec des herbes hautes', '["/images/savane.jpeg"]'),
       (2, 'Forêt', 'Forêt tropicale dense et verdoyante', '["/images/forest.jpg"]'),
       (3, 'Aquarium', 'Ecosystème marin avec récifs coralliens', '["/images/aquarium.jpeg"]'),
       (4, 'Désert', 'Paysage aride avec dunes de sable', '["/images/desert.jpeg"]');

INSERT INTO animal (id, name, breed, habitat_id, images) 
VALUES (1, 'Kibo', 'Girafe', 1, '["/images/giraffe.jpg"]'),
       (2, 'Milo', 'Singe', 2, '["/images/monkey.jpg"]'),
       (3, 'Shere Khan', 'Tigre', 2, '["/images/tiger.jpg"]'),
       (4, 'Dumbo', 'Éléphant', 1, '["/images/elephants.jpg"]');
-- Services
INSERT INTO service (name, description) 
VALUES ('Restauration', 'Restaurants avec produits locaux');

-- Avis
INSERT INTO review (pseudo, comment, is_valid, created_at) 
VALUES ('Visiteur1', 'Super zoo !', FALSE, NOW());

-- Rapports vétérinaires
INSERT INTO report (animal_id, status, food, food_quantity, visit_date, details, vet_id) 
VALUES (1, 'Bon', 'Herbes', 1000, NOW(), 'Girafe en bonne santé', 2);

INSERT INTO report (animal_id, status, food, food_quantity, visit_date, details, vet_id) 
VALUES (2, 'Bon', 'Fruits', 500, NOW(), 'Singe actif', 2);

-- Nourriture
INSERT INTO feeding (animal_id, food, quantity, date, employee_id) 
VALUES (1, 'Herbes', 800, NOW(), 3);

INSERT INTO feeding (animal_id, food, quantity, date, employee_id) 
VALUES (2, 'Fruits', 300, NOW(), 3);

-- Contacts
INSERT INTO contact (email, title, description, created_at) 
VALUES ('visiteur@exemple.com', 'Question', 'Quels sont les horaires ?', NOW());

-- Commentaires sur les habitats
INSERT INTO habitat_comment (habitat_id, comment, created_at, vet_id) 
VALUES (1, 'Savane propre et bien entretenue', NOW(), 2);

INSERT INTO schedule (day, opening_time, closing_time, is_closed) VALUES
('Lundi', '09:00:00', '18:00:00', FALSE),
('Mardi', '09:00:00', '18:00:00', FALSE),
('Mercredi', '09:00:00', '18:00:00', FALSE),
('Jeudi', '09:00:00', '18:00:00', FALSE),
('Vendredi', '09:00:00', '18:00:00', FALSE),
('Samedi', '10:00:00', '19:00:00', FALSE),
('Dimanche', NULL, NULL, TRUE);