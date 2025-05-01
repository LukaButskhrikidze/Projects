-- Luka Butskhrikidze
-- luka.butskhrikidze@vanderbilt.edu
-- project

-- part 1
DROP DATABASE IF EXISTS nc_voter_data_2;
CREATE DATABASE nc_voter_data_2;

USE nc_voter_data_2;

SET GLOBAL local_infile = 1;

-- Creating the table "mega"
DROP TABLE IF EXISTS mega;
CREATE TABLE mega(
precinct_desc VARCHAR(10), 
party_cd VARCHAR(5), 
race_code VARCHAR(3), 
sex_code VARCHAR(3), 
age INT, 
pct_portion DECIMAL(9,6), 
first_name VARCHAR(25), 
middle_name VARCHAR(25),
last_name VARCHAR(25), 
full_name_mail VARCHAR(255), 
mail_addr1 VARCHAR(255), 
res_city_desc VARCHAR(255), 
state_cd VARCHAR(4), 
zip_code VARCHAR(10), 
registr_dt DATETIME,
voter_reg_num INT, 
nc_senate_desc VARCHAR(255), 
nc_house_desc VARCHAR(255), 
E1 INT, 
E1_date DATE, 
E1_VotingMethod VARCHAR(30), 
E1_PartyCd VARCHAR(5),
E2 INT, 
E2_Date DATE, 
E2_VotingMethod VARCHAR(30), 
E2_PartyCd VARCHAR(5), 
E3 INT, 
E3_Date DATE, 
E3_VotingMethod CHAR(3), 
E3_PartyCd CHAR(5)
);

-- Loading the data
LOAD DATA LOCAL INFILE 'C:/Users/Luka_Butskhrikidze/Desktop/Database/voter_data_24.csv'
INTO TABLE mega
FIELDS TERMINATED BY ';'
OPTIONALLY ENCLOSED BY '$'
LINES TERMINATED BY '\r\n'
(
    @precinct_desc, @party_cd, @race_code, @sex_code, @age, @pct_portion, 
    @first_name, @middle_name, @last_name, @full_name_mail, @mail_addr1, 
    @res_city_desc, @state_cd, @zip_code, @registr_dt, @voter_reg_num, 
    @nc_senate_desc, @nc_house_desc, @E1, @E1_date, @E1_VotingMethod, @E1_PartyCd, 
    @E2, @E2_Date, @E2_VotingMethod, @E2_PartyCd, @E3, @E3_Date, @E3_VotingMethod, @E3_PartyCd
)
SET 
    precinct_desc = NULLIF(TRIM(@precinct_desc), ''),
    party_cd = NULLIF(TRIM(@party_cd), ''),
    race_code = NULLIF(TRIM(@race_code), ''),
    sex_code = NULLIF(TRIM(@sex_code), ''),
    age = NULLIF(TRIM(@age), ''),
    pct_portion = NULLIF(TRIM(@pct_portion), ''),
    first_name = NULLIF(TRIM(@first_name), ''),
    middle_name = NULLIF(TRIM(@middle_name), ''),
    last_name = NULLIF(TRIM(@last_name), ''),
    full_name_mail = NULLIF(TRIM(@full_name_mail), ''),
    mail_addr1 = NULLIF(TRIM(@mail_addr1), ''),
    res_city_desc = NULLIF(TRIM(@res_city_desc), ''),
    state_cd = NULLIF(TRIM(@state_cd), ''),
    zip_code = NULLIF(TRIM(@zip_code), ''),
    registr_dt = NULLIF(TRIM(@registr_dt), ''),
    voter_reg_num = NULLIF(TRIM(@voter_reg_num), ''),
    nc_senate_desc = NULLIF(TRIM(@nc_senate_desc), ''),
    nc_house_desc = NULLIF(TRIM(@nc_house_desc), ''),
    E1 = NULLIF(TRIM(@E1), ''),
    E1_Date = IF(@E1_Date = '',NULL, STR_TO_DATE(@E1_Date, '%m/%d/%Y')), 
	E1_VotingMethod = NULLIF(TRIM(@E1_VotingMethod), ''),
    E1_PartyCd = NULLIF(TRIM(@E1_PartyCd), ''),
    E2 = NULLIF(TRIM(@E2), ''),
    E2_Date = IF(@E2_Date = '',NULL, STR_TO_DATE(@E2_Date, '%m/%d/%Y')),
    E2_VotingMethod = NULLIF(TRIM(@E2_VotingMethod), ''),
    E2_PartyCd = NULLIF(TRIM(@E2_PartyCd), ''),
    E3 = NULLIF(TRIM(@E3), ''),
    E3_Date = IF(@E3_Date = '',NULL, STR_TO_DATE(@E3_Date, '%m/%d/%Y')),
    E3_VotingMethod = NULLIF(TRIM(@E3_VotingMethod), ''),
    E3_PartyCd = NULLIF(TRIM(@E3_PartyCd), '')
