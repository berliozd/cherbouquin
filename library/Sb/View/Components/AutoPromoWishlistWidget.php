<?php

namespace Sb\View\Components;

use Sb\Entity\Urls;
use Sb\Helpers\HTTPHelper;
use Sb\Templates\Template;

/**
 *
 * @author Didier
 */
class AutoPromoWishlistWidget extends \Sb\View\AbstractView {

    function __construct() {
        parent::__construct();
    }

    public function get() {

        $baseTpl = "components/autoPromoWishlistWidget";
        $tpl = new Template($baseTpl);

        $autoPromoWishListLink = HTTPHelper::Link(Urls::USER_FRIENDS_WISHLIST);
        $autoPromoWishListImage = $this->getContext()->getBaseUrl() . "Resources/images/homepage/auto-promo-wishlist.png";
        $autoPromoWishListTitle = __("Offrez un livre à vos amis", "s1b");

        $tpl->setVariables(array("autoPromoWishListTitle" => $autoPromoWishListTitle,
            "autoPromoWishListLink" => $autoPromoWishListLink,
            "autoPromoWishListImage" => $autoPromoWishListImage));
        return $tpl->output();
    }

}