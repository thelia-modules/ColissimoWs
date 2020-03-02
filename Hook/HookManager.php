<?php
/*************************************************************************************/
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

/**
 * Created by Franck Allimant, CQFDev <franck@cqfdev.fr>
 * Date: 17/08/2019 14:34
 */
namespace ColissimoWs\Hook;

use ColissimoWs\ColissimoWs;
use ColissimoWs\Model\ColissimowsLabelQuery;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ModuleConfig;
use Thelia\Model\ModuleConfigQuery;
use Thelia\Tools\URL;

class HookManager extends BaseHook
{
    public function onModuleConfigure(HookRenderEvent $event)
    {
        $vars = [ ];

        if (null !== $params = ModuleConfigQuery::create()->findByModuleId(ColissimoWs::getModuleId())) {

            /** @var ModuleConfig $param */
            foreach ($params as $param) {
                $vars[ $param->getName() ] = $param->getValue();
            }
        }

        $event->add(
            $this->render(
                'colissimows/module_configuration.html',
                $vars
            )
        );
    }

    public function onMainTopMenuTools(HookRenderBlockEvent $event)
    {
        $event->add(
            [
                'id' => 'tools_menu_colissimows',
                'class' => '',
                'url' => URL::getInstance()->absoluteUrl('/admin/module/ColissimoWs'),
                'title' => $this->translator->trans("Colissimo labels (%num)", [ '%num' => ColissimowsLabelQuery::create()->count() ], ColissimoWs::DOMAIN_NAME)
            ]
        );
    }

    public function onModuleConfigJs(HookRenderEvent $event)
    {
        $event->add($this->render('colissimows/module-config-js.html'));
    }

}
