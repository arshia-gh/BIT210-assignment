DROP TABLE IF EXISTS Vaccinations;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Batches;
DROP TABLE IF EXISTS HealthcareCentres;
DROP TABLE IF EXISTS Vaccines;

CREATE TABLE Vaccines
(
    vaccineID    CHAR(2) PRIMARY KEY,
    vaccineName  VARCHAR(255) UNIQUE NOT NULL,
    manufacturer VARCHAR(255)        NOT NULL
);

CREATE TABLE HealthcareCentres
(
    centreName VARCHAR(255) PRIMARY KEY,
    address    VARCHAR(255) NOT NULL
);

CREATE TABLE Batches
(
    -- columns
    batchNo              VARCHAR(255)  PRIMARY KEY,
    expiryDate           DATE          NOT NULL,
    quantityAvailable    INT           NOT NULL CHECK ( quantityAvailable >= 0 ),
    quantityAdministered INT DEFAULT 0 NOT NULL CHECK ( quantityAdministered >= 0),

    -- foreign key
    vaccineID            CHAR(2)       NOT NULL,
    centreName           VARCHAR(255)  NOT NULL,

    -- constraints
    CONSTRAINT batches_fk_vaccine FOREIGN KEY (vaccineID) REFERENCES Vaccines (vaccineID),
    CONSTRAINT batches_fk_healthcare_centre FOREIGN KEY (centreName) REFERENCES HealthcareCentres (centreName)
);

CREATE TABLE Users
(
    username   VARCHAR(255) PRIMARY KEY,
    password   VARCHAR(255)        NOT NULL,
    email      VARCHAR(255) UNIQUE NOT NULL,
    fullName   VARCHAR(255)        NOT NULL,

    -- indicates the type of user
    userType   VARCHAR(255)        NOT NULL,

    -- admin uncommon columns
    staffID    VARCHAR(255) UNIQUE,
    centreName VARCHAR(255),

    -- patient uncommon columns
    ICPassport VARCHAR(255) UNIQUE,

    -- constraints
    CONSTRAINT users_fk_healthcare_centre FOREIGN KEY (centreName) REFERENCES HealthcareCentres (centreName),
    CONSTRAINT users_type_check CHECK (
                    (userType = 'administrator' AND ICPassport IS NULL AND staffID IS NOT NULL AND centreName IS NOT NULL) OR
                    (userType = 'patient' AND ICPassport IS NOT NULL AND staffID IS NULL AND centreName IS NULL))
);

CREATE TABLE Vaccinations
(
    -- columns
    vaccinationID   CHAR(8) PRIMARY KEY,
    appointmentDate DATE         NOT NULL,
    status          ENUM ('pending',
        'confirmed',
        'rejected',
        'administered')
        DEFAULT 'pending'        NOT NULL,
    remarks         VARCHAR(255),

    -- foreign keys
    username        VARCHAR(255) NOT NULL,
    batchNo         VARCHAR(255) NOT NULL,

    -- constraints -- find the easier way lmao
    CONSTRAINT vaccinations_remarks_check CHECK (status = 'confirmed' OR status = 'administered' OR
                                                ((status = 'accepted' OR status = 'pending') AND remarks IS NULL)),

    -- foreign key constraints
    CONSTRAINT vaccinations_fk_user FOREIGN KEY (username) REFERENCES Users (username),
    CONSTRAINT vaccinations_fk_batch FOREIGN KEY (batchNo) REFERENCES Batches (batchNo)
);

-- DATA INSERTION --

-- insert vaccine
INSERT INTO Vaccines
VALUES ('PF', 'Pfizer', 'Pfizer Biotech Ltd'),
       ('SI', 'Sinovac', 'Sinovac Biotech Ltd'),
       ('AS', 'AstraZeneca', 'AstraZeneca Biotech Ltd');

-- insert healthcare center
INSERT INTO HealthcareCentres
VALUES ('Century Medical Centre',
        '55-57, Jalan SS 25/2, Taman Mayang, 47301 Petaling Jaya, Selangor'),
       ('Klinik Impian Care 24 jam',
        '3g, Tingkat bawah, Jalan Bunga Cempaka 6a, Taman Muda, 68000 Ampang, Selangor'),
       ('Healthcare Dialysis Centre Sdn Bhd',
        '41, Jalan 6/31, Seksyen 6, 46000 Petaling Jaya, Selangor');

-- insert administrator
INSERT INTO Users(username, password, email, fullName, userType, staffID, centreName)
VALUES ('clinton', 'clinton123456', 'clinton@email.com', 'Clinton', 'administrator', 'B100100',
        'Century Medical Centre'),
       ('carrick', 'carrick123456', 'carrick@email.com', 'Carrick', 'administrator', 'B900600',
        'Klinik Impian Care 24 jam'),
       ('michael', 'michael123456', 'michael@email.com', 'Michael Wijaya', 'administrator', 'B100200',
        'Healthcare Dialysis Centre Sdn Bhd');

-- insert patient
INSERT INTO Users(username, password, email, fullName, userType, ICPassport)
VALUES ('john_banana', 'john123456', 'john_banana@email.com', 'John Banana', 'patient', 'H400100'),
       ('papaya_tyler', 'papaya123456', 'papaya_tyler@email.com', 'Papaya Tyler', 'patient', 'H800200'),
       ('kiwi_swift', 'kiwi123456', 'kiwi_swift@email.com', 'Kiwi Swift', 'patient', 'H400300');

-- insert batch
INSERT INTO Batches(batchNo, expiryDate, quantityAvailable, vaccineID, centreName)
VALUES ('PF01', '2021-12-15', 100, 'PF', 'Century Medical Centre'),
       ('SI03', '2021-11-20', 200, 'SI', 'Century Medical Centre'),
       ('PF05', '2021-10-12', 150, 'PF', 'Klinik Impian Care 24 jam');

-- insert vaccination
INSERT INTO Vaccinations(vaccinationID, appointmentDate, username, batchNo)
VALUES ('16346668', '2021-08-20', 'john_banana', 'PF01'),
       ('20341010', '2021-09-28', 'papaya_tyler', 'SI03'),
       ('50345030', '2021-08-22', 'kiwi_swift', 'PF01');
