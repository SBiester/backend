CREATE TABLE tbl_kategorie (
    KategorieID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255)
);

CREATE TABLE tbl_hersteller (
    HerstellerID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255)
);

CREATE TABLE tbl_rollengruppe (
    RollengruppeID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255)
);

CREATE TABLE tbl_sammelrollen (
    SammelrollenID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255),
    Schluessel VARCHAR(255),
    RollengruppeID INT,
    FOREIGN KEY (RollengruppeID) REFERENCES tbl_rollengruppe(RollengruppeID)
);

CREATE TABLE tbl_software (
    SoftwareID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255),
    HerstellerID INT,
    Sammelrollen bool NOT NULL,
    aktiv BOOLEAN,
    FOREIGN KEY (HerstellerID) REFERENCES tbl_hersteller(HerstellerID)
);

CREATE TABLE tbl_software_rollengruppe (
    Software_RollengruppeID INT PRIMARY KEY AUTO_INCREMENT,
    SoftwareID INT NOT NULL,
    RollengruppeID INT NOT NULL,
    FOREIGN KEY (SoftwareID) REFERENCES tbl_software(SoftwareID),
    FOREIGN KEY (RollengruppeID) REFERENCES tbl_rollengruppe(RollengruppeID)
);

CREATE TABLE tbl_funktion (
    FunktionID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255)
);

CREATE TABLE tbl_team (
    TeamID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255),
    FunktionID INT NOT NULL,
    FOREIGN KEY (FunktionID) REFERENCES tbl_funktion(FunktionID)
);

CREATE TABLE tbl_bereich (
    BereichID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255),
    TeamID INT NOT NULL,
    FOREIGN KEY (TeamID) REFERENCES tbl_team(TeamID)
);

CREATE TABLE tbl_referenz (
    ReferenzID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255),
    BereichID INT,
    aktiv BOOLEAN,
    FOREIGN KEY (BereichID) REFERENCES tbl_bereich(BereichID)
);

CREATE TABLE tbl_referenz_software (
    Referenz_SoftwareID INT PRIMARY KEY AUTO_INCREMENT,
    ReferenzID INT,
    SoftwareID INT,
    FOREIGN KEY (ReferenzID) REFERENCES tbl_referenz(ReferenzID),
    FOREIGN KEY (SoftwareID) REFERENCES tbl_software(SoftwareID)
);

CREATE TABLE tbl_sammelrollen_referenz (
    Sammelrollen_ReferenzID INT PRIMARY KEY AUTO_INCREMENT,
    ReferenzID INT,
    SammelrollenID INT,
    FOREIGN KEY (ReferenzID) REFERENCES tbl_referenz(ReferenzID),
    FOREIGN KEY (SammelrollenID) REFERENCES tbl_sammelrollen(SammelrollenID)
);

CREATE TABLE tbl_veraenderung_art (
    Veraenderung_ArtID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255)
);

CREATE TABLE tbl_veraenderung (
    VeraenderungID INT PRIMARY KEY AUTO_INCREMENT,
    Veraenderung_ArtID INT,
    AenderungZum DATE,
    BefristetBis DATE,
    Unternehmen VARCHAR(255),
    FOREIGN KEY (Veraenderung_ArtID) REFERENCES tbl_veraenderung_art(Veraenderung_ArtID)
);

CREATE TABLE tbl_ma_typ (
    MA_TypID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255)
);

CREATE TABLE tbl_position (
    PositionID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255)
);

CREATE TABLE tbl_ma (
    MAID INT PRIMARY KEY AUTO_INCREMENT,
    MA_Nummer VARCHAR(255),
    Vorname VARCHAR(255),
    Name VARCHAR(255),
    Funktion VARCHAR(255),
    Vorgesetzter VARCHAR(255),
    MA_TypID INT,
    BereichID INT,
    PositionID INT,
    FOREIGN KEY (MA_TypID) REFERENCES tbl_ma_typ(MA_TypID),
    FOREIGN KEY (BereichID) REFERENCES tbl_bereich(BereichID),
    FOREIGN KEY (PositionID) REFERENCES tbl_position(PositionID)
);

CREATE TABLE tbl_status (
    StatusID INT PRIMARY KEY,
    Bezeichnung VARCHAR(255)
);

CREATE TABLE tbl_auftrag (
    AuftragID INT PRIMARY KEY AUTO_INCREMENT,
    VeraenderungID INT,
    MAID INT,
    AuftragDatum DATE,
    AuftragMA VARCHAR(255),
    StatusID INT DEFAULT 1,
    Kommentar MEDIUMTEXT,
    FOREIGN KEY (VeraenderungID) REFERENCES tbl_veraenderung(VeraenderungID),
    FOREIGN KEY (MAID) REFERENCES tbl_ma(MAID),
    FOREIGN KEY (StatusID) REFERENCES tbl_status(StatusID)
);

CREATE TABLE tbl_hardware (
    HardwareID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255),
    KategorieID INT NOT NULL,
    FOREIGN KEY (KategorieID) REFERENCES tbl_kategorie(KategorieID)
);

CREATE TABLE tbl_referenz_hardware (
    Referenz_HardwareID INT PRIMARY KEY AUTO_INCREMENT,
    ReferenzID INT,
    HardwareID INT,
    FOREIGN KEY (ReferenzID) REFERENCES tbl_referenz(ReferenzID),
    FOREIGN KEY (HardwareID) REFERENCES tbl_hardware(HardwareID)
);

CREATE TABLE tbl_element (
    ElementID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255),
    SoftwareID INT,
    HardwareID INT,
    SammelrollenID INT,
    FOREIGN KEY (SoftwareID) REFERENCES tbl_software(SoftwareID),
    FOREIGN KEY (HardwareID) REFERENCES tbl_hardware(HardwareID),
    FOREIGN KEY (SammelrollenID) REFERENCES tbl_sammelrollen(SammelrollenID),
    CHECK (
        (CASE WHEN SoftwareID IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN HardwareID IS NOT NULL THEN 1 ELSE 0 END) +
        (CASE WHEN SammelrollenID IS NOT NULL THEN 1 ELSE 0 END) = 1
    )
);

CREATE TABLE tbl_auftrag_element (
    Auftrag_ElementID INT PRIMARY KEY AUTO_INCREMENT,
    AuftragID INT,
    ElementID INT,
    FOREIGN KEY (AuftragID) REFERENCES tbl_auftrag(AuftragID),
    FOREIGN KEY (ElementID) REFERENCES tbl_element(ElementID)
);

CREATE TABLE tbl_aktivitaet (
    AktivitaetID INT PRIMARY KEY AUTO_INCREMENT,
    Bezeichnung VARCHAR(255),
    Datum DATE
);

CREATE TABLE tbl_auftrag_referenz (
    Auftrag_ReferenzID INT PRIMARY KEY AUTO_INCREMENT,
    AuftragID INT,
    ReferenzID INT,
    FOREIGN KEY (AuftragID) REFERENCES tbl_auftrag(AuftragID),
    FOREIGN KEY (ReferenzID) REFERENCES tbl_referenz(ReferenzID)
);
