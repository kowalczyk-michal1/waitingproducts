<?php

/**
 *
 * @author     Ireneusz Kierkowski <ircykk@gmail.pl>
 * @copyright  (c)2013 addonsPresta.com
 * @package    Allegro PrestaShop Module   
 * @doc        http://addonspresta.com/content/10-allegro-integration
 * 
 */
class WPModel extends ObjectModel {


        static public function getEmail($email) {
                return Db::getInstance()->GetRow("
		SELECT *
		FROM `" . _DB_PREFIX_ . "waiting_products`
		WHERE `email` = '".$email."'");

        }
		
		static public function addEmail($email, $pid, $position) {
                return Db::getInstance()->Execute("
		INSERT INTO `" . _DB_PREFIX_ . "waiting_products` 
		set `email`='".$email."', 
		`position`='".$position."', 
		`product_id`='".$pid."'");

        }
		
		static public function getMaxPos($pid) {
                return Db::getInstance()->GetRow("
				select `position` from `" . _DB_PREFIX_ . "waiting_products` 
				where `product_id`='".$pid."' order by `position` DESC");

        }
		


}