;


SELECT COUNT(*)
FROM mega;

-- part 2
-- CREATE VIEW FOR FLATTENED VOTE DATA
DROP VIEW IF EXISTS voter_vote_view2;
CREATE VIEW voter_vote_view2 AS
SELECT 
    voter_reg_num,
    'E1' AS election_code,
    E1 AS election_id,
    E1_Date AS election_date,
    E1_VotingMethod AS voting_method,
    E1_PartyCd AS party_cd
FROM mega
WHERE E1 IS NOT NULL

UNION ALL

SELECT 
    voter_reg_num,
    'E2' AS election_code,
    E2 AS election_id,
    E2_Date AS election_date,
    E2_VotingMethod AS voting_method,
    E2_PartyCd AS party_cd
FROM mega
WHERE E2 IS NOT NULL

UNION ALL

SELECT 
    voter_reg_num,
    'E3' AS election_code,
    E3 AS election_id,
    E3_Date AS election_date,
    E3_VotingMethod AS voting_method,
    E3_PartyCd AS party_cd
FROM mega
WHERE E3 IS NOT NULL;

-- CREATE AND POPULATE NORMALIZED TABLES

-- Functional Dependency 1: pct_portion → precinct_desc, nc_senate_desc, nc_house_desc

DROP TABLE IF EXISTS pct_portion_info;
CREATE TABLE pct_portion_info
(
	pct_portion DECIMAL(9,6), 
    precinct_desc VARCHAR(10), 
    nc_senate_desc VARCHAR(255), 
    nc_house_desc VARCHAR(255),
    PRIMARY KEY (pct_portion) 
);

DROP TABLE IF EXISTS miscallenious;
CREATE TABLE miscallenious LIKE mega;

INSERT INTO miscallenious
SELECT * 
FROM mega
WHERE pct_portion IS NULL;


INSERT INTO pct_portion_info
SELECT DISTINCT pct_portion, precinct_desc, nc_senate_desc, nc_house_desc
FROM mega
WHERE pct_portion IS NOT NULL;

SELECT COUNT(*)
FROM pct_portion_info;

-- Functional Dependency 2: election_id → election_date

DROP TABLE IF EXISTS election_id_info;
CREATE TABLE election_id_info
(
	election_id INT,
    election_date DATE,
    PRIMARY KEY (election_id)
);

INSERT INTO election_id_info
SELECT DISTINCT election_id, election_date
FROM   voter_vote_view2
WHERE election_id IS NOT NULL
	AND election_date IS NOT NULL;


SELECT COUNT(*)
FROM election_id_info;


-- Functional Dependency 3: election_code → election_id

DROP TABLE IF EXISTS election_code_info;
create table election_code_info
(
	election_code VARCHAR(5),
    election_id	INT,
    PRIMARY KEY (election_code),
    FOREIGN KEY (election_id) REFERENCES election_id_info (election_id)
		ON UPDATE CASCADE
        ON DELETE CASCADE
);


INSERT INTO election_code_info
SELECT DISTINCT election_code, election_id
FROM voter_vote_view2
WHERE election_code IS NOT NULL;

SELECT COUNT(*)
FROM election_code_info;

-- Functional Dependency 4: (full_name_mail, registr_dt) → first_name, middle_name, last_name

DROP TABLE IF EXISTS person_info;
CREATE TABLE person_info
(
	full_name_mail VARCHAR(255), 
    registr_dt DATETIME,
    first_name VARCHAR(25), 
    middle_name VARCHAR(25),
    last_name VARCHAR(25), 
    PRIMARY KEY (full_name_mail, registr_dt)
);

    
INSERT INTO person_info
SELECT DISTINCT full_name_mail, registr_dt, first_name, middle_name, last_name
FROM mega;

