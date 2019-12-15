<?php

namespace App\Tests;

use App\Entity\Contact;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    public function testThatWeCanGetFullname()
    {
        $contact = new Contact();

        $contact->setFullName('Jonas Jonaitis');

        $this->assertEquals($contact->getFullName(), 'Jonas Jonaitis');
        
    }

    public function testThatWeCanGetPhonuNumber()
    {
        $contact = new Contact();

        $contact->setPhoneNumber('+37102135551');

        $this->assertEquals($contact->getPhoneNumber(), '+37102135551');
        
    }
}
