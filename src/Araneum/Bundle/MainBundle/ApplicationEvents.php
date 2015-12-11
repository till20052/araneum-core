<?php

namespace Araneum\Bundle\MainBundle;

/**
 * Class ApplicationEvents
 *
 * @package Araneum\Bundle\MainBundle
 */
final class ApplicationEvents
{
    const POST_PERSIST = 'araneum.main.application.event.post_persist';
    const POST_UPDATE  = 'araneum.main.application.event.post_update';
    const PRE_REMOVE   = 'araneum.main.application.event.pre_remove';
}
