<?php

/**
 * Gearman Bundle for Symfony2
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Janusz Paszyński <jpaszynski@have2code.com>
 */

namespace Mmoreram\GearmanBundle\Event\Worker;


use Mmoreram\GearmanBundle\Event\Abstracts\AbstractGearmanJobEvent;


/**
 * Event object sent to any gearman.worker.job.start kernel event listener. Provides no additional information
 * @package Mmoreram\GearmanBundle\Event\Worker
 * @since 3.1.0
 */
class JobStartEvent extends AbstractGearmanJobEvent
{

}
