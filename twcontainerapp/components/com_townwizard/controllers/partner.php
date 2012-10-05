<?php
/**
 * @version		1.0.0 townwizard_container $
 * @package		townwizard_container
 * @copyright	Copyright © 2012 - All rights reserved.
 * @license		GNU/GPL
 * @author		MLS
 * @author mail	nobody@nobody.com
 *
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class TownwizardControllerPartner extends JController
{
    public function partner()
    {
        $model = $this->getModel('Partner');

        $dbQuery = $model->getQuery();
        $dbQuery['conditions'][] = 'p.published = 1';
        $model->setQuery($dbQuery);

        $partner = $model->getOne();

        $response = array('status' => 0, 'error' => '', 'data' => array());
        if ($partner->id)
        {
            $category = $this->getModel('PartnerCategory');
            $category->setId($partner->partner_category_id);
            $category = $category->getOne();

            $locations = $this->getModel('PartnerLocation');
            $dbQuery = $locations->getQuery();
            $dbQuery['conditions'][] = 'pl.partner_id = ' . $partner->id;

            $db = & JFactory::getDBO();
            $db->setQuery($locations->buildQuery($dbQuery));
            $locations = $db->loadAssocList();

            $partner->image = $partner->image ? '/media/com_townwizard/images/partners/' . $partner->image : '';
            $partner->facebook_app_id = $partner->facebook_app_id ? $partner->facebook_app_id : '346995245338206';

            $response['status'] = 1;
            $response['data'] = array_merge(
                                        (array) $partner,
                                        array(
                                            'category' => $category->title,
                                            'locations' => $locations
                                        ));
        }
        else
        {
            $response['status'] = 0;
            $response['error'] = 'Partner with specified id is not found';
        }

        header('Content-type: application/json');
        echo json_encode($response);
        exit();
    }

    public function search()
    {
        $model = $this->getModel('PartnerLocation');
        $dbQuery = $model->getQuery();

        $db			=& JFactory::getDBO();
        $filter		= null;

        $query          = JRequest::getVar( 'q', '', '', 'string' );
        $lat			= JRequest::getVar( 'lat', 0, '', 'float' );
        $lon			= JRequest::getVar( 'lon', 0, '', 'float' );
        $offset			= JRequest::getVar( 'offset', 0, '', 'int' );
        $limit = 10;

        $dbQuery['conditions'][] = 'p.published = 1';

        $partner = $this->getModel('Partner');
        $pquery = $partner->getQuery();
        $dbQuery['fields'] = $pquery['fields'];
        $dbQuery['joins'][] = array_pop($pquery['joins']);
        $dbQuery['order'] = 'p.ordering';

        if (strlen(trim($query)) > 0)
        {
            $query = substr(trim($_GET['q']), 0, 64);
            $query = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $query);
            $query = trim(preg_replace("/\s(\S{1,2})\s/", " ", preg_replace("/ +/", "  "," $query ")));
            $query = preg_replace("/ +/", " ", $query);
            $query = strtolower($query);
            $query = $db->Quote( '%'.$db->getEscaped( $query, true ).'%', false );
            
            if (mb_strlen($query) >= 3)
            {
                $dbQuery['conditions'][] = '(LOWER(pl.city ) LIKE ' . $query
                                         .' OR LOWER(pl.zip) LIKE ' . $query
                                         .' OR LOWER(p.name) LIKE ' . $query . ')';
            }
        }

        if ($lat && $lon)
        {
            //$rad = 80.4672; //Search radius - 50 miles in km
            $R = 6371;  // earth's radius, km

            // first-cut bounding box (in degrees)
            //$maxLat = $lat + rad2deg($rad/$R);
            //$minLat = $lat - rad2deg($rad/$R);
            // compensate for degrees longitude getting smaller with increasing latitude
            //$maxLon = $lon + rad2deg($rad/$R/cos(deg2rad($lat)));
            //$minLon = $lon - rad2deg($rad/$R/cos(deg2rad($lat)));

            // convert origin of filter circle to radians
            $lat = deg2rad($lat);
            $lon = deg2rad($lon);

            //$dbQuery['conditions'][] = "(pl.latitude > $minLat AND pl.latitude < $maxLat
            //                            AND pl.longitude > $minLon AND pl.longitude < $maxLon)";

            $dbQuery['fields'] = array_merge($pquery['fields'], array(
                                         'pl.street',
                                         'pl.city',
                                         'pl.state',
                                         'pl.country',
                                         'pl.zip',
                                         'pl.latitude',
                                         'pl.longitude',
                                         'pl.map_zoom'));

            $searchQuery = $model->buildQuery($dbQuery);

            $cl1 = cos($lat);
            $sl1 = sin($lat);

            $pow1 = "pow(cos(radians(latitude)) * sin(radians(longitude) - $lon), 2)";
            $pow2 = "pow($cl1 * sin(radians(latitude)) - $sl1 * cos(radians(latitude)) * cos(radians(longitude) - $lon), 2)";
            $y = "sqrt($pow1 + $pow2)";
            $x = "$sl1 * sin(radians(latitude)) + $cl1 * cos(radians(latitude)) * cos(radians(longitude) - $lon)";
            $ad = "atan2($y, $x) * $R";

            $pfields = implode(', ', array_keys($partner->getTable()->getProperties()));

            //$distanceDbQuery = $dbQuery;
            $dbQuery['fields'] = array($pfields, 'category', "$ad AS distance");
            $dbQuery['table'] = "($searchQuery)";
            $dbQuery['joins'] = array();
            $dbQuery['conditions'] = array();
            //$dbQuery['conditions'] = array("$ad < $rad");
            $dbQuery['order'] = 'distance';
            $dbQuery['group'] = 'id';
        }

        $partners = array();
        if (count($dbQuery['conditions']) || $lat && $lon)
        {
            $countDbQuery = $dbQuery;
            $countDbQuery['fields'] = array('COUNT(*)');
            $countDbQuery['order'] = '';
            $countDbQuery['group'] = '';

            $sql = $model->buildQuery($countDbQuery);
            
            $db->setQuery($sql);
            $total = $db->loadResult();
            
            if ($total)
            {
                $dbQuery['limit'] = $limit;
                $dbQuery['offset'] = $offset;

                $sql = $model->buildQuery($dbQuery);
                $db->setQuery($sql);

                $partners = $db->loadAssocList();
            }
        }

        $response = array('status' => 0, 'data' => array(), 'error' => '');
        if ($partners)
        {
            $partnersIds = array();
            $partnersList = array();
            foreach ($partners as $partner)
            {
                $partner['image'] = $partner['image'] ? '/media/com_townwizard/images/partners/' . $partner['image'] : '';
                $partner['facebook_app_id'] = $partner['facebook_app_id'] ? $partner['facebook_app_id'] : '346995245338206';
                $partnersList[$partner['id']] = $partner;
                $partnersIds[] = $partner['id'];
            }

            $locations = $this->getModel('PartnerLocation');
            $dbQuery = $locations->getQuery();
            $dbQuery['conditions'][] = 'pl.partner_id IN(' . implode(',', $partnersIds) . ')';

            $db = & JFactory::getDBO();

            $db->setQuery($locations->buildQuery($dbQuery));
            $locations = $db->loadAssocList();

            foreach ($locations as $location)
            {
                $partnersList[$location['partner_id']]['locations'][] = $location;
            }

            $response['status'] = 1;
            $response['data'] = array_values($partnersList);
            $response['meta'] = array(
                'total' => $total,
                'offset' => $offset,
                'limit' => $limit
            );

            if (count($locations) + $offset < $total)
            {
                $response['meta']['next_offset'] = count($locations) + $offset;
            }
        }
        else
        {
            $response['error'] = "Sorry, but it looks like we don't have a Townwizard in your area";
        }

        header('Content-type: application/json');
        echo json_encode($response);
        exit();
    }
}
?>
