USE cafe_run;

-- 1) Disable FK checks for this session
SET FOREIGN_KEY_CHECKS = 0;

-- 2) Truncate every table in dependency order
TRUNCATE TABLE likes;               -- child of revues
TRUNCATE TABLE photos_revue;        -- child of revues
TRUNCATE TABLE commentaires;        -- child of revues
TRUNCATE TABLE revues_categories;   -- child of revues
TRUNCATE TABLE followers;           -- child of utilisateurs

TRUNCATE TABLE revues;              -- now safe (since all children are truncated)
TRUNCATE TABLE utilisateurs;        -- safe after followers
TRUNCATE TABLE categories;          -- safe after revues_categories
TRUNCATE TABLE cafes;               -- no direct children left

-- 3) Re-enable FK checks
SET FOREIGN_KEY_CHECKS = 1;


-- 4) UTILISATEURS
INSERT INTO utilisateurs (nom, email, mot_de_passe, role, token) VALUES
  ('Alice Dupont','alice@example.com','$2y$10$KIXD2zvIwUqJteQWnB5Uu','utilisateur',NULL),
  ('Bob Martin','bob@example.com','$2y$10$abc123DEFghIjKlmNopQr','utilisateur',NULL),
  ('Carol Durand','carol@example.com','$2y$10$QrStUvWxYzAbCdEfGhIjK','admin',NULL),
  ('David Lefevre','david@example.com','$2y$10$1234567890abcdefgHIJ','utilisateur',NULL),
  ('Jean Talon','jean@example.com','$2y$10$Cw5xlhPcAxqDlvKt/cdzQ.sfpk6ZqmLaZUMaW6h0RLrxqsNwHHxvC','utilisateur',NULL);


-- 5) CAFES
INSERT INTO cafes (nom, adresse, categories, telephone, email, site_web) VALUES
  ('Café Lumière','123 Rue de la Paix','Espresso,Latte','+123-45-6789','contact@lumieré.fr','https://cafelum.com'),
  ('Café Parisien','45 Av. des Champs','Cappuccino,Latte','+1987-654-3232','bonjour@parisien.fr','https://pariscafe.fr'),
  ('Café du Coin','78 Rue de la République','Espresso,Mocha','+123-45-6789','caf@ducoin.ca','https://cafeducoin.ca'),
  ('Café Cozy','12 Rue des Fleurs','Cappuccino,Mocha','+987-65-4321','cozycafe@cafe.ca','https://cozycafe.ca'),
  ('Café Horizon',      '21 Rue Montagne', 'Espresso,Cappuccino', '+33-1-2345-6789', 'contact@horizon.fr', 'https://cafehorizon.fr'),
  ('Café Lune',         '88 Avenue Lumière', 'Latte,Mocha', '+33-6-9876-5432', 'info@lune.fr', 'https://cafelune.fr'),
  ('Café Zen',          '5 Rue du Silence', 'Matcha,Tea', '+33-7-7654-3210', 'zen@cafe.fr', 'https://zen-cafe.fr'),
  ('Café Botanique',    '33 Boulevard Vert', 'Herbal,Tea', '+33-1-8888-7777', 'botanique@cafe.fr', 'https://botabotanique.fr'),
  ('Café Moderne',      '10 Rue Futur', 'Cold Brew,Espresso', '+33-9-0000-1111', 'modern@cafe.fr', 'https://cafemoderne.fr'),
  ('Café du Nord',      '1 Place du Nord', 'Espresso,Latte', '+33-1-2222-3333', 'nord@cafe.fr', 'https://cafedunord.fr'),
  ('Café Indigo',       '74 Rue Bleue', 'Latte,Mocha', '+33-6-4545-6767', 'contact@indigo.fr', 'https://cafeindigo.fr'),
  ('Café Vintage',      '22 Rue Ancienne', 'Mocha,Hot Chocolate', '+33-4-9999-0000', 'vintage@cafe.fr', 'https://cafevintage.fr'),
  ('Café Éclair',       '9 Rue de l’Orage', 'Espresso,Cold Brew', '+33-1-5678-1234', 'eclair@cafe.fr', 'https://cafeeclair.fr'),
  ('Café Plume',        '55 Rue des Poètes', 'Latte,Matcha', '+33-1-3141-5161', 'plume@cafe.fr', 'https://cafeplume.fr');

-- 6) CATEGORIES
INSERT INTO categories (nom) VALUES
  ('Good for studying'),
  ('Open Early'),
  ('Great for work'),
  ('Open Late'),
  ('Cozy Atmosphere'),
  ('Friendly Staff'),
  ('Great for Groups'),
  ('Great for Breakfast'),
  ('Great for Lunch'),
  ('Great for Dinner'),
  ('Great for Dessert'),
  ('Great for Coffee Lovers');

