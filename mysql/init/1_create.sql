use webauthn;

CREATE TABLE test_table
(
    id INT(11) AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);
INSERT INTO test_table (name) VALUES ('fumiaki');

CREATE TABLE users
(
    id INT(11) AUTO_INCREMENT NOT NULL,
    email VARCHAR(255) NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

-- 公開鍵クレデンシャル保管用
CREATE TABLE credential
(
    credential_id     VARBINARY(255) NOT NULL,
    user_id           VARBINARY(64)  NOT NULL,
    public_key        BLOB           NOT NULL,
    signature_counter LONG           NOT NULL,
#    FOREIGN KEY (user_id) REFERENCES users (id),
    PRIMARY KEY (credential_id)
);