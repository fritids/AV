#-- categories

INSERT INTO `allovitres`.`av_category` (`id_category`, `id_parent`, `active`, `date_add`, `date_upd`, `position`, `name`, `description`) VALUES 
(1, '', '1', '2013-09-26 00:00:00', '', '0', 'Simple Vitrage', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(2, '', '1', '2013-09-26 00:00:00', '', '0', 'Double Vitrage', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(3, '', '1', '2013-09-26 00:00:00', '', '0', 'Verre Spécifique', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(4, '', '1', '2013-09-26 00:00:00', '', '0', 'Miroir sur mesure', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(5, '', '1', '2013-09-26 00:00:00', '', '0', 'Paroi de douche', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(6, '', '1', '2013-09-26 00:00:00', '', '0', 'Verre decoratif', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(7, '', '1', '2013-09-26 00:00:00', '', '0', 'Accessoires', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.');


#-- produits
INSERT INTO `allovitres`.`av_product` (`id_product`, `id_category`, `quantity`, `price`, `unit_price_ratio`, `reference`, `width`, `height`, `depth`, `weight`, `active`, `date_add`, `date_upd`, `name`, `description`, `description_short`) VALUES
(1,  1,'5', '0', '1.5', 'REF 1', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 1', 'Produit de test', 'Description'),
(2,  2,'5', '0', '2.5', 'REF 2', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 2', 'Produit de test', 'Description'),
(3,  3,'5', '0', '3.5', 'REF 3', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 3', 'Produit de test', 'Description'),
(4,  4,'5', '0', '4.5', 'REF 4', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 4', 'Produit de test', 'Description'),
(5,  5,'5', '0', '5.5', 'REF 5', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 5', 'Produit de test', 'Description'),
(6,  6,'5', '0', '6.5', 'REF 6', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 6', 'Produit de test', 'Description'),
(7,  7,'5', '0', '7.5', 'REF 7', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 7', 'Produit de test', 'Description'),
(8,  1,'5', '0', '8.5', 'REF 8', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 8', 'Produit de test', 'Description'),
(9,  2,'5', '0', '9.5', 'REF 9', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 9', 'Produit de test', 'Description'),
(10, 3,'5', '0', '10.5','REF 0', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 0', 'Produit de test', 'Description');
    
#-- produits carac
INSERT INTO `allovitres`.`av_product_caract` (`id_product_caract`, `id_product`, `caract_name`, `caract_value`) VALUES
(NULL, '1', 'Transparence', 'Verre d''eau'),
(NULL, '1', 'Assemblage', '3 verres par un film ...'),
(NULL, '1', 'Delais de livraison', ' 4-5 semaines'),
(NULL, '2', 'Transparence', 'Verre d''eau'),
(NULL, '2', 'Assemblage', '3 verres par un film ...'),
(NULL, '2', 'Delais de livraison', ' 4-5 semaines'),
(NULL, '3', 'Transparence', 'Verre d''eau'),
(NULL, '3', 'Assemblage', '3 verres par un film ...'),
(NULL, '3', 'Delais de livraison', ' 4-5 semaines'),
(NULL, '4', 'Transparence', 'Verre d''eau'),
(NULL, '4', 'Assemblage', '3 verres par un film ...'),
(NULL, '4', 'Delais de livraison', ' 4-5 semaines'),
