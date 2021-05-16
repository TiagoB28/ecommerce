-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.14-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_ecommerce
CREATE DATABASE IF NOT EXISTS `db_ecommerce` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `db_ecommerce`;

-- Dumping structure for table db_ecommerce.posts
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.posts: ~2 rows (approximately)
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` (`id`, `title`, `user`, `photo`, `slug`, `description`, `created_at`, `updated_at`) VALUES
	(11, 'Han Solo – Uma História Star Wars | Han Solo, Chewbacca, Lando e Qi Ra estampam nova capa de revista', 1, '/assets/imgs/photos/3151415203e0a11521d93aee66af11cd1523424667.jpg', 'han-solo-novo-filme-wtar-wars', 'Ron Howard assumiu o comando do filme após a saída de Phil Lord e Chris Miller – saiba mais. O elenco conta com nomes como Alden Ehrenreich, Donald Glover, Woody Harrelson, Emilia Clarke e Thandie Newton. A estreia no Brasil está marcada para 24 de maio\r\n\r\nHan Solo foi originalmente interpretado por Harrison Ford na franquia Star Wars, enquanto Lando Calrissian foi vivido por Billy Dee Williams em O Império Contra-Ataca e O Retorno de Jedi', '2018-04-10 03:21:35', '2018-04-11 02:40:47'),
	(12, 'The Walking Dead | Audiência melhora na reta final da temporada.', 1, '/assets/imgs/photos/67a1fa03f49e0fd12a94acaaca53f35a1523425844.jpg', 'the-walking-dead-audiencia-final-temporada', 'A audiência de The Walking Dead teve uma leve melhora com “Worth”, penúltimo episódio da oitava temporada. Segundo a Variety, o capítulo marcou 2,8 pontos na métrica da Nielsen dentro do segmento do público entre 18-49 anos de idade, sendo visto por 6,7 milhões de pessoas no momento da exibição. A marca corresponde a um aumento de 6% na audiência da série em relação ao episódio anterior (&#34;Still Gotta Mean Something&#34;), a pior marca do ano oito, com 2,6 pontos e 6,3 milhões de TV ligadas. \r\n\r\nA oitava temporada representou uma grande queda na audiência geral da série, que teve retorno de midseason menos assistido da série. &#34;The Lost and the Plunderers&#34;, décimo episódio, foi o primeiro a ficar abaixo dos 3 pontos desde a primeira temporada. \r\n\r\nO final da temporada vai ao ar em 15 de abril. No Brasil, o canal pago Fox se encarrega da transmissão do seriado.', '2018-04-11 02:50:03', '2018-04-11 02:54:02');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;

-- Dumping structure for procedure db_ecommerce.sp_categories_save
DELIMITER //
CREATE PROCEDURE `sp_categories_save`(
pidcategory INT,
pdescategory VARCHAR(64)
)
BEGIN
	
	IF EXISTS(SELECT pidcategory FROM tb_categories WHERE (idcategory = pidcategory)) THEN
		
		UPDATE tb_categories
        SET descategory = pdescategory
        WHERE idcategory = pidcategory;
        
    ELSE
		
		INSERT INTO tb_categories (descategory) VALUES(pdescategory);
        SET pidcategory = LAST_INSERT_ID();
        
    END IF;
    
    SELECT * FROM tb_categories WHERE idcategory = pidcategory;
    
END//
DELIMITER ;

-- Dumping structure for procedure db_ecommerce.sp_products_save
DELIMITER //
CREATE PROCEDURE `sp_products_save`(
pidproduct int(11),
pdesproduct varchar(64),
pvlprice decimal(10,2),
pvlwidth decimal(10,2),
pvlheight decimal(10,2),
pvllength decimal(10,2),
pvlweight decimal(10,2),
pdesurl varchar(128)
)
BEGIN
	
		IF EXISTS(SELECT pidproduct FROM tb_products WHERE (idproduct = pidproduct)) THEN
		
		UPDATE tb_products
        SET 
			desproduct = pdesproduct,
            vlprice = pvlprice,
            vlwidth = pvlwidth,
            vlheight = pvlheight,
            vllength = pvllength,
            vlweight = pvlweight,
            desurl = pdesurl
        WHERE idproduct = pidproduct;
        
    ELSE
		
		INSERT INTO tb_products (desproduct, vlprice, vlwidth, vlheight, vllength, vlweight, desurl) 
        VALUES(pdesproduct, pvlprice, pvlwidth, pvlheight, pvllength, pvlweight, pdesurl);
        
        SET pidproduct = LAST_INSERT_ID();
        
    END IF;
    
    SELECT * FROM tb_products WHERE idproduct = pidproduct;
    
END//
DELIMITER ;

-- Dumping structure for procedure db_ecommerce.sp_userspasswordsrecoveries_create
DELIMITER //
CREATE PROCEDURE `sp_userspasswordsrecoveries_create`(
piduser INT,
pdesip VARCHAR(45)
)
BEGIN
  
  INSERT INTO tb_userspasswordsrecoveries (iduser, desip)
    VALUES(piduser, pdesip);
    
    SELECT * FROM tb_userspasswordsrecoveries
    WHERE idrecovery = LAST_INSERT_ID();
    
END//
DELIMITER ;

-- Dumping structure for procedure db_ecommerce.sp_usersupdate_save
DELIMITER //
CREATE PROCEDURE `sp_usersupdate_save`(
piduser INT,
pdesperson VARCHAR(64), 
pdeslogin VARCHAR(64), 
pdespassword VARCHAR(256), 
pdesemail VARCHAR(128), 
pnrphone BIGINT, 
pinadmin TINYINT
)
BEGIN
  
    DECLARE vidperson INT;
    
  SELECT idperson INTO vidperson
    FROM tb_users
    WHERE iduser = piduser;
    
    UPDATE tb_persons
    SET 
    desperson = pdesperson,
        desemail = pdesemail,
        nrphone = pnrphone
  WHERE idperson = vidperson;
    
    UPDATE tb_users
    SET
    deslogin = pdeslogin,
        despassword = pdespassword,
        inadmin = pinadmin
  WHERE iduser = piduser;
    
    SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = piduser;
    
END//
DELIMITER ;

-- Dumping structure for procedure db_ecommerce.sp_users_delete
DELIMITER //
CREATE PROCEDURE `sp_users_delete`(
piduser INT
)
BEGIN
  
    DECLARE vidperson INT;
    
  SELECT idperson INTO vidperson
    FROM tb_users
    WHERE iduser = piduser;
    
    DELETE FROM tb_users WHERE iduser = piduser;
    DELETE FROM tb_persons WHERE idperson = vidperson;
    
END//
DELIMITER ;

-- Dumping structure for procedure db_ecommerce.sp_users_save
DELIMITER //
CREATE PROCEDURE `sp_users_save`(
pdesperson VARCHAR(64), 
pdeslogin VARCHAR(64), 
pdespassword VARCHAR(256), 
pdesemail VARCHAR(128), 
pnrphone BIGINT, 
pinadmin TINYINT
)
BEGIN
  
    DECLARE vidperson INT;
    
  INSERT INTO tb_persons (desperson, desemail, nrphone)
    VALUES(pdesperson, pdesemail, pnrphone);
    
    SET vidperson = LAST_INSERT_ID();
    
    INSERT INTO tb_users (idperson, deslogin, despassword, inadmin)
    VALUES(vidperson, pdeslogin, pdespassword, pinadmin);
    
    SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = LAST_INSERT_ID();
    
END//
DELIMITER ;

-- Dumping structure for table db_ecommerce.tb_addresses
CREATE TABLE IF NOT EXISTS `tb_addresses` (
  `idaddress` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `desaddress` varchar(128) NOT NULL,
  `descomplement` varchar(32) DEFAULT NULL,
  `descity` varchar(32) NOT NULL,
  `desstate` varchar(32) NOT NULL,
  `descountry` varchar(32) NOT NULL,
  `nrzipcode` int(11) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idaddress`),
  KEY `fk_addresses_persons_idx` (`idperson`),
  CONSTRAINT `fk_addresses_persons` FOREIGN KEY (`idperson`) REFERENCES `tb_persons` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_addresses: ~0 rows (approximately)
