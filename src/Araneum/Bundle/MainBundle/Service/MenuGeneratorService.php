<?php

namespace Araneum\Bundle\MainBundle\Service;

class MenuGeneratorService
{

    private $output = [];

    /**
     * Generate one dimentional array
     *
     * @param $array
     * @return array
     */
    public function generateOneDimentional($array)
    {
        $tempArray = [];

        foreach ($array as $menu) {
            if (is_array($menu)) {

                $keys = array_keys($menu);
                $subMenu = false;
                $rootAdded = false;

                if (in_array('submenu', $keys)) {
                    $subMenu = true;
                }

                foreach ($keys as $key) {
                    if ($subMenu && $key != 'submenu') {
                        $tempArray[$key] = $menu[$key];
                    }

                    if ($subMenu && $key == 'submenu') {
                        $tempArray['heading'] = 'true';
                        array_push($this->output, $tempArray);
                        $rootAdded = true;
                        $this->generateOneDimentional($menu['submenu']);
                    }

                    if (!$subMenu) {
                        $tempArray[$key] = $menu[$key];
                    }
                }
                if (!$rootAdded) {
                    array_push($this->output, $tempArray);
                }
            }
        }

        return $this->output;
    }
}