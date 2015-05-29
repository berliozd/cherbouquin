<?php

namespace Sb\Db\Service;

/**
 * Description of InvitationSvc
 * @author Didier
 */
class InvitationSvc extends AbstractService {

    private static $instance;

    /**
     *
     * @return \Sb\Db\Service\InvitationSvc
     */
    public static function getInstance() {

        if (!self::$instance)
            self::$instance = new \Sb\Db\Service\InvitationSvc();
        return self::$instance;
    }

    protected function __construct() {

        parent::__construct(\Sb\Db\Dao\InvitationDao::getInstance(), "Invitation");
    }

    public function setInvitationsAccepted($email) {

        try {
            // Testing if the user registering match invitations and set them to validted and accepted if they exist
            $invitations = \Sb\Db\Dao\InvitationDao::getInstance()->getListByGuestEmail($email);
            if ($invitations && count($invitations) > 0) {
                foreach ($invitations as $invitation) {
                    if (!$invitation->getIs_accepted()) {
                        $invitation->setIs_accepted(true);
                        $invitation->setIs_validated(true);
                        $invitation->setLast_modification_date(new \DateTime());
                        \Sb\Trace\Trace::addItem("Updating invitation for " . $email);
                        \Sb\Db\Dao\InvitationDao::getInstance()->update($invitation);
                    }
                }
            }
        } catch (\Exception $exc) {
            $this->logException(get_class(), __FUNCTION__, $exc);
        }
    }

}