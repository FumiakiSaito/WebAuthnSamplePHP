use webauthn;

CREATE TABLE test_table
(
    id INT(11) AUTO_INCREMENT NOT NULL,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_email (email)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='テスト用';
INSERT INTO test_table (email, name) VALUES ('a.jp', 'fumiaki');



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



CREATE TABLE `webauthn_credentials` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `public_key_credential_source_id` varchar(256) NOT NULL COMMENT '認証ID',
  `use_flag` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '使用フラグ[0:無効 1:有効]',
  `user_handle` varchar(256) NOT NULL COMMENT 'ユーザハンドル（ログインID）',
  `credential` text COMMENT '認証情報(json)'
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_public_key_credential_source_id` (`public_key_credential_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Webauthn認証';