SELECT COUNT(*)
FROM person_info;

-- Functional Dependency 5: res_city_desc → state_cd

DROP TABLE IF EXISTS res_city_desc_info;
CREATE TABLE res_city_desc_info
(
	res_city_desc VARCHAR(255), 
    state_cd VARCHAR(4),
    PRIMARY KEY (res_city_desc)
);

INSERT INTO res_city_desc_info
SELECT DISTINCT res_city_desc, state_cd
FROM mega;

SELECT COUNT(*)
FROM res_city_desc_info;



-- Functional Dependency 6: zip_code → res_city_desc

DROP TABLE IF EXISTS zip_code_info;
CREATE TABLE zip_code_info
(
	zip_code VARCHAR(10), 
    res_city_desc VARCHAR(255),
    PRIMARY KEY (zip_code),
    FOREIGN KEY (res_city_desc) REFERENCES res_city_desc_info (res_city_desc)
		ON UPDATE CASCADE
        ON DELETE CASCADE
);


INSERT INTO zip_code_info
SELECT DISTINCT zip_code, res_city_desc
FROM mega;

SELECT COUNT(*)
FROM zip_code_info;

-- Functional Dependency 7: voter_reg_num → race_code, sex_code, age, mail_addr1, party_cd

DROP TABLE IF EXISTS voter_attributes;
CREATE TABLE voter_attributes
(
	voter_reg_num INT,
    race_code VARCHAR(3), 
    sex_code VARCHAR(3), 
    age INT, 
    mail_addr1 VARCHAR(255), 
    party_cd VARCHAR(5),
    full_name_mail VARCHAR(255), 
    registr_dt DATETIME,
    pct_portion DECIMAL(9,6), 
    zip_code VARCHAR(10), 
    PRIMARY KEY (voter_reg_num),
    FOREIGN KEY (full_name_mail,registr_dt) REFERENCES person_info (full_name_mail,registr_dt)
		ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (pct_portion) REFERENCES pct_portion_info (pct_portion)
		ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (zip_code) REFERENCES zip_code_info (zip_code)
		ON UPDATE CASCADE
        ON DELETE CASCADE
);

INSERT INTO voter_attributes
SELECT DISTINCT voter_reg_num, race_code, sex_code, age, mail_addr1, party_cd,full_name_mail, registr_dt, pct_portion, zip_code
FROM mega;

SELECT COUNT(*)
FROM voter_attributes;

-- Functional Dependency 8: voter_reg_num, election_code → voting_method, party_cd

DROP TABLE IF EXISTS voter_and_election_info;
CREATE TABLE voter_and_election_info
(
	voter_reg_num INT,
    election_code VARCHAR(5),
    voting_method VARCHAR(30), 
    party_cd1 VARCHAR(5),
    PRIMARY KEY (voter_reg_num,election_code),
    FOREIGN KEY (election_code) REFERENCES election_code_info (election_code)
		ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (voter_reg_num) REFERENCES voter_attributes (voter_reg_num)
		ON UPDATE CASCADE
        ON DELETE CASCADE
);


INSERT INTO voter_and_election_info
SELECT DISTINCT voter_reg_num, election_code, voting_method, party_cd
FROM voter_vote_view2;

SELECT COUNT(*)
FROM voter_and_election_info;

CREATE OR REPLACE VIEW voter_vote_view2 AS
SELECT 
    vei.voter_reg_num,
    vei.election_code,
    vei.election_code AS election_id,
    e.election_date,
    vei.voting_method,
    vei.party_cd1 AS party_cd
FROM voter_and_election_info vei
LEFT JOIN election_id_info e
    ON vei.election_code = e.election_id;


-- part 3 -------------------------------------------------------------------------------

-- search voter can be done directly from the voter_attributes table

-- Voting History
DROP PROCEDURE IF EXISTS get_voting_record;
DELIMITER //

CREATE PROCEDURE get_voting_record (IN num CHAR(12))

BEGIN

SELECT election_id AS 'Election ID', 
	voting_method AS 'Voting Method',
    party_cd AS 'Party'
FROM voter_vote_view2
WHERE voter_reg_num = num;

END //

DELIMITER ;

