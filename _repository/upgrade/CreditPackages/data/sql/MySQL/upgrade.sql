

CREATE TABLE IF NOT EXISTS PH7_CreditPackages (
  packageId int(10) unsigned NOT NULL AUTO_INCREMENT,
  credits INT(10) DEFAULT 0,
  price DOUBLE DEFAULT 0,
  sortOrder INT default 0,
  enabled tinyint(3) unsigned DEFAULT 1,
  PRIMARY KEY (packageId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO PH7_CreditPackages (credits, price, sortOrder, enabled) VALUES (
    50, 9.99, 1, 1
);

INSERT INTO PH7_CreditPackages (credits, price, sortOrder, enabled) VALUES (
    180, 34.99, 2, 1
);

INSERT INTO PH7_CreditPackages (credits, price, sortOrder, enabled) VALUES (
    485, 84.99, 2, 1
);

INSERT INTO PH7_CreditPackages (credits, price, sortOrder, enabled) VALUES (
    900, 149.99, 2, 1
);

INSERT INTO PH7_CreditPackages (credits, price, sortOrder, enabled) VALUES (
    1550, 249.99, 2, 1
);
