<?php
/*********************************************************************
 *
 * $Id: yocto_rangefinder.php 26329 2017-01-11 14:04:39Z mvuilleu $
 *
 * Implements YRangeFinder, the high-level API for RangeFinder functions
 *
 * - - - - - - - - - License information: - - - - - - - - - 
 *
 *  Copyright (C) 2011 and beyond by Yoctopuce Sarl, Switzerland.
 *
 *  Yoctopuce Sarl (hereafter Licensor) grants to you a perpetual
 *  non-exclusive license to use, modify, copy and integrate this
 *  file into your software for the sole purpose of interfacing
 *  with Yoctopuce products.
 *
 *  You may reproduce and distribute copies of this file in
 *  source or object form, as long as the sole purpose of this
 *  code is to interface with Yoctopuce products. You must retain
 *  this notice in the distributed source file.
 *
 *  You should refer to Yoctopuce General Terms and Conditions
 *  for additional information regarding your rights and
 *  obligations.
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED 'AS IS' WITHOUT
 *  WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING 
 *  WITHOUT LIMITATION, ANY WARRANTY OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE, TITLE AND NON-INFRINGEMENT. IN NO
 *  EVENT SHALL LICENSOR BE LIABLE FOR ANY INCIDENTAL, SPECIAL,
 *  INDIRECT OR CONSEQUENTIAL DAMAGES, LOST PROFITS OR LOST DATA,
 *  COST OF PROCUREMENT OF SUBSTITUTE GOODS, TECHNOLOGY OR
 *  SERVICES, ANY CLAIMS BY THIRD PARTIES (INCLUDING BUT NOT
 *  LIMITED TO ANY DEFENSE THEREOF), ANY CLAIMS FOR INDEMNITY OR
 *  CONTRIBUTION, OR OTHER SIMILAR COSTS, WHETHER ASSERTED ON THE
 *  BASIS OF CONTRACT, TORT (INCLUDING NEGLIGENCE), BREACH OF
 *  WARRANTY, OR OTHERWISE.
 *
 *********************************************************************/

//--- (YRangeFinder return codes)
//--- (end of YRangeFinder return codes)
//--- (YRangeFinder definitions)
if(!defined('Y_RANGEFINDERMODE_DEFAULT'))    define('Y_RANGEFINDERMODE_DEFAULT',   0);
if(!defined('Y_RANGEFINDERMODE_LONG_RANGE')) define('Y_RANGEFINDERMODE_LONG_RANGE', 1);
if(!defined('Y_RANGEFINDERMODE_HIGH_ACCURACY')) define('Y_RANGEFINDERMODE_HIGH_ACCURACY', 2);
if(!defined('Y_RANGEFINDERMODE_HIGH_SPEED')) define('Y_RANGEFINDERMODE_HIGH_SPEED', 3);
if(!defined('Y_RANGEFINDERMODE_INVALID'))    define('Y_RANGEFINDERMODE_INVALID',   -1);
if(!defined('Y_COMMAND_INVALID'))            define('Y_COMMAND_INVALID',           YAPI_INVALID_STRING);
//--- (end of YRangeFinder definitions)

//--- (YRangeFinder declaration)
/**
 * YRangeFinder Class: RangeFinder function interface
 *
 * The Yoctopuce class YRangeFinder allows you to use and configure Yoctopuce range finders
 * sensors. It inherits from YSensor class the core functions to read measurements,
 * register callback functions, access to the autonomous datalogger.
 * This class adds the ability to easily perform a one-point linear calibration
 * to compensate the effect of a glass or filter placed in front of the sensor.
 */
class YRangeFinder extends YSensor
{
    const RANGEFINDERMODE_DEFAULT        = 0;
    const RANGEFINDERMODE_LONG_RANGE     = 1;
    const RANGEFINDERMODE_HIGH_ACCURACY  = 2;
    const RANGEFINDERMODE_HIGH_SPEED     = 3;
    const RANGEFINDERMODE_INVALID        = -1;
    const COMMAND_INVALID                = YAPI_INVALID_STRING;
    //--- (end of YRangeFinder declaration)

