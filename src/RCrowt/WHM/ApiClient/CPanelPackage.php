<?php
/**
 * Created by PhpStorm.
 * User: Richard
 * Date: 02/08/2015
 * Time: 18:53
 */

namespace RCrowt\WHM\ApiClient;


class CPanelPackage extends CPanelObject
{

    /**
     * Get the package name.
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * Get the Bandwidth Limit in Megabytes.
     * @return float|null
     */
    public function getBandwidthLimit()
    {
        $out = $this->get('BWLIMIT');
        if ($out == 'unlimited') return null;
        else return (float)$out;
    }

    /**
     * Get the Disk/WebSpace limit in Megabytes.
     * @return mixed|null
     */
    public function getDiskLimit()
    {
        $out = $this->get('QUOTA');
        if ($out == 'unlimited') return null;
        else return (float)$out;
    }
}