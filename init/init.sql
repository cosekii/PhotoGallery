/* TODO: create tables */
CREATE TABLE `users` (
  `user_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  `username`	TEXT NOT NULL UNIQUE,
  `password`	TEXT NOT NULL,
  `session`	TEXT
);

CREATE TABLE `images` (
  `image_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  `image_title`	TEXT NOT NULL,
  `image_description`	TEXT,
  `creator_id`  INTEGER NOT NULL,
  `ext`	TEXT NOT NULL
);

CREATE TABLE `tags` (
  `tag_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  `tag`	TEXT NOT NULL UNIQUE
);

CREATE TABLE `images_tags_mapping` (
  `id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  `image_id`	INTEGER NOT NULL,
  `tag_id`	INTEGER NOT NULL,
  UNIQUE  (image_id, tag_id)
);

/* TODO: initial seed data */
INSERT INTO users (username, password) VALUES ('abc123', '$2y$10$.NZyAeFvRt9dfNIxjClajOOKVm7YN.dopZNd6woPPNDtdbLQpCrd.');
INSERT INTO users (username, password) VALUES ('xyz777', '$2y$10$rusbvPKXdZFlXGx7gR096.GtWb9atZRpVmdYLHYkWWe1KQFzGb01u');

/* Image source: I took these images */
INSERT INTO images (image_title, image_description, creator_id, ext) VALUES('Half Moon Bay', 'It is beautiful!', 1, 'jpg');
INSERT INTO images (image_title, image_description, creator_id, ext) VALUES('SF', 'Night Scene in SF', 1, 'jpg');
INSERT INTO images (image_title, image_description, creator_id, ext) VALUES('Golden Bridge', 'Golden Bridge', 1, 'jpg');
INSERT INTO images (image_title, image_description, creator_id, ext) VALUES('Half Moon Bay', NULL, 1, 'jpg');
INSERT INTO images (image_title, image_description, creator_id, ext) VALUES('Duck Confit', 'It is good', 1, 'jpg');
INSERT INTO images (image_title, image_description, creator_id, ext) VALUES('Steak', 'So tasty!', 2, 'jpg');
INSERT INTO images (image_title, image_description, creator_id, ext) VALUES('SF Downtown', NULL, 2, 'jpg');
INSERT INTO images (image_title, image_description, creator_id, ext) VALUES('Cornell', 'Winter at Cornell', 2, 'jpg');
INSERT INTO images (image_title, image_description, creator_id, ext) VALUES('Chinese Spicy Fish', 'Really Spicy!', 2, 'jpg');
INSERT INTO images (image_title, image_description, creator_id, ext) VALUES('Trees at Cornell', 'So gorgeous.', 2, 'jpg');

INSERT INTO tags (tag) VALUES ('Cornell');
INSERT INTO tags (tag) VALUES ('Bay Area');
INSERT INTO tags (tag) VALUES ('Food');
INSERT INTO tags (tag) VALUES ('Downtown');
INSERT INTO tags (tag) VALUES ('Beach');

INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (1, 2);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (1, 5);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (2, 2);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (2, 4);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (3, 2);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (3, 4);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (4, 2);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (4, 5);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (5, 2);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (5, 3);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (5, 4);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (6, 2);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (6, 3);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (6, 4);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (7, 2);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (7, 4);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (8, 1);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (9, 2);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (9, 3);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (9, 4);
INSERT INTO images_tags_mapping (image_id, tag_id) VALUES (10, 1);
