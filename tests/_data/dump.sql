BEGIN TRANSACTION;

DROP TABLE IF EXISTS "model";
CREATE TABLE IF NOT EXISTS "model"
(
    "id"                INTEGER   NOT NULL PRIMARY KEY AUTOINCREMENT,
    "list"              VARCHAR(255),
    "created_at"        TIMESTAMP NOT NULL,
    "updated_at"        TIMESTAMP NOT NULL,
    "status"            VARCHAR(10) DEFAULT 'new',
    "body"              TEXT
);

DROP TABLE IF EXISTS "relation";
CREATE TABLE IF NOT EXISTS "relation"
(
    "id"                INTEGER   NOT NULL PRIMARY KEY AUTOINCREMENT,
    "rel_id"            INTEGER   NULL,
    "title"             VARCHAR(255)
);

DROP TABLE IF EXISTS "junction";
CREATE TABLE IF NOT EXISTS "junction"
(
    "stub_id"           INTEGER   NOT NULL,
    "rel_id"            INTEGER   NOT NULL,
    PRIMARY KEY("stub_id", "rel_id")
);

COMMIT;