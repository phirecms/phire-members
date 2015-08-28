--
-- Members Module SQLite Database for Phire CMS 2.0
--

--  --------------------------------------------------------

--
-- Set database encoding
--

PRAGMA encoding = "UTF-8";
PRAGMA foreign_keys = ON;

-- --------------------------------------------------------

--
-- Table structure for table "members"
--

CREATE TABLE IF NOT EXISTS "[{prefix}]members" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "role_id" integer NOT NULL,
  "name" varchar NOT NULL,
  "uri" varchar NOT NULL,
  UNIQUE ("id"),
  CONSTRAINT "fk_member_role_id" FOREIGN KEY ("role_id") REFERENCES "[{prefix}]roles" ("id") ON DELETE CASCADE ON UPDATE CASCADE
) ;

INSERT INTO "sqlite_sequence" ("name", "seq") VALUES ('[{prefix}]members', 8000);