/*!40000 ALTER TABLE `tb_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_addresses` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_carts
CREATE TABLE IF NOT EXISTS `tb_carts` (
  `idcart` int(11) NOT NULL AUTO_INCREMENT,
  `dessessionid` varchar(64) NOT NULL,
  `iduser` int(11) DEFAULT NULL,
  `deszipcode` char(8) DEFAULT NULL,
  `vlfreight` decimal(10,2) DEFAULT NULL,
  `nrdays` int(11) DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idcart`),
  KEY `FK_carts_users_idx` (`iduser`),
  CONSTRAINT `fk_carts_users` FOREIGN KEY (`iduser`) REFERENCES `tb_users` (`iduser`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_carts: ~0 rows (approximately)
/*!40000 ALTER TABLE `tb_carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_carts` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_cartsproducts
CREATE TABLE IF NOT EXISTS `tb_cartsproducts` (
  `idcartproduct` int(11) NOT NULL AUTO_INCREMENT,
  `idcart` int(11) NOT NULL,
  `idproduct` int(11) NOT NULL,
  `dtremoved` datetime NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idcartproduct`),
  KEY `FK_cartsproducts_carts_idx` (`idcart`),
  KEY `FK_cartsproducts_products_idx` (`idproduct`),
  CONSTRAINT `fk_cartsproducts_carts` FOREIGN KEY (`idcart`) REFERENCES `tb_carts` (`idcart`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cartsproducts_products` FOREIGN KEY (`idproduct`) REFERENCES `tb_products` (`idproduct`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_cartsproducts: ~0 rows (approximately)
/*!40000 ALTER TABLE `tb_cartsproducts` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_cartsproducts` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_categories
CREATE TABLE IF NOT EXISTS `tb_categories` (
  `idcategory` int(11) NOT NULL AUTO_INCREMENT,
  `descategory` varchar(32) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_categories: ~2 rows (approximately)
/*!40000 ALTER TABLE `tb_categories` DISABLE KEYS */;
INSERT INTO `tb_categories` (`idcategory`, `descategory`, `dtregister`) VALUES
	(2, 'Android', '2021-04-12 11:38:06'),
	(30, 'Motorola', '2021-04-13 21:16:16'),
	(35, 'Apple', '2021-05-09 15:16:56');
