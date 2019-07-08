<?php
/**
 * @package   System - ZOO Event Slack
 * @author    Ray Lawlor - ZOOModsPlus https://www.zoomodsplus.com
 * @copyright Copyright (C) Ray Lawlor - ZOOModsPlus https://www.zoomodsplus.com
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemZooeventslack extends JPlugin
{

    public function onAfterInitialise()
    {

        if (!JComponentHelper::getComponent('com_zoo', true)->enabled) {
            return;
        }

        jimport('joomla.filesystem.file');
        if (!JFile::exists(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php') || !JComponentHelper::getComponent('com_zoo', true)->enabled) {
            return;
        }
        require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

        if (!class_exists('App')) {
            return;
        }

        $zoo = App::getInstance('zoo');

        $zoo->event->dispatcher->connect('item:saved', array($this, 'sendMessage'));

    }


    public function sendMessage($event)
    {
        $item = $event->getSubject();
        $new = $event['new'];


        if ($new) {

            $item_link = JURI::root() . 'administrator/index.php?' . http_build_query(array(
                    'option' => "com_zoo",
                    'controller' => 'item',
                    'changeapp' => $item->application_id,
                    'task' => 'edit',
                    'cid[]' => $item->id,
                ), '', '&');

            $message = '{
                "attachments": [{
                    "color": "good",
                    "title": "' . $item->name . '",
                    "title_link": "' . $item_link . '",
                    "text": "A new item named: ' . $item->name . ' has been added to your ZOO app.",
                    "ts": ' . time() . '
                }]
            }';


            $c = curl_init(USE YOUR SLACK WBHOOK);
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, $message);
            curl_exec($c);
            curl_close($c);


        }

    }

}