-- analytics 1 --------------------------------------------------------------------------

-- view 1 - Constituents Party Statistics
DROP VIEW IF EXISTS constituent_stats;
CREATE VIEW constituent_stats AS
SELECT 
    party_cd AS Party,
    COUNT(*) AS Count,
    (COUNT(*) * 100.0) / (SELECT COUNT(*) FROM voter_attributes) AS Percentage
FROM voter_attributes
GROUP BY party_cd
ORDER BY Count DESC;

-- view 2 - Democratic Party Gender Voting Statistics
DROP VIEW IF EXISTS dem_gender_stats;
CREATE VIEW dem_gender_stats AS
SELECT 
    sex_code AS Gender,
    COUNT(*) AS Count,
    (COUNT(*) * 100.0) / (SELECT COUNT(*) FROM voter_attributes WHERE party_cd = 'DEM') 
		AS Percentage
FROM voter_attributes
WHERE party_cd = 'DEM'
GROUP BY Gender
ORDER BY Count DESC;

-- Analytics 2 -------------------------------------------------------------------------

-- view 1 - Voting Method Preference by Party
DROP VIEW IF EXISTS voting_method_by_party;
CREATE VIEW voting_method_by_party AS
SELECT 
    v.party_cd1 AS Party,
    v.voting_method,
    COUNT(*) AS vote_count,
    (COUNT(*) * 100.0) / p.total_votes AS percent_within_party
FROM voter_and_election_info v
	JOIN (
		SELECT party_cd1, COUNT(*) AS total_votes
		FROM nc_voter_data_2.voter_and_election_info
		WHERE party_cd1 IS NOT NULL AND voting_method IS NOT NULL
		GROUP BY party_cd1
			) p ON v.party_cd1 = p.party_cd1
WHERE v.party_cd1 IS NOT NULL AND v.voting_method IS NOT NULL
GROUP BY v.party_cd1, v.voting_method, p.total_votes;

-- view 2 - Party Preference by ZIP Code
DROP VIEW party_preference_by_zip;
CREATE VIEW party_preference_by_zip AS
SELECT 
    va.zip_code AS zip_code,
    vei.party_cd1 AS party,
    COUNT(*) AS vote_count,
    ROUND((COUNT(*) * 100.0) / total.total_votes_in_zip, 2) AS percent_within_zip
FROM voter_attributes va
	JOIN voter_and_election_info vei ON va.voter_reg_num = vei.voter_reg_num
	JOIN (
		SELECT 
			va.zip_code,
			COUNT(*) AS total_votes_in_zip
		FROM nc_voter_data_2.voter_attributes va
		JOIN nc_voter_data_2.voter_and_election_info vei
			ON va.voter_reg_num = vei.voter_reg_num
		WHERE va.zip_code IS NOT NULL AND vei.party_cd1 IS NOT NULL
		GROUP BY va.zip_code
		) AS total
ON va.zip_code = total.zip_code
WHERE va.zip_code IS NOT NULL AND vei.party_cd1 IS NOT NULL
GROUP BY va.zip_code, vei.party_cd1, total.total_votes_in_zip
ORDER BY va.zip_code, percent_within_zip DESC;


-- Insert Record ------------------------------------------------------------------------