/*!40000 ALTER TABLE `tb_categories` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_orders
CREATE TABLE IF NOT EXISTS `tb_orders` (
  `idorder` int(11) NOT NULL AUTO_INCREMENT,
  `idcart` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `idstatus` int(11) NOT NULL,
  `vltotal` decimal(10,2) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idorder`),
  KEY `FK_orders_carts_idx` (`idcart`),
  KEY `FK_orders_users_idx` (`iduser`),
  KEY `fk_orders_ordersstatus_idx` (`idstatus`),
  CONSTRAINT `fk_orders_carts` FOREIGN KEY (`idcart`) REFERENCES `tb_carts` (`idcart`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_ordersstatus` FOREIGN KEY (`idstatus`) REFERENCES `tb_ordersstatus` (`idstatus`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_users` FOREIGN KEY (`iduser`) REFERENCES `tb_users` (`iduser`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_orders: ~0 rows (approximately)
/*!40000 ALTER TABLE `tb_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_orders` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_ordersstatus
CREATE TABLE IF NOT EXISTS `tb_ordersstatus` (
  `idstatus` int(11) NOT NULL AUTO_INCREMENT,
  `desstatus` varchar(32) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idstatus`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_ordersstatus: ~4 rows (approximately)
/*!40000 ALTER TABLE `tb_ordersstatus` DISABLE KEYS */;
INSERT INTO `tb_ordersstatus` (`idstatus`, `desstatus`, `dtregister`) VALUES
	(1, 'Em Aberto', '2017-03-13 03:00:00'),
	(2, 'Aguardando Pagamento', '2017-03-13 03:00:00'),
	(3, 'Pago', '2017-03-13 03:00:00'),
	(4, 'Entregue', '2017-03-13 03:00:00');
