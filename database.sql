PRAGMA foreign_keys = ON;



CREATE TABLE operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE
);


CREATE TABLE prefixe (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    operateur_id INTEGER NOT NULL,
    FOREIGN KEY (operateur_id) REFERENCES operateur(id)
);



CREATE TABLE type_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE
);



CREATE TABLE bareme_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min REAL NOT NULL,
    montant_max REAL NOT NULL,
    frais REAL NOT NULL,
    FOREIGN KEY (type_operation_id)
        REFERENCES type_operation(id)
);



CREATE TABLE client (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone TEXT NOT NULL UNIQUE,
    nom TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE compte (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL UNIQUE,
    solde REAL DEFAULT 0,
    FOREIGN KEY (client_id)
        REFERENCES client(id)
);



CREATE TABLE operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    type_operation_id INTEGER NOT NULL,

    compte_source INTEGER,
    compte_destination INTEGER,

    montant REAL NOT NULL,
    frais REAL NOT NULL,

    date_operation DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(type_operation_id)
        REFERENCES type_operation(id),

    FOREIGN KEY(compte_source)
        REFERENCES compte(id),

    FOREIGN KEY(compte_destination)
        REFERENCES compte(id)
);
