<?php
namespace RCrowt\WHM\ApiClient;

use RCrowt\WHM\ApiClient;

/**
 * CPanel Account
 * @package RCrowt\WHM\ApiClient
 *
 * @see https://documentation.cpanel.net/display/SDK/WHM+API+1+Functions+-+listaccts
 */
class CPanelAccount extends CPanelObject
{

    /**
     * Get the disk limit in Megabytes
     * @return float|null
     */
    public function getDiskLimit()
    {
        if ($this->data->disklimit == 'unlimited') return null;
        else return (float)$this->get('disklimit');
    }

    /**
     * Get the current disk usage in Megabytes.
     * @return float|null
     */
    public function getDiskUsed()
    {
        return (float)$this->get('diskused');
    }

    /**
     * Get the primary domain name.
     * @return null|string
     */
    public function getDomain()
    {
        return $this->get('domain');
    }

    /**
     * Get the account owners email address.
     * @return string|null
     */
    public function getEmail()
    {
        return $this->get('email');
    }

    public function getPackage()
    {
        return $this->client->getPackage($this->getPackageName());
    }

    /**
     * Get the name of the plan the account is on.
     * @return null|string
     */
    public function getPackageName()
    {
        return $this->get('plan');
    }

    /**
     * Get the account create date.
     * @return \DateTime
     */
    public function getStartDate()
    {
        return new \DateTime($this->get('startdate'));
    }

    /**
     * Get the Username.
     * @return null|string
     */
    public function getUser()
    {
        return $this->get('user');
    }

    /**
     * Is this account suspended?
     * @return bool
     */
    public function isSuspended()
    {
        return (boolean)$this->get('suspended', false);
    }

}