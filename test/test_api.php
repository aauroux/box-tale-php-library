<?php
use \Emc\Carrier;
use \Emc\ContentCategory;
use \Emc\ListPoints;
use \Emc\Country;
use \Emc\ParcelPoint;
use \Emc\OrderStatus;
use \Emc\Service;
use \Emc\User;
use \Emc\Quotation;

error_reporting(E_ERROR | E_WARNING | E_PARSE);


require_once('../config/autoload.php');
require_once(EMC_PARENT_DIR.'layout/header.php');

ob_start();
//header('Content-Type: text/html; charset=utf-8');

/* Possible state of testings */
define('UNKNOWN', 0);// Not tested
define('OK', 1);// Classe 100% working
define('WARNING', 2);// Some not criticals errors
define('ERROR', 3);// At least one critical error

/* Classes to test
 * To add a new class to the test, add it in this array, and create the corresponding test function
 */
$_CLASSES = array(
    'Carrier',
    'ContentCategory',
    'ListPoints',
    'Country',
    'ParcelPoint',
    'OrderStatus',
    'Service',
    'User',
    'Quotation'
);

/* Test functions corresponding to their classes, each one must return an array of this configuration :
 * $result                          => array(
 *  ['duration']                    => #int(sec)
 *  ['reception']               => #state
 *  ['reception_info'][x] => #text
 *  ['parsing']                     => #state
 *  ['parsing_info'][x]         => #text
 *  ['additionals'][x]          => array(
 *      ['name']                            => #text
 *      ['info'][x]                     => #text
 *      ['state']                       => #state
 *      )
 *  )
 **/

function parse_message($state, $info)
{
    $message = '';
    $message_info = '';
    if (count($info) == 0) {
        $message_info .='No available informations';
    } else {
        $message_info .= '<ul class="info">';
        foreach ($info as $info_ent) {
            $message_info .= '<li>'.$info_ent.'</li>';
        }
        $message_info .= '</ul>';
    }

    switch ($state) {
        case UNKNOWN:
            $badge = 'default';
            $msg = 'Not tested';
            break;
        case OK:
            $badge = 'success';
            $msg = 'success';
            break;
        case WARNING:
            $badge = 'warning';
            $msg = 'Warning';
            break;
        case ERROR:
            $badge = 'danger';
            $msg = 'Error';
            break;
    }

    $message .= '<span class="label label-'.$badge.'" data-container="body" data-toggle="popover" data-placement="left" data-content="'.htmlspecialchars($message_info).'">'. $msg .'</span>';

    return $message;
}

