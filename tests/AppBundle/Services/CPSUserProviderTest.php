<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Services;

use AppBundle\Entity\Allegato;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\CPSUser;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\User;
use AppBundle\Logging\LogConstants;
use AppBundle\Services\CPSUserProvider;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\VarDumper\VarDumper;
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class UserProviderTest
 * @package AppBundle\Services\Test
 */
class CPSUserProviderTest extends AbstractAppTestCase
{
    /**
     * @var CPSUserProvider
     */
    protected $userProvider;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->cleanDb(ComponenteNucleoFamiliare::class);
        $this->cleanDb(Allegato::class);
        $this->cleanDb(Pratica::class);
        $this->cleanDb(User::class);
    }

    /**
     * @test
     */
    public function testUserProviderAssignsFakeEmailToCPSUserWithNoRegisteredMail()
    {
        $mockLogger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $mockLogger->expects($this->exactly(1))
            ->method('notice')
            ->with(LogConstants::CPS_USER_CREATED_WITH_BOGUS_DATA);

        $this->container->set('logger', $mockLogger);
        $this->userProvider = $this->container->get('ocsdc.cps.userprovider');

        $data = $this->getCPSUserData();
        $data['codiceFiscale'] = 'RLDLCU77T05G224F';
        $data['emailAddress'] = null;
        $data['emailAddressPersonale'] = null;
        $user = $this->userProvider->provideUser($data);

        $this->assertContains($user->getId().'', $user->getEmail());
    }

    public function testUserProviderStoreCPSUser()
    {
        $mockLogger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $mockLogger->expects($this->exactly(1))
                   ->method('info')
                   ->with(LogConstants::CPS_USER_CREATED);

        $this->container->set('logger', $mockLogger);
        $this->userProvider = $this->container->get('ocsdc.cps.userprovider');

        $data = $this->getCPSUserData();
        $user = $this->userProvider->provideUser($data);
        $mappedValue = self::getRemoteDataMapper($user);
        foreach($data as $key => $value){
            $this->assertEquals($value, $mappedValue['HTTP_'.strtoupper(str_replace('-', '_', $key))]);
        }
    }

    private static function getRemoteDataMapper(CPSUser $user)
    {
        return [
            "HTTP_CODICEFISCALE" => $user->getCodiceFiscale(),
            "HTTP_CAPDOMICILIO" => $user->getCapDomicilio(),
            "HTTP_CAPRESIDENZA" => $user->getCapResidenza(),
            "HTTP_CELLULARE" => $user->getCellulare(),
            "HTTP_CITTADOMICILIO" => $user->getCittaDomicilio(),
            "HTTP_CITTARESIDENZA" => $user->getCittaResidenza(),
            "HTTP_COGNOME" => $user->getCognome(),
            "HTTP_DATANASCITA" => $user->getDataNascita() instanceof \DateTime ? $user->getDataNascita()->format('d/m/Y') : null,
            "HTTP_EMAILADDRESS" => $user->getEmail(),
            "HTTP_EMAILADDRESSPERSONALE" => $user->getEmailAlt(),
            "HTTP_INDIRIZZODOMICILIO" => $user->getIndirizzoDomicilio(),
            "HTTP_INDIRIZZORESIDENZA" => $user->getIndirizzoResidenza(),
            "HTTP_LUOGONASCITA" => $user->getLuogoNascita(),
            "HTTP_NOME" => $user->getNome(),
            "HTTP_PROVINCIADOMICILIO" => $user->getProvinciaDomicilio(),
            "HTTP_PROVINCIANASCITA" => $user->getProvinciaNascita(),
            "HTTP_PROVINCIARESIDENZA" => $user->getProvinciaResidenza(),
            "HTTP_SESSO" => $user->getSesso(),
            "HTTP_STATODOMICILIO" => $user->getStatoDomicilio(),
            "HTTP_STATONASCITA" => $user->getStatoNascita(),
            "HTTP_STATORESIDENZA" => $user->getStatoResidenza(),
            "HTTP_TELEFONO" => $user->getTelefono(),
            "HTTP_TITOLO" => $user->getTitolo(),
            "HTTP_X509CERTIFICATE_ISSUERDN" => $user->getX509certificateIssuerdn(),
            "HTTP_X509CERTIFICATE_SUBJECTDN" => $user->getX509certificateSubjectdn(),
            "HTTP_X509CERTIFICATE_BASE64" => $user->getX509certificateBase64()
        ];

    }

}