-- 7) REVUES
INSERT INTO revues (id_cafe, id_utilisateur, rating, titre, contenu) VALUES
  (1, 1, 5,  'Excellent Espresso',      'Un espresso parfait, crème onctueuse.'),
  (1, 2, 4,  'Bon mais cher',           'Très bon café, un peu cher pour la taille.'),
  (2, 1, 4,  'Super Cappuccino',        'Texture impeccable, service rapide.'),
  (3, 1, 5,  'Café du Coin',            'Un café chaleureux avec une ambiance conviviale.'),
  (1, 3, 4, 'Ambiance agréable', 'J’ai passé une bonne après-midi à travailler ici.'),
  (2, 4, 3, 'Un peu bruyant', 'Le café est bon mais il y avait beaucoup de bruit.'),
  (2, 5, 5, 'Parfait pour le petit-déjeuner', 'Le croissant et le cappuccino étaient délicieux.'),
  (3, 2, 5, 'Lieu idéal pour les groupes', 'Nous étions 6 et avons été bien accueillis.'),
  (4, 1, 4, 'Bonne musique de fond', 'Pas trop forte, juste ce qu’il faut.'),
  (1, 4, 3, 'Service lent', 'J’ai attendu 15 minutes pour un café.'),
  (3, 5, 5, 'Décor charmant', 'Décoration vintage très réussie.'),
  (2, 3, 2, 'Pas à la hauteur', 'Trop cher pour la qualité.'),
  (4, 5, 4, 'Endroit calme', 'Idéal pour lire un livre.'),
  (1, 2, 5, 'Mon QG du matin', 'J’y passe tous les matins avant le boulot.'),
  (2, 1, 4, 'Wi-Fi rapide', 'Parfait pour télétravailler.'),
  (3, 4, 5, 'Meilleur café en ville', 'Sans doute le meilleur espresso que j’ai bu.'),
  (4, 3, 4, 'Très cozy', 'On se sent comme à la maison.'),
  (2, 2, 3, 'Correct', 'Pas mal mais pas exceptionnel.'),
  (3, 3, 5, 'Je recommande !', 'Ambiance, café, tout est top.');
  


-- 8) REVUES_CATEGORIES (liaisons N–N)
INSERT INTO revues_categories (id_categorie, id_revue) VALUES
  (1, 1),  -- Good for studying → revue 1
  (3, 1),  -- Good for work    → revue 1
  (2, 2),  -- Open Early → revue 2
  (4, 3),  -- Open Late → revue 3
  (5, 1),  -- Cozy Atmosphere → revue 1
  (6, 2),  -- Friendly Staff → revue 2
  (3, 3),  -- Great for Groups → revue 3
  (1, 2),  -- Great for Breakfast → revue 2
  (2, 3),  -- Great for Lunch → revue 3
  (4, 1),  -- Great for Dinner → revue 1
  (5, 2),  -- Great for Dessert → revue 2
  (6, 3),  -- Great for Coffee Lovers → revue 3;
  (2, 4),  -- Great for Lunch → revue 4
  (3, 4),  -- Great for Groups → revue 4
  (1, 5), (3, 5), (5, 5),             -- Revue 5
  (7, 6), (6, 6),                     -- Revue 6
  (8, 7), (11, 7),                    -- Revue 7
  (7, 8), (6, 8),                     -- Revue 8
  (5, 9), (1, 9),                     -- Revue 9
  (5, 10), (2, 10),                   -- Revue 10
  (3, 11), (6, 11),                   -- Revue 11
  (1, 12), (4, 12), (11, 12),         -- Revue 12
  (5, 13), (6, 13),                   -- Revue 13
  (3, 14),                            -- Revue 14
  (1, 15), (3, 15), (5, 15);          -- Revue 15

-- 9) COMMENTAIRES
INSERT INTO commentaires (id_revue, id_utilisateur, contenu) VALUES
  (1, 2, 'Merci pour ce retour, j’irai tester !'),
  (3, 1, 'J’adore ce café aussi.');

-- 10) FOLLOWERS
INSERT INTO followers (follower_id, followee_id) VALUES
  (2, 1),  -- Bob suit Alice
  (3, 1),  -- Carol suit Alice
  (1, 2);  -- Alice suit Bob

-- 11) PHOTOS_REVUE
INSERT INTO photos_revue (id_revue, filepath, caption, is_primary) VALUES
  (1, 'assets/images/uploads/espresso1.jpg', 'Mon shot d’espresso', 1),
  (2, 'assets/images/uploads/latte2.jpg',    'Latte art sympa',     1);

-- 12) LIKES
INSERT INTO likes (id_revue, id_utilisateur) VALUES
  (1, 2),  -- Bob aime revue 1
  (1, 3),  -- Carol aime revue 1
  (3, 1);  -- Alice aime revue 3
