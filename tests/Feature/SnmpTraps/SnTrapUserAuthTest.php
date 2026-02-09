<?php
namespace ObzoraNMS\Tests\Feature\SnmpTraps;

class SnTrapUserAuthTest extends SnmpTrapTestCase
{
    /**
     * Create snTrapUserLogin trap object
     * Test SnTrapUserLogin handler
     *
     * @return void
     */
    public function testSnTrapUserLogin(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
SNMPv2-MIB::snmpTrapOID.0 FOUNDRY-SN-TRAP-MIB::snTrapUserLogin
DISMAN-EVENT-MIB::sysUpTimeInstance 172:9:43:55.64
FOUNDRY-SN-AGENT-MIB::snAgGblTrapMessage.0 "Security: ssh login by rancid from src IP {{ ip }} to PRIVILEGED EXEC mode using RSA as Server Host Key. "
TRAP,
            'Security: ssh login by rancid from src IP {{ ip }} to PRIVILEGED EXEC mode using RSA as Server Host Key. ',
            'Could not handle snTrapUserLogin',
        );
    }

    /**
     * Create snTrapUserLogout trap object
     * Test SnTrapUserLogout handler
     *
     * @return void
     */
    public function testSnTrapUserLogout(): void
    {
        $this->assertTrapLogsMessage(<<<'TRAP'
{{ hostname }}
UDP: [{{ ip }}]:57602->[192.168.5.5]:162
SNMPv2-MIB::snmpTrapOID.0 FOUNDRY-SN-TRAP-MIB::snTrapUserLogin
DISMAN-EVENT-MIB::sysUpTimeInstance 172:9:43:55.64
FOUNDRY-SN-AGENT-MIB::snAgGblTrapMessage.0 "Security: ssh logout by rancid from src IP {{ ip }} from USER EXEC mode using RSA as Server Host Key. "
TRAP,
            'Security: ssh logout by rancid from src IP {{ ip }} from USER EXEC mode using RSA as Server Host Key. ',
            'Could not handle snTrapUserLogout',
        );
    }
}