function default_value()
{
    $result = array(
        'duration' => 0,
        'reception' => UNKNOWN,
        'reception_info' => array(),
        'additionals' => array(
        )
    );
    return $result;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function test_Carrier()
{
    $result = default_value();
    $start = microtime_float();

    /* Initialisation */
    $env = new Carrier();
    $env->getCarriers();

    /* Reception test */
    if ($env->curl_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while sending the query';
    } elseif ($env->resp_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Invalid query : ' . $userData["api_key"];
        foreach ($env->resp_errors_list as $message) {
            $result['reception_info'][count($result['reception_info'])] = $message['message'];
        }
    } else {
        $result['reception'] = max($result['reception'], OK);
        $result['reception_info'][count($result['reception_info'])] = 'Reception time : ' . (microtime_float() - $start) . 's';
    }

    /* Additionals test */

    /* Test for the result structure */
    $result['additionals'][0]['name'] = 'Structure';
    $result['additionals'][0]['state'] = OK;
    if (count($env->carriers) == 0) {
        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$carriers array is empty';
    } else {
        foreach ($env->carriers as $code => $carrier) {
            if (!isset($carrier['label'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['code'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['logo'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"logo" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['logo_modules'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"logo_modules" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['description'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"description" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['address'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"address" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['url'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"url" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['tracking'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"tracking" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['tel'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"tel" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['cgv'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"cgv" not defined in $carriers["'.$code.'"] array';
            }
        }
    }

    $result['duration'] = microtime_float() - $start;
    return $result;
}
function test_ContentCategory()
{
    $result = default_value();
    $start = microtime_float();

    /* Initialisation */
    $env = new ContentCategory();

    // Gather categories
    $env->getCategories();
    // Gather contents
    $env->getContents();

    /* Reception test */
    if ($env->curl_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while sending the query';
    } elseif ($env->resp_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Invalid query : ' . $userData["api_key"];
        foreach ($env->resp_errors_list as $message) {
            $result['reception_info'][count($result['reception_info'])] = $message['message'];
        }
    } else {
        $result['reception'] = max($result['reception'], OK);
        $result['reception_info'][count($result['reception_info'])] = 'Reception time : ' . (microtime_float() - $start). 's';
        ;
    }

    /* Additionals test */

    /* Test for the result structure */
    $result['additionals'][0]['name'] = 'Structure';
    $result['additionals'][0]['state'] = OK;
    if (count($env->categories) == 0) {
        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$categories array is empty';
    } else {
        foreach ($env->categories as $code => $category) {
            if (!isset($category['label'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $categories["'.$code.'"] array';
            }
            if (!isset($category['code'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $categories["'.$code.'"] array';
            }
        }
    }
    if (count($env->categories) == 0) {
        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$contents array is empty';
    } else {
        foreach ($env->contents as $category => $content) {
            foreach ($content as $x => $line) {
                if (!isset($env->contents[$category][$x]['label'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $contents["'.$category.'"]['.$x.'] array';
                }
                if (!isset($env->contents[$category][$x]['code'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $contents["'.$category.'"]['.$x.'] array';
                }
                if (!isset($env->contents[$category][$x]['category'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"category" not defined in $contents["'.$category.'"]['.$x.'] array';
                }
            }
        }
    }

    $result['duration'] = microtime_float() - $start;
    return $result;
}
function test_ListPoints()
{
    $result = default_value();
    $start = microtime_float();

    /* Initialisation */
    $env = new ListPoints();
    $params = array('pays' => 'FR', 'cp' => '75011', 'ville' => 'PARIS');
    $env->getListPoints(array('SOGP_RelaisColis', 'MONR_CpourToi'), $params);

    /* Reception test */
    if ($env->curl_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while sending the query';
    } elseif ($env->resp_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Invalid query : ' . $userData["api_key"];
        foreach ($env->resp_errors_list as $message) {
            $result['reception_info'][count($result['reception_info'])] = $message['message'];
        }
    } else {
        $result['reception'] = max($result['reception'], OK);
        $result['reception_info'][count($result['reception_info'])] = 'Reception time : ' . (microtime_float() - $start). 's';
        ;
    }

    /* Additionals test */

    /* Test for the result structure */
    $result['additionals'][0]['name'] = 'Structure';
    $result['additionals'][0]['state'] = OK;
    if (count($env->list_points) == 0) {
        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$list_points array is empty';
    } else {
        foreach ($env->list_points as $i => $carrier) {
            foreach ($carrier['points'] as $x => $content) {
                if (!isset($content['code'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $list_points[' .$i. ']['.$x.'] array';
                }
                if (!isset($content['name'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"name" not defined in $list_points[' .$i. ']['.$x.'] array';
                }
                if (!isset($content['address'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"address" not defined in $list_points[' .$i. ']['.$x.'] array';
                }
                if (!isset($content['city'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"city" not defined in $list_points[' .$i. ']['.$x.'] array';
                }
                if (!isset($content['zipcode'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"zipcode" not defined in $list_points[' .$i. ']['.$x.'] array';
                }
                if (!isset($content['country'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"country" not defined in $list_points[' .$i. ']['.$x.'] array';
                }
                if (!isset($content['description'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"description" not defined in $list_points[' .$i. ']['.$x.'] array';
                }
                if (!isset($content['schedule'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"schedule" not defined in $list_points[' .$i. ']['.$x.'] array';
                } elseif (count($content['schedule']) != 7) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"schedule" should contains 7 days info, but contains ' . count($content['schedule']) . ' instead';
                }
                if (isset($content['schedule'])) {
                    foreach ($content['schedule'] as $j => $day) {
                        if (!isset($day['weekday'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"weekday" not defined in $list_points[' .$i. ']['.$x.']["schedule"]['.$j.'] array';
                        }
                        if (!isset($day['open_am'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"open_am" not defined in $list_points[' .$i. ']['.$x.']["schedule"]['.$j.'] array';
                        }
                        if (!isset($day['close_am'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"close_am" not defined in $list_points[' .$i. ']['.$x.']["schedule"]['.$j.'] array';
                        }
                        if (!isset($day['open_pm'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"open_pm" not defined in $list_points[' .$i. ']['.$x.']["schedule"]['.$j.'] array';
                        }
                        if (!isset($day['close_pm'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"close_pm" not defined in $list_points[' .$i. ']['.$x.']["schedule"]['.$j.'] array';
                        }
                    }
                }
            }
        }
    }

    $result['duration'] = microtime_float() - $start;
    return $result;
}
function test_Country()
{
    $result = default_value();
    $start = microtime_float();

    /* Initialisation */
    $env = new Country();
    $env->getCountries();
    $env->getCountry("NL");

    /* Reception test */
    if ($env->curl_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while sending the query';
    } elseif ($env->resp_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Invalid query : ' . $userData["api_key"];
        foreach ($env->resp_errors_list as $message) {
            $result['reception_info'][count($result['reception_info'])] = $message['message'];
        }
    } else {
        $result['reception'] = max($result['reception'], OK);
        $result['reception_info'][count($result['reception_info'])] = 'Reception time : ' . (microtime_float() - $start). 's';
        ;
    }

    /* Additionals test */

    /* Test for the result structure */
    $result['additionals'][0]['name'] = 'Structure';
    $result['additionals'][0]['state'] = OK;
    if (count($env->countries) == 0) {
        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$countries array is empty';
    } else {
        foreach ($env->countries as $x => $country) {
            if (!isset($env->countries[$x]['label'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $countries["'.$x.'"] array';
            }
            if (!isset($env->countries[$x]['code'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $countries["'.$x.'"] array';
            }
        }
    }
    if (count($env->country) == 0) {
        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$country array is empty';
    } else {
        foreach ($env->country as $x => $country) {
            if (!isset($env->country[$x]['label'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $country['.$x.'] array';
            }
            if (!isset($env->country[$x]['code'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $country['.$x.'] array';
            }
        }
    }

    $result['duration'] = microtime_float() - $start;
    return $result;
}
function test_ParcelPoint()
{
    $result = default_value();
    $start = microtime_float();

    /* Initialisation */
    $env = new ParcelPoint();

    $env->getParcelPoint("dropoff_point", "MONR-003921");
    $env->getParcelPoint("dropoff_point", "SOGP-C1160");
    $env->getParcelPoint("pickup_point", "MONR-018481");
    $env->getParcelPoint("pickup_point", "SOGP-C3210");

    /* Reception test */
    if ($env->curl_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while sending the query';
    } elseif ($env->resp_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Invalid query : ' . $userData["api_key"];
        foreach ($env->resp_errors_list as $message) {
            $result['reception_info'][count($result['reception_info'])] = $message['message'];
        }
    } else {
        $result['reception'] = max($result['reception'], OK);
        $result['reception_info'][count($result['reception_info'])] = 'Reception time : ' . (microtime_float() - $start). 's';
        ;
    }

    /* Additionals test */

    /* Test for the result structure */
    $result['additionals'][0]['name'] = 'Structure';
    $result['additionals'][0]['state'] = OK;
    if (count($env->points) != 2) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$points should contains 2 elements, contains ' . count($env->points) . ' instead';
    } else {
        foreach ($env->points as $type => $points) {
            if (count($env->points[$type]) == 0) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$points["'.$type.'"] array is empty';
            } else {
                foreach ($points as $point) {
                    if (!isset($point['code'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $points["'.$type.'"] array';
                    }
                    if (!isset($point['name'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"name" not defined in $points["'.$type.'"] array';
                    }
                    if (!isset($point['address'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"address" not defined in $points["'.$type.'"] array';
                    }
                    if (!isset($point['city'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"city" not defined in $points["'.$type.'"] array';
                    }
                    if (!isset($point['zipcode'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"zipcode" not defined in $points["'.$type.'"] array';
                    }
                    if (!isset($point['country'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"country" not defined in $points["'.$type.'"] array';
                    }
                    if (!isset($point['description'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"description" not defined in $points["'.$type.'"] array';
                    }
                    if (!isset($point['schedule'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"days" not defined in $points["'.$type.'"] array';
                    } elseif (count($point['schedule']) != 7) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"days" should contains 7 days info, but contains ' . count($env->points[$type]['schedule']) . ' instead';
                    }
                    if (isset($point['schedule'])) {
                        foreach ($point['schedule'] as $j => $day) {
                            if (!isset($point['schedule'][$j]['weekday'])) {
                                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"weekday" not defined in $points["'.$type.'"]["days"]['.$j.'] array';
                            }
                            if (!isset($point['schedule'][$j]['open_am'])) {
                                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"open_am" not defined in $points["'.$type.'"]["days"]['.$j.'] array';
                            }
                            if (!isset($point['schedule'][$j]['close_am'])) {
                                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"close_am" not defined in $points["'.$type.'"]["days"]['.$j.'] array';
                            }
                            if (!isset($point['schedule'][$j]['open_pm'])) {
                                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"open_pm" not defined in $points["'.$type.'"]["days"]['.$j.'] array';
                            }
                            if (!isset($point['schedule'][$j]['close_pm'])) {
                                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"close_pm" not defined in $points["'.$type.'"]["days"]['.$j.'] array';
                            }
                        }
                    }
                }
            }
        }
    }

    $result['duration'] = microtime_float() - $start;
    return $result;
}
function test_OrderStatus()
{
    /* Create an order for the test */
    /* Initialisation */
    if (EMC_MODE == "prod") {
        $emcRef = "1605122984DHLEQA2DFR";
    } else {
        $emcRef = "1606073393UPSE082ZFR";
    }
    $result = default_value();
    $start = microtime_float();

    /* Initialisation */
    $env = new OrderStatus($emcRef);

    /* Reception test */
    if ($env->curl_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while sending the query';
    } elseif ($env->resp_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Invalid query : ' . $userData["api_key"];
        foreach ($env->resp_errors_list as $message) {
            $result['reception_info'][count($result['reception_info'])] = $message['message'];
        }
    } else {
        $result['reception'] = max($result['reception'], OK);
        $result['reception_info'][count($result['reception_info'])] = 'Reception time : ' . (microtime_float() - $start). 's';
        ;
    }

    /* Additionals test */

    /* Test for the result structure */
    $result['additionals'][0]['name'] = 'Structure';
    $result['additionals'][0]['state'] = OK;
    if (count($env->order_info) == 0) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order_info array is empty';
    } else {
        if (!isset($env->order_info['emcRef'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"emcRef" not defined in $order_info array';
        }
        if (!isset($env->order_info['state'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"emcRef" not defined in $order_info array';
        }
        if (!isset($env->order_info['opeRef'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"emcRef" not defined in $order_info array';
        }
        if (!isset($env->order_info['labelAvailable'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"emcRef" not defined in $order_info array';
        } elseif ($env->order_info['labelAvailable']) {
            if (!isset($env->order_info['labelUrl'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"labelUrl" not defined in $order_info array';
            }
            if (!isset($env->order_info['labels'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"labels" not defined in $order_info array';
            } else {
                if (count($env->order_info['labels']) == 0) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order_info["labels"] array is empty';
                }
            }
        }
    }

    $result['duration'] = microtime_float() - $start;
    return $result;
}
function test_Service()
{
    $result = default_value();
    $start = microtime_float();

    /* Initialisation */
    $env = new Service();

    $env->getServices();

    /* Reception test */
    if ($env->curl_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while sending the query';
    } elseif ($env->resp_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Invalid query : ' . $userData["api_key"];
        foreach ($env->resp_errors_list as $message) {
            $result['reception_info'][count($result['reception_info'])] = $message['message'];
        }
    } else {
        $result['reception'] = max($result['reception'], OK);
        $result['reception_info'][count($result['reception_info'])] = 'Reception time : ' . (microtime_float() - $start). 's';
        ;
    }

    /* Additionals test */

    /* Test for the result structure */
    $result['additionals'][0]['name'] = 'Structure';
    $result['additionals'][0]['state'] = OK;
    if (count($env->carriers) == 0) {
        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$carriers array is empty';
    } else {
        foreach ($env->carriers as $code => $carrier) {
            if (!isset($carrier['label'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['code'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['logo'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"logo" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['logo_modules'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"logo_modules" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['description'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"description" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['address'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"address" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['url'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"url" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['tracking'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"tracking" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['tel'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"tel" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['cgv'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"cgv" not defined in $carriers["'.$code.'"] array';
            }
            if (!isset($carrier['services'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"services" not defined in $carriers["'.$code.'"] array';
            } else {
                if (count($carrier['services']) == 0) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$carriers["'.$code.'"]["services"] array is empty';
                } else {
                    foreach ($carrier['services'] as $s => $service) {
                        if (!isset($service['code'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $carriers["'.$code.'"]["services"]["'.$s.'"] array';
                        }
                        if (!isset($service['label'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $carriers["'.$code.'"]["services"]["'.$s.'"] array';
                        }
                        if (!isset($service['mode'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"mode" not defined in $carriers["'.$code.'"]["services"]["'.$s.'"] array';
                        }
                        if (!isset($service['alert'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"alert" not defined in $carriers["'.$code.'"]["services"]["'.$s.'"] array';
                        }
                        if (!isset($service['collection'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"collection" not defined in $carriers["'.$code.'"]["services"]["'.$s.'"] array';
                        }
                        if (!isset($service['delivery'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"delivery" not defined in $carriers["'.$code.'"]["services"]["'.$s.'"] array';
                        }
                        if (!isset($service['is_pluggable'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"is_pluggable" not defined in $carriers["'.$code.'"]["services"]["'.$s.'"] array';
                        }
                        if (!isset($service['options'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"options" not defined in $carriers["'.$code.'"]["services"]["'.$s.'"] array';
                        } else {
                            if (count($service['options']) == 0) {
                                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$carriers["'.$code.'"]["services"]["'.$s.'"]["options"] array is empty';
                            }
                        }
                        if (!isset($service['exclusions'])) {
                            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"exclusions" not defined in $carriers["'.$code.'"]["services"]["'.$s.'"] array';
                        } else {
                            if (count($service['exclusions']) == 0) {
                                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$carriers["'.$code.'"]["services"]["'.$s.'"]["exclusions"] array is empty';
                            }
                        }
                    }
                }
            }
        }
    }

    $result['duration'] = microtime_float() - $start;
    return $result;
}
function test_User()
{
    $result = default_value();
    $start = microtime_float();

    /* Initialisation */
    $env = new User();
    $env->getEmailConfiguration();

    /* Reception test */
    if ($env->curl_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while sending the query';
    } elseif ($env->resp_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Invalid query : ' . $userData["api_key"];
        foreach ($env->resp_errors_list as $message) {
            $result['reception_info'][count($result['reception_info'])] = $message['message'];
        }
    } else {
        $result['reception'] = max($result['reception'], OK);
        $result['reception_info'][count($result['reception_info'])] = 'Reception time : ' . (microtime_float() - $start). 's';
        ;
    }

    /* Additionals test */

    /* Test for the result structure */
    $result['additionals'][0]['name'] = 'Structure';
    $result['additionals'][0]['state'] = OK;
    if (!isset($env->user_configuration['emails'])) {
        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"emails" not defined in $user_configuration array';
    } else {
        if (count($env->user_configuration['emails']) == 0) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$user_configuration array is empty';
        }
    }

    $result['duration'] = microtime_float() - $start;
    return $result;
}
function test_Quotation($userData)
{
    $result = default_value();
    $start = microtime_float();

    /* Initialisation */

    // shipper address
    $from = array(
        'pays' => 'FR',  // must be an ISO code, set get_country example on how to get codes
        'code_postal' => '75002',
        'ville' => 'Paris',
        'type' => 'entreprise', // accepted values are "particulier" or "entreprise"
        'adresse' => '15, rue Marsollier',
        'civilite' => 'M', // accepted values are "M" (sir) or "Mme" (madam)
        'prenom' => 'John',
        'nom' => 'Snow',
        'societe' => 'Boxtale',
        'email' => 'jsnow@boxtale.com',
        'tel' => '0606060606',
        'infos' => 'Some informations about this address'
    );


    // Recipient's address
    $to = array(
        'pays' => 'FR',  // must be an ISO code, set get_country example on how to get codes
        'code_postal' => '13002',
        'ville' => 'Marseille',
        'type' => 'particulier', // accepted values are "particulier" or "entreprise"
        'adresse' => '1, rue Chape',
        'civilite' => 'Mme', // accepted values are "M" (sir) or "Mme" (madam)
        'prenom' => 'Jane',
        'nom' => 'Doe',
        'email' => 'jdoe@boxtale.com',
        'tel' => '0606060606',
        'infos' => 'Some informations about this address'
    );

    /* Parcels informations */
    $parcels = array(
        'type' => 'colis', // your shipment type: "encombrant" (bulky parcel), "colis" (parcel), "palette" (pallet), "pli" (envelope)
        'dimensions' => array(
            1 => array(
                'poids' => 5,
                'longueur' => 15,
                'largeur' => 16,
                'hauteur' => 8
            ),
            2 => array(
                'poids' => 21,
                'longueur' => 7,
                'largeur' => 8,
                'hauteur' => 11
            )
        )
    );

    $order_env = new Quotation($from, $to, $parcels);
    // Informations sur l'envoi
    $order_quotInfo = array("collecte" => date("Y-m-d"), "delai" => "aucun",  "code_contenu" => 10120,
    "operator" => "UPSE",
    "raison" => "sale",
    "colis.valeur" => 1200,
    // "assurance.selection" => false,
    "colis.description" => "Des journaux",
    "disponibilite.HDE" => "09:00",
    "disponibilite.HLE" => "19:00");

    // Pour cet envoi on est oblig� de joindre une facture proforma qui r�sume 2 objets exp�di�s
    $order_env->setProforma(array(1 => array("description_en" => "L'Equipe newspaper from 1998",
    "description_fr" => "le journal L'Equipe du 1998",  "nombre" => 1, "valeur" => 10,
    "origine" => "FR", "poids" => 1.2),
    2 => array("description_en" => "300 editions of L'Equipe newspaper from 1999",
    "description_fr" => "300 num�ros de L'Equipe du 1999",  "nombre" => 300,  "valeur" => 8,
    "origine" => "FR", "poids" => 0.1)));

    $order_env->setEnv('test'); // To make an order on test
    $orderPassed = $order_env->makeOrder($order_quotInfo, true);

    $offer_quotInfo = array(
        "collecte" => date("Y-m-d"),
        "delai" => "aucun",
        "code_contenu" => 10120
        );

    $offer_env = new Quotation($from, $to, $parcels, $offer_quotInfo);
    $offer_env->getOffers();

    /* Reception test */
    if (!$orderPassed) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while making the order (makeOrder returned false)';
    }
    if ($order_env->curl_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while sending the query';
    } elseif ($order_env->resp_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Invalid query : ' . $userData["api_key"];
        foreach ($order_env->resp_errors_list as $message) {
            $result['reception_info'][count($result['reception_info'])] = $message['message'];
        }
    } elseif ($offer_env->curl_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Error while sending the query';
    } elseif ($offer_env->resp_error) {
        $result['reception'] = max($result['reception'], ERROR);
        $result['reception_info'][count($result['reception_info'])] = 'Invalid query : ' . $userData["api_key"];
        foreach ($offer_env->resp_errors_list as $message) {
            $result['reception_info'][count($result['reception_info'])] = $message['message'];
        }
    } else {
        $result['reception'] = max($result['reception'], OK);
        $result['reception_info'][count($result['reception_info'])] = 'Reception time : ' . (microtime_float() - $start). 's';
    }

    /* Additionals test */

    /* Test for the result structure */
    $result['additionals'][0]['name'] = 'Structure';
    $result['additionals'][0]['state'] = OK;
    if (count($offer_env->offers) == 0) {
        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$offers array is empty';
    } else {
        foreach ($offer_env->offers as $x => $offer) {
            if (!isset($offer_env->offers[$x]['mode'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"mode" not defined in $offers['.$x.'] array';
            }
            if (!isset($offer_env->offers[$x]['url'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"url" not defined in $offers['.$x.'] array';
            }
            if (!isset($offer_env->offers[$x]['characteristics'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"characteristics" not defined in $offers['.$x.'] array';
            }
            if (!isset($offer_env->offers[$x]['alert'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"alert" not defined in $offers['.$x.'] array';
            }
            if (!isset($offer_env->offers[$x]['operator'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"operator" not defined in $offers['.$x.'] array';
            } else {
                if (count($offer_env->offers[$x]['operator']) == 0) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$offers['.$x.']["operator"] array is empty';
                } else {
                    if (!isset($offer_env->offers[$x]['operator']['code'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $offers['.$x.']["operator"] array';
                    }
                    if (!isset($offer_env->offers[$x]['operator']['label'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $offers['.$x.']["operator"] array';
                    }
                    if (!isset($offer_env->offers[$x]['operator']['logo'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"logo" not defined in $offers['.$x.']["operator"] array';
                    }
                }
            }
            if (!isset($offer_env->offers[$x]['service'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"operator" not defined in $offers['.$x.'] array';
            } else {
                if (count($offer_env->offers[$x]['service']) == 0) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$offers['.$x.']["service"] array is empty';
                } else {
                    if (!isset($offer_env->offers[$x]['service']['code'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $offers['.$x.']["service"] array';
                    }
                    if (!isset($offer_env->offers[$x]['service']['label'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $offers['.$x.']["service"] array';
                    }
                }
            }
            if (!isset($offer_env->offers[$x]['price'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"price" not defined in $offers['.$x.'] array';
            } else {
                if (count($offer_env->offers[$x]['price']) == 0) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$offers['.$x.']["price"] array is empty';
                } else {
                    if (!isset($offer_env->offers[$x]['price']['currency'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"currency" not defined in $offers['.$x.']["price"] array';
                    }
                    if (!isset($offer_env->offers[$x]['price']['tax-exclusive'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"tax-exclusive" not defined in $offers['.$x.']["price"] array';
                    }
                    if (!isset($offer_env->offers[$x]['price']['tax-inclusive'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"tax-inclusive" not defined in $offers['.$x.']["price"] array';
                    }
                }
            }
            if (!isset($offer_env->offers[$x]['collection'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"collection" not defined in $offers['.$x.'] array';
            } else {
                if (count($offer_env->offers[$x]['collection']) == 0) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$offers['.$x.']["collection"] array is empty';
                } else {
                    if (!isset($offer_env->offers[$x]['collection']['type'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"type" not defined in $offers['.$x.']["collection"] array';
                    }
                    if (!isset($offer_env->offers[$x]['collection']['date'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"date" not defined in $offers['.$x.']["collection"] array';
                    }
                    if (!isset($offer_env->offers[$x]['collection']['label'])) {
                        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $offers['.$x.']["collection"] array';
                    }
                }
            }
            if (!isset($offer_env->offers[$x]['mandatory'])) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"mandatory" not defined in $offers['.$x.'] array';
            } elseif (count($offer_env->offers[$x]['mandatory']) == 0) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$offers['.$x.']["mandatory"] array is empty';
            }
        }
    }

    if (count($order_env->order) == 0) {
        $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
        $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order array is empty';
    } else {
        if (!isset($order_env->order['ref'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"ref" not defined in $order array';
        }
        if (!isset($order_env->order['date'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"date" not defined in $order array';
        }
        if (!isset($order_env->order['mode'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"mode" not defined in $order array';
        }
        if (!isset($order_env->order['url'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"url" not defined in $order array';
        }
        if (!isset($order_env->order['proforma'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"proforma" not defined in $order array';
        }
        if (!isset($order_env->order['offer']['operator'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"offer"/"operator" not defined in $order array';
        } else {
            if (count($order_env->order['offer']['operator']) == 0) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order['.$x.']["offer"]["operator"] array is empty';
            } else {
                if (!isset($order_env->order['offer']['operator']['code'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $order["offer"]["operator"] array';
                }
                if (!isset($order_env->order['offer']['operator']['label'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $order["offer"]["operator"] array';
                }
                if (!isset($order_env->order['offer']['operator']['logo'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"logo" not defined in $order["offer"]["operator"] array';
                }
            }
        }
        if (!isset($order_env->order['service'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"operator" not defined in $order array';
        } else {
            if (count($order_env->order['service']) == 0) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order["service"] array is empty';
            } else {
                if (!isset($order_env->order['service']['code'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $order["service"] array';
                }
                if (!isset($order_env->order['service']['label'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $order["service"] array';
                }
            }
        }
        if (!isset($order_env->order['price'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"price" not defined in $order array';
        } else {
            if (count($order_env->order['price']) == 0) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order["price"] array is empty';
            } else {
                if (!isset($order_env->order['price']['currency'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"currency" not defined in $order["price"] array';
                }
                if (!isset($order_env->order['price']['tax-exclusive'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"tax-exclusive" not defined in $order["price"] array';
                }
                if (!isset($order_env->order['price']['tax-inclusive'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"tax-inclusive" not defined in $order["price"] array';
                }
            }
        }
        if (!isset($order_env->order['collection'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"collection" not defined in $order array';
        } else {
            if (count($order_env->order['collection']) == 0) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order["collection"] array is empty';
            } else {
                if (!isset($order_env->order['collection']['code'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $order["collection"] array';
                }
                if (!isset($order_env->order['collection']['type_label'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"type_label" not defined in $order["collection"] array';
                }
                if (!isset($order_env->order['collection']['date'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"date" not defined in $order["collection"] array';
                }
                if (!isset($order_env->order['collection']['time'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"time" not defined in $order["collection"] array';
                }
                if (!isset($order_env->order['collection']['label'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $order["collection"] array';
                }
            }
        }
        if (!isset($order_env->order['delivery'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"delivery" not defined in $order array';
        } else {
            if (count($order_env->order['delivery']) == 0) {
                $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
                $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order["delivery"] array is empty';
            } else {
                if (!isset($order_env->order['delivery']['code'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"code" not defined in $order["delivery"] array';
                }
                if (!isset($order_env->order['delivery']['type_label'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"type_label" not defined in $order["delivery"] array';
                }
                if (!isset($order_env->order['delivery']['date'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"date" not defined in $order["delivery"] array';
                }
                if (!isset($order_env->order['delivery']['time'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"time" not defined in $order["delivery"] array';
                }
                if (!isset($order_env->order['delivery']['label'])) {
                    $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
                    $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"label" not defined in $order["delivery"] array';
                }
            }
        }
        if (!isset($order_env->order['alerts'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"alerts" not defined in $order array';
        } elseif (count($order_env->order['alerts']) == 0) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order["alerts"] array is empty';
        }
        if (!isset($order_env->order['chars'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"chars" not defined in $order array';
        } elseif (count($order_env->order['chars']) == 0) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order["chars"] array is empty';
        }
        if (!isset($order_env->order['labels'])) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], ERROR);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '"labels" not defined in $order array';
        } elseif (count($order_env->order['labels']) == 0) {
            $result['additionals'][0]['state'] = max($result['additionals'][0]['state'], WARNING);
            $result['additionals'][0]['info'][count($result['additionals'][0]['info'])] = '$order["labels"] array is empty';
        }
    }

    $result['duration'] = microtime_float() - $start;
    return $result;
}

?>
    <!-- Code servant � tester le fonctionnement des classes du module -->
    <div class="row">
        <table class="table table-striped table-bordered">
            <tr>
                <th>Classe</th>
                <th>Test duration</th>
                <th>Answer reception</th>
                <th>Additional tests</th>
            </tr>
            <tbody>
<?php
foreach ($_CLASSES as $class) {
    $result = array();
    try {
        eval('$result = test_'.$class.'($userData);');
    } catch (Exception $e) {
        echo 'pas done';
        $result = default_value();
        $result['reception'] = ERROR;
        $result['reception_info'][0] = $e->getMessage();
        $result['duration'] = 0;
    }
?>
<tr>
    <td><span class="class"><?php echo $class;?></span></td>
    <td><span class="badge alert-info"><?php echo $result['duration'] . ' s';?></span></td>
    <td><?php echo parse_message($result['reception'], $result['reception_info']);?></td>
    <td>
        <ul class="additional">
        <?php
        foreach ($result['additionals'] as $test) {
            echo '<li>'.$test['name']. ' : '.parse_message($test['state'], $test['info']).'</li>';
        }
        ?>
        </ul>
    </td>
</tr>
<?php
}
?>
            </tbody>
        </table>
<?php
require_once(EMC_PARENT_DIR.'layout/footer.php');
