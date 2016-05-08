--
-- Members Module PostgreSQL Database for Phire CMS 2.0
--

-- --------------------------------------------------------

--
-- Table structure for table "members"
--

CREATE SEQUENCE member_id_seq START 8001;

CREATE TABLE IF NOT EXISTS "[{prefix}]members" (
  "id" integer NOT NULL DEFAULT nextval('member_id_seq'),
  "role_id" integer NOT NULL,
  "name" varchar(255) NOT NULL,
  "uri" varchar(255) NOT NULL,
  "redirect" varchar(255),
  PRIMARY KEY ("id"),
  CONSTRAINT "fk_member_role_id" FOREIGN KEY ("role_id") REFERENCES "[{prefix}]roles" ("id") ON DELETE CASCADE ON UPDATE CASCADE
) ;

ALTER SEQUENCE member_id_seq OWNED BY "[{prefix}]members"."id";