    //--- (YRangeFinder attributes)
    protected $_rangeFinderMode          = Y_RANGEFINDERMODE_INVALID;    // RangeFinderMode
    protected $_command                  = Y_COMMAND_INVALID;            // Text
    //--- (end of YRangeFinder attributes)

    function __construct($str_func)
    {
        //--- (YRangeFinder constructor)
        parent::__construct($str_func);
        $this->_className = 'RangeFinder';

        //--- (end of YRangeFinder constructor)
    }

    //--- (YRangeFinder implementation)

    function _parseAttr($name, $val)
    {
        switch($name) {
        case 'rangeFinderMode':
            $this->_rangeFinderMode = intval($val);
            return 1;
        case 'command':
            $this->_command = $val;
            return 1;
        }
        return parent::_parseAttr($name, $val);
    }

    /**
     * Changes the measuring unit for the measured temperature. That unit is a string.
     * String value can be " or mm. Any other value will be ignored.
     * Remember to call the saveToFlash() method of the module if the modification must be kept.
     * WARNING: if a specific calibration is defined for the rangeFinder function, a
     * unit system change will probably break it.
     *
     * @param newval : a string corresponding to the measuring unit for the measured temperature
     *
     * @return YAPI_SUCCESS if the call succeeds.
     *
     * On failure, throws an exception or returns a negative error code.
     */
    public function set_unit($newval)
    {
        $rest_val = $newval;
        return $this->_setAttr("unit",$rest_val);
    }

    /**
     * Returns the rangefinder running mode. The rangefinder running mode
     * allows to put priority on precision, speed or maximum range.
     *
     * @return a value among Y_RANGEFINDERMODE_DEFAULT, Y_RANGEFINDERMODE_LONG_RANGE,
     * Y_RANGEFINDERMODE_HIGH_ACCURACY and Y_RANGEFINDERMODE_HIGH_SPEED corresponding to the rangefinder running mode
     *
     * On failure, throws an exception or returns Y_RANGEFINDERMODE_INVALID.
     */
    public function get_rangeFinderMode()
    {
        if ($this->_cacheExpiration <= YAPI::GetTickCount()) {
            if ($this->load(YAPI::$defaultCacheValidity) != YAPI_SUCCESS) {
                return Y_RANGEFINDERMODE_INVALID;
            }
        }
        return $this->_rangeFinderMode;
    }

    /**
     * Changes the rangefinder running mode, allowing to put priority on
     * precision, speed or maximum range.
     *
     * @param newval : a value among Y_RANGEFINDERMODE_DEFAULT, Y_RANGEFINDERMODE_LONG_RANGE,
     * Y_RANGEFINDERMODE_HIGH_ACCURACY and Y_RANGEFINDERMODE_HIGH_SPEED corresponding to the rangefinder
     * running mode, allowing to put priority on
     *         precision, speed or maximum range
     *
     * @return YAPI_SUCCESS if the call succeeds.
     *
     * On failure, throws an exception or returns a negative error code.
     */
    public function set_rangeFinderMode($newval)
    {
        $rest_val = strval($newval);
        return $this->_setAttr("rangeFinderMode",$rest_val);
    }

    public function get_command()
    {
        if ($this->_cacheExpiration <= YAPI::GetTickCount()) {
            if ($this->load(YAPI::$defaultCacheValidity) != YAPI_SUCCESS) {
                return Y_COMMAND_INVALID;
            }
        }
        return $this->_command;
    }

    public function set_command($newval)
    {
        $rest_val = $newval;
        return $this->_setAttr("command",$rest_val);
    }

    /**
     * Retrieves a range finder for a given identifier.
     * The identifier can be specified using several formats:
     * <ul>
     * <li>FunctionLogicalName</li>
     * <li>ModuleSerialNumber.FunctionIdentifier</li>
     * <li>ModuleSerialNumber.FunctionLogicalName</li>
     * <li>ModuleLogicalName.FunctionIdentifier</li>
     * <li>ModuleLogicalName.FunctionLogicalName</li>
     * </ul>
     *
     * This function does not require that the range finder is online at the time
     * it is invoked. The returned object is nevertheless valid.
     * Use the method YRangeFinder.isOnline() to test if the range finder is
     * indeed online at a given time. In case of ambiguity when looking for
     * a range finder by logical name, no error is notified: the first instance
     * found is returned. The search is performed first by hardware name,
     * then by logical name.
     *
     * @param func : a string that uniquely characterizes the range finder
     *
     * @return a YRangeFinder object allowing you to drive the range finder.
     */
    public static function FindRangeFinder($func)
    {
        // $obj                    is a YRangeFinder;
        $obj = YFunction::_FindFromCache('RangeFinder', $func);
        if ($obj == null) {
            $obj = new YRangeFinder($func);
            YFunction::_AddToCache('RangeFinder', $func, $obj);
        }
        return $obj;
    }

