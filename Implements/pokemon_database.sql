/*
    Implementation 2
    Jacob Ballard, Xu Song, Noah Feld
    CSC 362
*/

/* Remove and re-create DB */
DROP DATABASE IF EXISTS move_tutor;
CREATE DATABASE move_tutor;
USE move_tutor;

/* Table describes a Pokemon */
CREATE TABLE pokemons (
    PRIMARY KEY (poke_id),
    poke_id                INT AUTO_INCREMENT UNIQUE,
    poke_species           VARCHAR(100) NOT NULL,
    poke_name              VARCHAR(100)               
);

/* Stores different types that can be associated with 0 or many Pokemon */
CREATE TABLE types (
    PRIMARY KEY (poke_type),
    poke_type   VARCHAR(16)
);

/* Table describes a move associated with 0 or many Pokemon through the KnownMoves and LearnHistory tables */
CREATE TABLE moves (
    PRIMARY KEY (move_name),
    move_name   VARCHAR(32) NOT NULL,
    move_type   VARCHAR(16) NOT NULL,
    move_time   VARCHAR(16) NOT NULL,
    is_hm       VARCHAR(3)  NOT NULL,
    FOREIGN KEY (move_type) REFERENCES types(poke_type)
                            ON DELETE RESTRICT

);

/* Table associates a Pokemon with 1 - 4 moves */
CREATE TABLE known_moves (
    PRIMARY KEY (poke_id,move_name),
    poke_id     INT          NOT NULL,
    move_name   VARCHAR(32)  NOT NULL,
    FOREIGN KEY (poke_id) REFERENCES pokemons(poke_id)
                            ON DELETE RESTRICT,
    FOREIGN KEY (move_name) REFERENCES moves(move_name)
                            ON DELETE RESTRICT
    
);

/* If a Pokemon is taught a move, it's stored here */
CREATE TABLE learn_history (
    PRIMARY KEY (poke_id, move_name),
    poke_id     INT         NOT NULL,
    move_name   VARCHAR(32) NOT NULL,
    learn_date  DATE        DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (poke_id) REFERENCES pokemons (poke_id)
                        ON DELETE RESTRICT,
    FOREIGN KEY (move_name) REFERENCES moves (move_name)
                        ON DELETE RESTRICT
);

/* Stores the type of a Pokemon, Pokemon have 1 or 2 types */
CREATE TABLE poke_types (
    PRIMARY KEY (poke_id, poke_type),
    poke_id     INT     ,
    poke_type   VARCHAR(16),
    FOREIGN KEY (poke_id) REFERENCES pokemons (poke_id)
                        ON DELETE RESTRICT,
    FOREIGN KEY (poke_type) REFERENCES types (poke_type)
                        ON DELETE RESTRICT
);

--FUNCTION for getting the number of known_moves of a pokemon
CREATE FUNCTION get_move_numbers (pkmn_id INT)
RETURNS INT
RETURN(
    SELECT COUNT(move_name)
    FROM known_moves
    WHERE known_moves.poke_id=pkmn_id
);

--FUNCTION for getting the number of types of a pokemon
CREATE FUNCTION get_type_numbers (pkmn_id INT)
RETURNS INT
RETURN(
    SELECT COUNT(poke_type)
    FROM poke_types
    WHERE poke_types.poke_id=pkmn_id
);

--FUNCTION for checking if the move is hm
CREATE FUNCTION check_hm (moveName VARCHAR(50))
RETURNS VARCHAR(3)
RETURN(
    SELECT is_hm
    FROM moves
    WHERE moves.move_name=moveName
);

--TRIGGERS

---check_max_moves
DELIMITER //
CREATE TRIGGER check_max_moves
BEFORE INSERT ON known_moves
FOR EACH ROW
BEGIN
    IF get_move_numbers(NEW.poke_id)>=4 THEN
     SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Too many moves for pokemon!';
    END IF;
END;//
DELIMITER ;
-- check_min_moves
DELIMITER //
CREATE TRIGGER check_min_moves
BEFORE DELETE ON known_moves
FOR EACH ROW
BEGIN
    IF get_move_numbers(OLD.poke_id)=1 THEN
     SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A pokemon should know at least one move!';
    END IF;
END;//
DELIMITER ;

-- check_max_type
DELIMITER //
CREATE TRIGGER check_max_type
BEFORE INSERT ON poke_types
FOR EACH ROW
BEGIN
    IF get_type_numbers(NEW.poke_id)>=2 THEN
     SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Too many type for a Pokemon!';
    END IF;
END;//
DELIMITER ;

-- check_min_type
DELIMITER //
CREATE TRIGGER check_min_type
BEFORE DELETE ON poke_types
FOR EACH ROW
BEGIN
    IF get_type_numbers(OLD.poke_id)=1 THEN
     SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A pokemon should have at least one type!';
    END IF;
END;//
DELIMITER ;

-- check_hm
DELIMITER //
CREATE TRIGGER check_hm
BEFORE DELETE ON known_moves
FOR EACH ROW
BEGIN
    IF check_hm(OLD.move_name)="Yes" THEN
     SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'You cannot forget a hidden move!';
    END IF;
END;//
DELIMITER ;

-- insert to known_move before insert to learn_history
DELIMITER //
CREATE TRIGGER add_to_known_move
BEFORE INSERT ON learn_history
FOR EACH ROW
BEGIN
    INSERT INTO known_moves (poke_id,move_name)
    VALUES (NEW.poke_id,NEW.move_name);
END;//
DELIMITER ;






-- add values to validation table
INSERT INTO types (poke_type)
VALUES ('normal'),
       ('fighting'),
       ('flying'),
       ('poison'),
       ('ground'),
       ('rock'),
       ('bug'),
       ('ghost'),
       ('steel'),
       ('fire'),
       ('water'),
       ('ice'),
       ('electric'),
       ('dragon'),
       ('psychic'),
       ('dark'),
       ('fairy'),
       ('glass');

-- Add values to for testing
INSERT INTO pokemons (poke_species, poke_name)
VALUES  ('charmander', 'char'),
        ('pikachu', 'pika'),
        ('wailord', 'will'),
        ('pelliper', 'pelly');

INSERT INTO moves (move_name,move_type,move_time,is_hm)
VALUES  ('Thief', 'dark','Past','No'),
        ('Sleep_Talk', 'normal','Present','No'),
        ('Rain_Dance', 'water','Future','No'),
        ('Fusion_Bolt', 'electric','Past','No'),
        ('Dragon_Breath', 'dragon','Future','No'),
        ('Fire_Lash', 'fire','Present','No'),
        ('Bouncy_Bobble', 'water','Present','No'),
        ('Dragon_Dance', 'dragon','N/A','No');

INSERT INTO poke_types (poke_id, poke_type)
VALUES (1,'dragon'),
       (1,'fire'),
       (2,'electric'),
       (3,'water'),
       (4,'water'),
       (4,'flying');

INSERT INTO known_moves (poke_id,move_name)
VALUES (1,'dragon_dance'),
       (1,'sleep_talk'),
       (2,'sleep_talk'),
       (3,'Rain_Dance'),
       (4,'Rain_Dance');

INSERT INTO learn_history (poke_id, move_name)
VALUES (1,'dragon_breath'),
       (1,'fire_lash'),
       (2,'fusion_bolt'),
       (3,'Bouncy_Bobble'),
       (4,'Bouncy_Bobble');
