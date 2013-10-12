#-- categories

INSERT INTO `av_category` (`id_category`, `id_parent`, `active`, `date_add`, `date_upd`, `position`, `name`, `description`) VALUES 
(1, '', '1', '2013-09-26 00:00:00', '', '0', 'Simple Vitrage', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(2, '', '1', '2013-09-26 00:00:00', '', '0', 'Double Vitrage', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(3, '', '1', '2013-09-26 00:00:00', '', '0', 'Verre Sp�cifique', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(4, '', '1', '2013-09-26 00:00:00', '', '0', 'Miroir sur mesure', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(5, '', '1', '2013-09-26 00:00:00', '', '0', 'Paroi de douche', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(6, '', '1', '2013-09-26 00:00:00', '', '0', 'Verre decoratif', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.'),
(7, '', '1', '2013-09-26 00:00:00', '', '0', 'Accessoires', 'Itaque verae amicitiae difficillime reperiuntur in iis qui in honoribus reque publica versantur; ubi enim istum invenias qui honorem amici anteponat suo? Quid? Haec ut omittam, quam graves, quam difficiles plerisque videntur calamitatum societates! Ad quas non est facile inventu qui descendant. Quamquam Ennius recte.');


#-- produits
INSERT INTO `av_product` (`id_product`, `id_category`, `quantity`, `price`, `unit_price_ratio`, `reference`, `width`, `height`, `depth`, `weight`, `active`, `date_add`, `date_upd`, `name`, `description`, `description_short`) VALUES
(1,  1,'5', '1.5','1.5', 'REF 1', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 1', 'Produit de test', 'Description'),
(2,  2,'5', '2.5','2.5', 'REF 2', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 2', 'Produit de test', 'Description'),
(3,  3,'5', '3.5','3.5', 'REF 3', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 3', 'Produit de test', 'Description'),
(4,  4,'5', '4.5','4.5', 'REF 4', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 4', 'Produit de test', 'Description'),
(5,  5,'5', '5.5','5.5', 'REF 5', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 5', 'Produit de test', 'Description'),
(6,  6,'5', '6.5','6.5', 'REF 6', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 6', 'Produit de test', 'Description'),
(7,  7,'5', '7.5','7.5', 'REF 7', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 7', 'Produit de test', 'Description'),
(8,  1,'5', '8.5','8.5', 'REF 8', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 8', 'Produit de test', 'Description'),
(9,  2,'5', '9.5','9.5', 'REF 9', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 9', 'Produit de test', 'Description'),
(10, 3,'5', '10.5','10.5','REF 0', '0', '0', '0', '0.000000', '1', '2013-09-26 00:00:00', '2013-09-26 00:00:00', 'Produit 0', 'Produit de test', 'Description');

update av_product set weight = round(rand()*10), `min_width`= 100, `min_height`= 100, `max_width`= 1000, `max_height`= 1000, `max_surface`= 1000;
    
#-- produits carac
INSERT INTO `av_product_caract` (`id_product_caract`, `id_product`, `caract_name`, `caract_value`) VALUES
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
(NULL, '4', 'Delais de livraison', ' 4-5 semaines');


INSERT INTO `av_product_attribute` (`id_product_attribute`, `id_product`, `name`, `price`, `weight`, `unit_price_impact`, `default_on`) VALUES 
(NULL, '1', 'RAL 1000', '11', '11', '0.00', '0'),
(NULL, '1', 'RAL 1001', '12', '12', '0.00', '0'),
(NULL, '1', 'RAL 1002', '13', '13', '0.00', '0'),
(NULL, '1', 'RAL 1003', '14', '14', '0.00', '0'),
(NULL, '1', 'RAL 1004', '15', '15', '0.00', '0'),
(NULL, '1', 'RAL 1005', '16', '16', '0.00', '0'),
(NULL, '2', 'RAL 1000', '17', '17', '0.00', '0'),
(NULL, '2', 'RAL 1001', '18', '18', '0.00', '0'),
(NULL, '2', 'RAL 1002', '19', '19', '0.00', '0'),
(NULL, '2', 'RAL 1003', '11', '11', '0.00', '0'),
(NULL, '2', 'RAL 1004', '12', '12', '0.00', '0'),
(NULL, '2', 'RAL 1005', '13', '13', '0.00', '0');


INSERT INTO `av_range_weight` (`id_range_weight`, `delimiter1`, `delimiter2`, `delivery_ratio`) VALUES
(2, '0.000000', '1.000000', '1.000000'),
(3, '1.000000', '10.000000', '5.000000'),
(4, '10.000000', '50.000000', '15.000000');


INSERT INTO `av_product_images` (`id_image`, `id_product`, `cover`, `filename`) VALUES
(1, 1, 1, 'verre-extra-clair-laque-securit.jpg'),
(2, 1, 0, 'verre-extra-clair-laque-securit1.jpg'),
(3, 1, 0, 'verre-extra-clair-laque-securit2.jpg');



INSERT INTO `av_truck` (`id_truck`, `imma`, `name`, `date_add`, `is_actif`, `capacity`, `status`) VALUES
(1, '12-APA-97', 'Mon truck', '2013-10-08 22:00:00', 1, 10, 0),
(2, '45PAF-75', 'Mon truck 2', '2013-10-09 11:03:14', 1, 20, 0),
(3, '65-PIF-45', 'Long courrier', '2013-10-10 13:58:30', 1, 30, 0),
(4, '23-MAR-80', 'Regional', '2013-10-10 13:59:03', 1, 15, 0);


update av_order_detail set 
product_width = round(rand()*4000,2),
product_height = round(rand()*4000,2),
product_depth = round(rand()*200,2),
product_weight = round(rand()*10,2)