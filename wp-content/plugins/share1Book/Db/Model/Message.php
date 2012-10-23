<?php

namespace Sb\Db\Model;

/** @Entity @Table(name="s1b_messages") */
class Message implements \Sb\Db\Model\Model {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="messages_sent")
     * @JoinColumn(name="sender_id", referencedColumnName="id")
     */
    protected $sender;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="messages_received")
     * @JoinColumn(name="recipient_id", referencedColumnName="id")
     */
    protected $recipient;

    /** @Column(type="datetime") */
    protected $date;

    /** @Column(type="string", length=250) */
    protected $title;

    /** @Column(type="string", length=4000) */
    protected $message;

    /** @Column(type="boolean") */
    protected $is_read = 0;

    /** @Column(type="boolean") */
    protected $is_facebook_post = 0;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getSender() {
        return $this->sender;
    }

    public function setSender(\Sb\Db\Model\User $sender) {
        $this->sender = $sender;
        $sender->addMessagesSent($this);
    }

    public function getRecipient() {
        return $this->recipient;
    }

    public function setRecipient(\Sb\Db\Model\User $recipient) {
        $this->recipient = $recipient;
        $recipient->addMessagesReceived($this);
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function getIs_read() {
        return $this->is_read;
    }

    public function setIs_read($is_read) {
        $this->is_read = $is_read;
    }

    public function getIs_facebook_post() {
        return $this->is_facebook_post;
    }

    public function setIs_facebook_post($is_facebook_post) {
        $this->is_facebook_post = $is_facebook_post;
    }


    public function IsValid() {
        return true;
    }

    // TODO : delete
    protected $sender_id;
    protected $recipient_id;
    public function getSender_id() {
        return $this->sender_id;
    }

    public function setSender_id($sender_id) {
        $this->sender_id = $sender_id;
    }

    public function getRecipient_id() {
        return $this->recipient_id;
    }

    public function setRecipient_id($recipient_id) {
        $this->recipient_id = $recipient_id;
    }

}