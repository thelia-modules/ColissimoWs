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
 * Date: 06/09/2019 01:27
 */

namespace ColissimoWs\Soap;

use ColissimoPostage\ServiceType\Generate;

class GenerateWithAttachments extends Generate
{
    const DEFAULT_SOAP_CLIENT_CLASS = '\ColissimoWs\Soap\SoapClientWithAttachements';


    public function getRawResponse()
    {
        return self::getSoapClient()->getRawResponse();
    }
}
