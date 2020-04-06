use webauthn;
SET NAMES UTF8;

CREATE TABLE test_table
(
    id INT(11) AUTO_INCREMENT NOT NULL,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_email (email)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='テスト用';
INSERT INTO test_table (email, name) VALUES ('a.jp', 'fumiaki');


CREATE TABLE `credentials` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `credential_id` varchar(256) NOT NULL COMMENT '認証ID',
  `user_handle` varchar(256) NOT NULL COMMENT 'ユーザーハンドル（ログインID）',
  `credential` text COMMENT '認証情報(json)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_credential_id` (`credential_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Webauthn認証';