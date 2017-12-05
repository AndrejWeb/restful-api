/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


CREATE DATABASE IF NOT EXISTS `restful_api` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `restful_api`;

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method` varchar(20) DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT NULL,
  `data_submitted` text,
  `data_returned` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `todos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `completed` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=351 DEFAULT CHARSET=latin1;

DELETE FROM `todos`;
/*!40000 ALTER TABLE `todos` DISABLE KEYS */;
INSERT INTO `todos` (`id`, `userId`, `title`, `completed`) VALUES
	(1, 1, 'delectus aut autem', 0),
	(2, 1, 'quis ut nam facilis et officia qui', 0),
	(3, 1, 'fugiat veniam minus', 0),
	(4, 1, 'et porro tempora', 1),
	(5, 1, 'laboriosam mollitia et enim quasi adipisci quia provident illum', 0),
	(6, 1, 'qui ullam ratione quibusdam voluptatem quia omnis', 0),
	(7, 1, 'illo expedita consequatur quia in', 0),
	(11, 1, 'vero rerum temporibus dolor', 1),
	(12, 1, 'ipsa repellendus fugit nisi', 1),
	(13, 1, 'et doloremque nulla', 0),
	(14, 1, 'repellendus sunt dolores architecto voluptatum', 1),
	(15, 1, 'ab voluptatum amet voluptas', 1),
	(16, 1, 'accusamus eos facilis sint et aut voluptatem', 1),
	(17, 1, 'quo laboriosam deleniti aut qui', 1),
	(18, 1, 'dolorum est consequatur ea mollitia in culpa', 0),
	(20, 1, 'ullam nobis libero sapiente ad optio sint', 1),
	(21, 2, 'suscipit repellat esse quibusdam voluptatem incidunt', 0),
	(22, 2, 'distinctio vitae autem nihil ut molestias quo', 1),
	(23, 2, 'et itaque necessitatibus maxime molestiae qui quas velit', 0),
	(24, 2, 'adipisci non ad dicta qui amet quaerat doloribus ea', 0),
	(25, 2, 'voluptas quo tenetur perspiciatis explicabo natus', 1),
	(30, 2, 'nemo perspiciatis repellat ut dolor libero commodi blanditiis omnis', 1),
	(31, 2, 'repudiandae totam in est sint facere fuga', 0),
	(33, 2, 'sint sit aut vero', 0),
	(34, 2, 'porro aut necessitatibus eaque distinctio', 0),
	(40, 2, 'totam atque quo nesciunt', 1),
	(41, 3, 'aliquid amet impedit consequatur aspernatur placeat eaque fugiat suscipit', 0),
	(42, 3, 'rerum perferendis error quia ut eveniet', 0),
	(46, 3, 'vel voluptatem repellat nihil placeat corporis', 0),
	(47, 3, 'nam qui rerum fugiat accusamus', 0),
	(48, 3, 'sit reprehenderit omnis quia', 0),
	(49, 3, 'ut necessitatibus aut maiores debitis officia blanditiis velit et', 0),
	(58, 3, 'est dicta totam qui explicabo doloribus qui dignissimos', 0),
	(59, 3, 'perspiciatis velit id laborum placeat iusto et aliquam odio', 0),
	(60, 3, 'et sequi qui architecto ut adipisci', 1),
	(61, 4, 'odit optio omnis qui sunt', 1),
	(62, 4, 'et placeat et tempore aspernatur sint numquam', 0),
	(63, 4, 'doloremque aut dolores quidem fuga qui nulla', 1),
	(64, 4, 'voluptas consequatur qui ut quia magnam nemo esse', 0),
	(81, 5, 'suscipit qui totam', 1),
	(82, 5, 'voluptates eum voluptas et dicta', 0),
	(87, 5, 'laudantium quae eligendi consequatur quia et vero autem', 1),
	(88, 5, 'vitae aut excepturi laboriosam sint aliquam et et accusantium', 0),
	(89, 5, 'sequi ut omnis et', 1),
	(90, 5, 'molestiae nisi accusantium tenetur dolorem et', 1),
	(91, 5, 'nulla quis consequatur saepe qui id expedita', 1),
	(93, 5, 'odio iure consequatur molestiae quibusdam necessitatibus quia sint', 1),
	(94, 5, 'facilis modi saepe mollitia', 0),
	(95, 5, 'vel nihil et molestiae iusto assumenda nemo quo ut', 1),
	(96, 5, 'nobis suscipit ducimus enim asperiores voluptas', 0);
/*!40000 ALTER TABLE `todos` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