/*!40000 ALTER TABLE `tb_ordersstatus` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_persons
CREATE TABLE IF NOT EXISTS `tb_persons` (
  `idperson` int(11) NOT NULL AUTO_INCREMENT,
  `desperson` varchar(64) NOT NULL,
  `desemail` varchar(128) DEFAULT NULL,
  `nrphone` bigint(20) DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idperson`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_persons: ~5 rows (approximately)
/*!40000 ALTER TABLE `tb_persons` DISABLE KEYS */;
INSERT INTO `tb_persons` (`idperson`, `desperson`, `desemail`, `nrphone`, `dtregister`) VALUES
	(9, 'Jane P', 'jane@gmail.com', 21123456789, '2021-04-09 17:11:59'),
	(10, 'Administrado', 'admin.m@gmail.com', 112364587, '2021-04-10 10:00:07'),
	(11, 'James B', 'tiago.barbosap28@gmail.com', 2136548529, '2021-04-11 10:57:41'),
	(18, 'Mike House', 'mike@example.com', 123366548, '2021-05-01 10:07:41'),
	(22, 'Florencio Beatty', 'florencio40@ethereal.email', 0, '2021-05-03 14:56:01'),
	(23, 'Joanie Prohaska', 'joanie.prohaska47@ethereal.email', 0, '2021-05-10 16:51:36');
/*!40000 ALTER TABLE `tb_persons` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_products
CREATE TABLE IF NOT EXISTS `tb_products` (
  `idproduct` int(11) NOT NULL AUTO_INCREMENT,
  `desproduct` varchar(64) NOT NULL,
  `vlprice` decimal(10,2) NOT NULL,
  `vlwidth` decimal(10,2) NOT NULL,
  `vlheight` decimal(10,2) NOT NULL,
  `vllength` decimal(10,2) NOT NULL,
  `vlweight` decimal(10,2) NOT NULL,
  `desurl` varchar(128) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  `desimage` varchar(64) NOT NULL,
  PRIMARY KEY (`idproduct`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_products: ~9 rows (approximately)
/*!40000 ALTER TABLE `tb_products` DISABLE KEYS */;
INSERT INTO `tb_products` (`idproduct`, `desproduct`, `vlprice`, `vlwidth`, `vlheight`, `vllength`, `vlweight`, `desurl`, `dtregister`, `desimage`) VALUES
	(1, 'Galaxy A01 Core Preto 32GB', 629.10, 67.50, 141.70, 8.60, 150.00, 'smartphone-android-7.0', '2017-03-13 03:00:00', '1cea0d52d6'),
	(2, 'Smart TV LED 4K LG', 3925.99, 917.00, 596.00, 288.00, 8600.00, 'smarttv-led-4k', '2017-03-13 03:00:00', '31a94bea55'),
	(3, 'Notebook 14" 4GB 1TB', 1949.99, 345.00, 23.00, 30.00, 2000.00, 'notebook-14-4gb-1tb', '2017-03-13 03:00:00', '610355f384'),
	(5, 'iPad Pro Apple, Tela Liquid Retina 11”, 128 GB, Cinza Espacial, ', 7821.07, 1.00, 16.50, 24.96, 200.00, 'iPad-Pro-256gb', '2021-04-13 16:31:57', 'a3c53684f9'),
	(8, 'Smartphone Motorola Moto E6s 64G', 791.10, 7.30, 15.50, 0.85, 160.00, 'smartphone-motorola-moto-g5-plus', '2021-04-14 09:53:09', '87b9526835'),
	(9, 'Celular Motorola Moto Z2 Play 64gb Dual Xt1710', 980.15, 14.10, 0.90, 1.16, 0.13, 'smartphone-moto-z-play', '2021-04-14 09:53:09', '33a220c0e1'),
	(10, 'Celular Samsung Galaxy J5 J500 Dual Chip 16gb', 1299.00, 14.60, 7.10, 0.80, 0.16, 'smartphone-samsung-galaxy-j5', '2021-04-14 09:53:09', 'fb86ef9833'),
	(11, 'Smartphone Samsung Galaxy J7 Prime Duos Preto com 32GB', 1149.00, 7.50, 15.10, 0.81, 167.00, 'smartphone-samsung-galaxy-j7', '2021-04-14 09:53:09', '0984bcb1ef'),
	(12, 'Zenfone Shot Plus Black Asus, Tela 6,26", 4G, 128GB', 999.00, 159.00, 76.00, 0.80, 160.00, '', '2021-05-16 10:19:10', 'a685af877a');
