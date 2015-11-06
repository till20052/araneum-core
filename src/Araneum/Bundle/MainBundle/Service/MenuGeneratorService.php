<?php

namespace Araneum\Bundle\MainBundle\Service;

class MenuGeneratorService
{

    /**
     * Generate one dimentional array
     *
     * @param $array
     * @return array
     */
    public function generateOneDimentional($array)
    {

        static $output = [];
        $tempArray = [];

        foreach ($array as $menu) {
            if (is_array($menu)) {

                $keys = array_keys($menu);
                $submenu = false;
                $rootadded = false;

                if (in_array('submenu', $keys)) {
                    $submenu = true;
                }

                foreach ($keys as $key) {
                    if ($submenu && $key != 'submenu') {
                        $tempArray[$key] = $menu[$key];
                    }

                    if ($submenu && $key == 'submenu') {
                        $tempArray['heading'] = 'true';
                        array_push($output, $tempArray);
                        $rootadded = true;
                        $this->generateOneDimentional($menu['submenu']);
                    }

                    if (!$submenu) {
                        $tempArray[$key] = $menu[$key];
                    }
                }
                if (!$rootadded) {
                    array_push($output, $tempArray);
                }
            }
        }

        return $output;
    }
}