    /**
     * Triggers a sensor calibration according to the current ambient temperature. That
     * calibration process needs no physical interaction with the sensor. It is performed
     * automatically at device startup, but it is recommended to start it again when the
     * temperature delta since last calibration exceeds 8°C.
     *
     * @return YAPI_SUCCESS if the call succeeds.
     *         On failure, throws an exception or returns a negative error code.
     */
    public function triggerTempCalibration()
    {
        return $this->set_command('T');
    }

    public function setUnit($newval)
    { return $this->set_unit($newval); }

    public function rangeFinderMode()
    { return $this->get_rangeFinderMode(); }

    public function setRangeFinderMode($newval)
    { return $this->set_rangeFinderMode($newval); }

    public function command()
    { return $this->get_command(); }

    public function setCommand($newval)
    { return $this->set_command($newval); }

    /**
     * Continues the enumeration of range finders started using yFirstRangeFinder().
     *
     * @return a pointer to a YRangeFinder object, corresponding to
     *         a range finder currently online, or a null pointer
     *         if there are no more range finders to enumerate.
     */
    public function nextRangeFinder()
    {   $resolve = YAPI::resolveFunction($this->_className, $this->_func);
        if($resolve->errorType != YAPI_SUCCESS) return null;
        $next_hwid = YAPI::getNextHardwareId($this->_className, $resolve->result);
        if($next_hwid == null) return null;
        return self::FindRangeFinder($next_hwid);
    }

    /**
     * Starts the enumeration of range finders currently accessible.
     * Use the method YRangeFinder.nextRangeFinder() to iterate on
     * next range finders.
     *
     * @return a pointer to a YRangeFinder object, corresponding to
     *         the first range finder currently online, or a null pointer
     *         if there are none.
     */
    public static function FirstRangeFinder()
    {   $next_hwid = YAPI::getFirstHardwareId('RangeFinder');
        if($next_hwid == null) return null;
        return self::FindRangeFinder($next_hwid);
    }

    //--- (end of YRangeFinder implementation)

};

//--- (RangeFinder functions)

/**
 * Retrieves a range finder for a given identifier.
 * The identifier can be specified using several formats:
 * <ul>
 * <li>FunctionLogicalName</li>
 * <li>ModuleSerialNumber.FunctionIdentifier</li>
 * <li>ModuleSerialNumber.FunctionLogicalName</li>
 * <li>ModuleLogicalName.FunctionIdentifier</li>
 * <li>ModuleLogicalName.FunctionLogicalName</li>
 * </ul>
 *
 * This function does not require that the range finder is online at the time
 * it is invoked. The returned object is nevertheless valid.
 * Use the method YRangeFinder.isOnline() to test if the range finder is
 * indeed online at a given time. In case of ambiguity when looking for
 * a range finder by logical name, no error is notified: the first instance
 * found is returned. The search is performed first by hardware name,
 * then by logical name.
 *
 * @param func : a string that uniquely characterizes the range finder
 *
 * @return a YRangeFinder object allowing you to drive the range finder.
 */
function yFindRangeFinder($func)
{
    return YRangeFinder::FindRangeFinder($func);
}

/**
 * Starts the enumeration of range finders currently accessible.
 * Use the method YRangeFinder.nextRangeFinder() to iterate on
 * next range finders.
 *
 * @return a pointer to a YRangeFinder object, corresponding to
 *         the first range finder currently online, or a null pointer
 *         if there are none.
 */
function yFirstRangeFinder()
{
    return YRangeFinder::FirstRangeFinder();
}

//--- (end of RangeFinder functions)
?>