/*!40000 ALTER TABLE `tb_products` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_productscategories
CREATE TABLE IF NOT EXISTS `tb_productscategories` (
  `idcategory` int(11) NOT NULL,
  `idproduct` int(11) NOT NULL,
  PRIMARY KEY (`idcategory`,`idproduct`),
  KEY `fk_productscategories_products_idx` (`idproduct`),
  CONSTRAINT `fk_productscategories_categories` FOREIGN KEY (`idcategory`) REFERENCES `tb_categories` (`idcategory`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_productscategories_products` FOREIGN KEY (`idproduct`) REFERENCES `tb_products` (`idproduct`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_productscategories: ~7 rows (approximately)
/*!40000 ALTER TABLE `tb_productscategories` DISABLE KEYS */;
INSERT INTO `tb_productscategories` (`idcategory`, `idproduct`) VALUES
	(2, 1),
	(2, 8),
	(2, 9),
	(2, 10),
	(2, 11),
	(2, 12),
	(30, 8),
	(35, 5);
/*!40000 ALTER TABLE `tb_productscategories` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_users
CREATE TABLE IF NOT EXISTS `tb_users` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `deslogin` varchar(64) NOT NULL,
  `despassword` varchar(256) NOT NULL,
  `inadmin` tinyint(4) NOT NULL DEFAULT 0,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`iduser`),
  KEY `FK_users_persons_idx` (`idperson`),
  CONSTRAINT `fk_users_persons` FOREIGN KEY (`idperson`) REFERENCES `tb_persons` (`idperson`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_users: ~6 rows (approximately)
/*!40000 ALTER TABLE `tb_users` DISABLE KEYS */;
INSERT INTO `tb_users` (`iduser`, `idperson`, `deslogin`, `despassword`, `inadmin`, `dtregister`) VALUES
	(9, 9, 'Jane', '$2y$12$01UacVVK1QD24mh0M14zfuiIYQvJ0WFLXq.QmX8srsd6r5ZY.se1q', 1, '2021-04-09 17:11:59'),
	(10, 10, 'Admin', '$2y$12$Qp49Og5D3nAU3occ1gcKA.VdSwd8n4QPtHcL8ZwyPjpJmAamBQJKi', 1, '2021-04-10 10:00:07'),
	(11, 11, 'James', '$2y$12$E3J17GwjejnQAHL6JYCO/.Rmfsi02tLP6izpbA0xZZH3aiy72ngue', 1, '2021-04-11 10:57:41'),
	(35, 18, 'Mike H', '$2y$10$P9m2W7.O5aa3OdqN1DpijePd07eBZy.ZLY3Q3WAFAXFi6lsQKLvTS', 1, '2021-05-01 10:07:42'),
	(37, 22, 'Florencio', '$2y$10$8lOv8ZRORIH05Qqhhx9EveqrCM9KQVTNaNgB/XBvSwKwBj3flPaym', 1, '2021-05-03 14:56:01'),
	(38, 23, 'joanie.prohaska47@ethereal.email', '$2y$10$3KSy/ad2rg/DUTYFg7zgMOc0NZ15b3CFT7ZFJlkaoBx3fErc.ZFaO', 0, '2021-05-10 16:51:36');