-- Audit Insert Table
CREATE TABLE IF NOT EXISTS audit_insert (
    voter_reg_num CHAR(12),
    election_code VARCHAR(10),
    voting_method VARCHAR(10),
    party_cd1 VARCHAR(10),
    timestamp_inserted TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Audit Update Table
DROP TRIGGER IF EXISTS trg_after_vote_update;
DELIMITER //
CREATE TRIGGER trg_after_vote_update
AFTER UPDATE ON voter_and_election_info
FOR EACH ROW
BEGIN
    INSERT INTO audit_insert (voter_reg_num, election_code, voting_method, party_cd1)
    VALUES (NEW.voter_reg_num, NEW.election_code, NEW.voting_method, NEW.party_cd1);
END;
//
DELIMITER ;

-- Audit Delete Table
CREATE TABLE IF NOT EXISTS audit_delete (
    voter_reg_num CHAR(12),
    full_name_mail VARCHAR(255),
    registr_dt DATE,
    timestamp_deleted TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Trigger to normalize party code to N/A
DROP TRIGGER IF EXISTS trg_check_party_code;
DELIMITER //
CREATE TRIGGER trg_check_party_code
BEFORE INSERT ON voter_and_election_info
FOR EACH ROW
BEGIN
    IF NEW.party_cd1 NOT IN ('DEM', 'REP', 'LIB', 'GRE', 'UNA') THEN
        SET NEW.party_cd1 = 'N/A';
    END IF;
END //
DELIMITER ;


-- Trigger for audit insert
DROP TRIGGER IF EXISTS trg_after_vote_insert;
DELIMITER //
CREATE TRIGGER trg_after_vote_insert
AFTER INSERT ON voter_and_election_info
FOR EACH ROW
BEGIN
    INSERT INTO audit_insert (voter_reg_num, election_code, voting_method, party_cd1)
    VALUES (NEW.voter_reg_num, NEW.election_code, NEW.voting_method, NEW.party_cd1);
END //
DELIMITER ;

-- Trigger for audit delete
DROP TRIGGER IF EXISTS trg_before_voter_delete;
DELIMITER //
CREATE TRIGGER trg_before_voter_delete
BEFORE DELETE ON voter_attributes
FOR EACH ROW
BEGIN
    INSERT INTO audit_delete (voter_reg_num, full_name_mail, registr_dt)
    VALUES (OLD.voter_reg_num, OLD.full_name_mail, OLD.registr_dt);
END //
DELIMITER ;

-- Stored Procedure
DROP PROCEDURE IF EXISTS insert_record;
DELIMITER //

CREATE PROCEDURE insert_record (
    IN in_voter_reg_num CHAR(12),
    IN in_election_code VARCHAR(10),
    IN in_voting_method VARCHAR(10),
    IN in_party_cd1 VARCHAR(10),
    OUT result_message VARCHAR(255)
)
BEGIN
    DECLARE existing_count INT DEFAULT 0;
    DECLARE election_count INT DEFAULT 0;

    -- Check if voter exists
    SELECT COUNT(*) INTO existing_count 
    FROM voter_attributes 
    WHERE voter_reg_num = in_voter_reg_num;

    IF existing_count = 0 THEN
        SET result_message = 'Voter registration number does not exist';
    
    ELSE
        -- Check if voting record already exists
        SELECT COUNT(*) INTO election_count 
        FROM voter_and_election_info
        WHERE voter_reg_num = in_voter_reg_num AND election_code = in_election_code;

        IF election_count = 0 THEN
            INSERT INTO voter_and_election_info (voter_reg_num, election_code, voting_method, party_cd1)
            VALUES (in_voter_reg_num, in_election_code, in_voting_method, in_party_cd1);
            
            SET result_message = 'New voting record inserted';

        ELSE
            UPDATE voter_and_election_info
            SET voting_method = in_voting_method,
                party_cd1 = in_party_cd1
            WHERE voter_reg_num = in_voter_reg_num AND election_code = in_election_code;

            SET result_message = 'Voting record updated';
        END IF;
    END IF;

    -- Return result to PHP
    SELECT result_message AS msg;
END //

DELIMITER ;


-- Stored Procedure to delete a voter and all related info --------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS delete_voter;
DELIMITER //
CREATE PROCEDURE delete_voter (
    IN in_voter_reg_num CHAR(12)
)
BEGIN
    START TRANSACTION;

    -- Delete from dependent tables first to maintain referential integrity
    DELETE FROM voter_and_election_info WHERE voter_reg_num = in_voter_reg_num;

    -- This will trigger audit logging
    DELETE FROM voter_attributes WHERE voter_reg_num = in_voter_reg_num;

    COMMIT;
END //
DELIMITER ;

INSERT INTO person_info (full_name_mail, registr_dt, first_name, middle_name, last_name)
VALUES 
  ('Test User', '1966-04-01', 'Test', '', 'User'),
  ('Test User 2', '1960-05-01', 'Test', '', 'User 2');

INSERT INTO voter_attributes (
    voter_reg_num, full_name_mail, registr_dt, age, race_code, sex_code, party_cd
)
VALUES 
  ('0000000111', 'Test User', '1966-04-01', 58, 'W', 'M', 'DEM'),
  ('99999991', 'Test User 2', '1960-05-01', 64, 'B', 'F', 'REP');








