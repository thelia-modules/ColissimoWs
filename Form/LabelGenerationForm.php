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
 * Date: 17/08/2019 12:26
 */
namespace ColissimoWs\Form;

use ColissimoWs\ColissimoWs;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class LabelGenerationForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'new_status',
                'choice', [
                    'label' => Translator::getInstance()->trans('Order status after export'),
                    'choices' => [
                        "nochange" => Translator::getInstance()->trans("Do not change", [], ColissimoWs::DOMAIN_NAME),
                        "processing" => Translator::getInstance()->trans("Set orders status as processing", [], ColissimoWs::DOMAIN_NAME),
                        "sent" => Translator::getInstance()->trans("Set orders status as sent", [], ColissimoWs::DOMAIN_NAME)
                    ],
                    'required' => 'true',
                    'expanded' => true,
                    'multiple' => false,
                    'data'     => ColissimoWs::getConfigValue("new_status", 'nochange')
                ]
            )
            ->add(
                'order_id',
                'collection',
                [
                    'type' => 'integer',
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ->add(
                "weight",
                'collection',
                [
                    'type' => 'number',
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
            ->add(
                "signed",
                "collection",
                [
                    'type' => 'checkbox',
                    'label' => 'Signature',
                    'allow_add' => true,
                    'allow_delete' => true,
                ]);

        ;
    }
}