/*!40000 ALTER TABLE `tb_users` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_userslogs
CREATE TABLE IF NOT EXISTS `tb_userslogs` (
  `idlog` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `deslog` varchar(128) NOT NULL,
  `desip` varchar(45) NOT NULL,
  `desuseragent` varchar(128) NOT NULL,
  `dessessionid` varchar(64) NOT NULL,
  `desurl` varchar(128) NOT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idlog`),
  KEY `fk_userslogs_users_idx` (`iduser`),
  CONSTRAINT `fk_userslogs_users` FOREIGN KEY (`iduser`) REFERENCES `tb_users` (`iduser`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_userslogs: ~0 rows (approximately)
/*!40000 ALTER TABLE `tb_userslogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `tb_userslogs` ENABLE KEYS */;

-- Dumping structure for table db_ecommerce.tb_userspasswordsrecoveries
CREATE TABLE IF NOT EXISTS `tb_userspasswordsrecoveries` (
  `idrecovery` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `desip` varchar(45) NOT NULL,
  `dtrecovery` datetime DEFAULT NULL,
  `dtregister` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idrecovery`),
  KEY `fk_userspasswordsrecoveries_users_idx` (`iduser`),
  CONSTRAINT `fk_userspasswordsrecoveries_users` FOREIGN KEY (`iduser`) REFERENCES `tb_users` (`iduser`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- Dumping data for table db_ecommerce.tb_userspasswordsrecoveries: ~17 rows (approximately)
/*!40000 ALTER TABLE `tb_userspasswordsrecoveries` DISABLE KEYS */;
INSERT INTO `tb_userspasswordsrecoveries` (`idrecovery`, `iduser`, `desip`, `dtrecovery`, `dtregister`) VALUES
	(16, 11, '127.0.0.1', NULL, '2021-04-11 10:58:05'),
	(17, 11, '127.0.0.1', NULL, '2021-04-11 11:04:16'),
	(18, 11, '127.0.0.1', NULL, '2021-04-11 11:15:02'),
	(19, 11, '127.0.0.1', NULL, '2021-04-11 11:16:51'),
	(20, 11, '127.0.0.1', NULL, '2021-04-11 11:22:45'),
	(21, 11, '127.0.0.1', NULL, '2021-04-11 11:41:05'),
	(22, 11, '127.0.0.1', NULL, '2021-04-11 11:42:27'),
	(23, 11, '127.0.0.1', NULL, '2021-04-11 11:47:13'),
	(24, 11, '127.0.0.1', NULL, '2021-04-11 11:57:34'),
	(25, 11, '127.0.0.1', NULL, '2021-04-11 12:09:20'),
	(26, 11, '127.0.0.1', NULL, '2021-04-11 12:15:57'),
	(27, 11, '127.0.0.1', NULL, '2021-04-11 12:24:04'),
	(28, 11, '127.0.0.1', NULL, '2021-04-11 14:52:16'),
	(29, 11, '127.0.0.1', NULL, '2021-04-11 14:55:59'),
	(30, 11, '127.0.0.1', NULL, '2021-04-11 15:15:11'),
	(31, 11, '127.0.0.1', NULL, '2021-04-11 15:34:38'),
	(32, 11, '127.0.0.1', NULL, '2021-04-11 15:45:06');
/*!40000 ALTER TABLE `tb_userspasswordsrecoveries` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
