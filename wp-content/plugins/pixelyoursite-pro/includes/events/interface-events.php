<?php
namespace PixelYourSite;

interface EventsFactory {


    function getCount();
    function isEnabled();
    function getOptions();

    function getEvents();
    /**
     * Check is event ready for fire
     * @param $event
     * @return bool
     */
    function isReadyForFire($event);

    /**
     * @param String $event
     * @return SingleEvent
     */
    function getEvent($event);
}