<?php

namespace WuJunze\IdGen\Tests;

use Wujunze\IdGen\Guid;
use Wujunze\IdGen\IdGen;
use Wujunze\IdGen\SnowFlake;
use function WuJunze\IdGen\get_age_by_birthday;
use function WuJunze\IdGen\get_format_time;

class IdGenTest extends TestCase
{
    public function testStaticGeneration()
    {
        $uuid = IdGen::generate(1);
        $this->assertInstanceOf('WuJunze\Idgen\IdGen', $uuid);

        $uuid = IdGen::generate(3, 'example.com', IdGen::NS_DNS);
        $this->assertInstanceOf('WuJunze\Idgen\IdGen', $uuid);

        $uuid = IdGen::generate(4);
        $this->assertInstanceOf('WuJunze\Idgen\IdGen', $uuid);

        $uuid = IdGen::generate(5, 'example.com', IdGen::NS_DNS);
        $this->assertInstanceOf('WuJunze\Idgen\IdGen', $uuid);
    }

    public function testImportAllZeroUuid()
    {
        $uuid = IdGen::import('00000000-0000-0000-0000-000000000000');
        $this->assertInstanceOf('WuJunze\Idgen\IdGen', $uuid);
        $this->assertEquals('00000000-0000-0000-0000-000000000000', (string)$uuid);
    }

    public function testGenerationOfValidUuidViaRegex()
    {
        $uuid = IdGen::generate(1);
        $this->assertRegExp('~'.IdGen::VALID_UUID_REGEX.'~', (string)$uuid);

        $uuid = IdGen::generate(3, 'example.com', IdGen::NS_DNS);
        $this->assertRegExp('~'.IdGen::VALID_UUID_REGEX.'~', (string)$uuid);

        $uuid = IdGen::generate(4);
        $this->assertRegExp('~'.IdGen::VALID_UUID_REGEX.'~', (string)$uuid);

        $uuid = IdGen::generate(5, 'example.com', IdGen::NS_DNS);
        $this->assertRegExp('~'.IdGen::VALID_UUID_REGEX.'~', (string)$uuid);
    }

    public function testGenerationOfValidUuidViaValidator()
    {
        $uuid = IdGen::generate(1);
        $this->assertTrue(IdGen::validate($uuid->string));

        $uuid = IdGen::generate(3, 'example.com', IdGen::NS_DNS);
        $this->assertTrue(IdGen::validate($uuid->string));

        $uuid = IdGen::generate(4);
        $this->assertTrue(IdGen::validate($uuid->string));

        $uuid = IdGen::generate(5, 'example.com', IdGen::NS_DNS);
        $this->assertTrue(IdGen::validate($uuid->string));

        $uuid = IdGen::generate(1);
        $this->assertTrue(IdGen::validate($uuid->bytes));

        $uuid = IdGen::generate(3, 'example.com', IdGen::NS_DNS);
        $this->assertTrue(IdGen::validate($uuid->bytes));

        $uuid = IdGen::generate(4);
        $this->assertTrue(IdGen::validate($uuid->bytes));

        $uuid = IdGen::generate(5, 'example.com', IdGen::NS_DNS);
        $this->assertTrue(IdGen::validate($uuid->bytes));

        $uuid = IdGen::generate(1);
        $this->assertTrue(IdGen::validate($uuid->urn));

        $uuid = IdGen::generate(3, 'example.com', IdGen::NS_DNS);
        $this->assertTrue(IdGen::validate($uuid->urn));

        $uuid = IdGen::generate(4);
        $this->assertTrue(IdGen::validate($uuid->urn));

        $uuid = IdGen::generate(5, 'example.com', IdGen::NS_DNS);
        $this->assertTrue(IdGen::validate($uuid->urn));

        $this->assertTrue(IdGen::validate(IdGen::generate(1)));

        $this->assertTrue(IdGen::validate(IdGen::generate(3, 'example.com', IdGen::NS_DNS)));

        $this->assertTrue(IdGen::validate(IdGen::generate(4)));

        $this->assertTrue(IdGen::validate(IdGen::generate(5, 'example.com', IdGen::NS_DNS)));
    }

    public function testCorrectVersionUuid()
    {
        $uuidOne = IdGen::generate(1);
        $this->assertEquals(1, $uuidOne->version);

        $uuidThree = IdGen::generate(3, 'example.com', IdGen::NS_DNS);
        $this->assertEquals(3, $uuidThree->version);

        $uuidFour = IdGen::generate(4);
        $this->assertEquals(4, $uuidFour->version);

        $uuidFive = IdGen::generate(5, 'example.com', IdGen::NS_DNS);
        $this->assertEquals(5, $uuidFive->version);
    }

    public function testCorrectVariantUuid()
    {
        $uuidOne = IdGen::generate(1);
        $this->assertEquals(1, $uuidOne->variant);

        $uuidThree = IdGen::generate(3, 'example.com', IdGen::NS_DNS);
        $this->assertEquals(1, $uuidThree->variant);

        $uuidFour = IdGen::generate(4);
        $this->assertEquals(1, $uuidFour->variant);

        $uuidFive = IdGen::generate(5, 'example.com', IdGen::NS_DNS);
        $this->assertEquals(1, $uuidFive->variant);
    }

    public function testCorrectVersionOfImportedUuid()
    {
        $uuidOne = IdGen::generate(1);
        $importedOne = IdGen::import((string)$uuidOne);
        $this->assertEquals($uuidOne->version, $importedOne->version);

        $uuidThree = IdGen::generate(3, 'example.com', IdGen::NS_DNS);
        $importedThree = IdGen::import((string)$uuidThree);
        $this->assertEquals($uuidThree->version, $importedThree->version);

        $uuidFour = IdGen::generate(4);
        $importedFour = IdGen::import((string)$uuidFour);
        $this->assertEquals($uuidFour->version, $importedFour->version);

        $uuidFive = IdGen::generate(5, 'example.com', IdGen::NS_DNS);
        $importedFive = IdGen::import((string)$uuidFive);
        $this->assertEquals($uuidFive->version, $importedFive->version);
    }

    public function testCorrectNodeOfGeneratedUuid()
    {
        $macAdress = \Faker\Provider\Internet::macAddress();
        $uuidThree = IdGen::generate(1, $macAdress);
        $this->assertEquals(strtolower(str_replace(':', '', $macAdress)), $uuidThree->node);

        $uuidThree = IdGen::generate(3, $macAdress, IdGen::NS_DNS);
        $this->assertNull($uuidThree->node);

        $uuidThree = IdGen::generate(4, $macAdress);
        $this->assertNull($uuidThree->node);

        $uuidThree = IdGen::generate(5, $macAdress, IdGen::NS_DNS);
        $this->assertNull($uuidThree->node);
    }

    public function testCorrectTimeOfImportedUuid()
    {
        $uuidOne = IdGen::generate(1);
        $importedOne = IdGen::import((string)$uuidOne);
        $this->assertEquals($uuidOne->time, $importedOne->time);

        $uuidThree = IdGen::generate(3, 'example.com', IdGen::NS_DNS);
        $importedThree = IdGen::import((string)$uuidThree);
        $this->assertEmpty($importedThree->time);

        $uuidFour = IdGen::generate(4);
        $importedFour = IdGen::import((string)$uuidFour);
        $this->assertEmpty($importedFour->time);

        $uuidFive = IdGen::generate(5, 'example.com', IdGen::NS_DNS);
        $importedFive = IdGen::import((string)$uuidFive);
        $this->assertEmpty($importedFive->time);
    }

    public function testUuidCompare()
    {
        $uuid1 = (string)IdGen::generate(1);
        $uuid2 = (string)IdGen::generate(1);

        $this->assertTrue(IdGen::compare($uuid1, $uuid1));
        $this->assertFalse(IdGen::compare($uuid1, $uuid2));
    }

    public function testSampleKey()
    {
        $id = IdGen::getSamplePk();

        $this->assertInternalType('int', $id);
        $this->assertGreaterThanOrEqual(13, strlen($id));
    }

    public function testGenIdByTypeShareKey()
    {
        $genId = IdGen::genIdByTypeShareKey(6, 89);

        $this->assertInternalType('int', $genId);
        $this->assertGreaterThanOrEqual(16, strlen($genId));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGenIdByTypeShareKeyException()
    {
        $genId = IdGen::genIdByTypeShareKey(21, 89);
        $this->assertInternalType('int', $genId);
        $this->assertGreaterThanOrEqual(16, strlen($genId));
    }

    public function testGenIdByType()
    {
        $typeId = IdGen::genIdByType(8);
        $this->assertInternalType('int', $typeId);
        $this->assertGreaterThanOrEqual(9, strlen($typeId));

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGenIdByTypeException()
    {
        $typeId = IdGen::genIdByType(18);
        $this->assertInternalType('int', $typeId);
        $this->assertGreaterThanOrEqual(9, strlen($typeId));

    }


    public function testGenCode()
    {
        $code = IdGen::genCode(9, 9, 888);

        $this->assertTrue(IdGen::IdValidate($code));
        $this->assertInternalType('int', $code);
        $this->assertGreaterThanOrEqual(17, strlen($code));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGenCodeTypeException()
    {
        $code = IdGen::genCode(16, 9, 888);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGenCodeResourceException()
    {
        $code = IdGen::genCode(10, 16, 888);
    }


    /**
     * @expectedException  \InvalidArgumentException
     */
    public function testGenCodeException()
    {
        $code = IdGen::genCode(9, 9, 88888);
        $this->assertInternalType('int', $code);
        $this->assertGreaterThanOrEqual(17, strlen($code));
    }

    public function testGetFormatTime()
    {
        $dateTime = get_format_time();
        $this->assertNotEmpty($dateTime);
        $this->assertInternalType('string', $dateTime);
    }

    public function testGetAgeByBirthday()
    {
        $age = get_age_by_birthday(19930806);
        $this->assertInternalType('numeric', $age);
        $this->assertEquals(date('Y') - 1993, $age);

        $ageEmpty = get_age_by_birthday(0);
        $this->assertEmpty($ageEmpty);
    }

    public function testSnowFlake()
    {
        $snowFlakeId = IdGen::snowFlakeId();
        $this->assertInternalType('numeric', $snowFlakeId);
    }

    public function testSnowFlakeIdRepeat()
    {
        $all = [];
        $exits = [];

        for ($i = 0; $i < 100000; $i++) {
            $id = IdGen::snowFlakeId(10);
            if (in_array($id, $all)) {
                $exits[] = $id;
            } else {
                $all[] = $id;
            }
        }

        $this->assertEquals(100000, count($all));
        $this->assertEmpty($exits);
    }


    public function testSimplePKRepeat()
    {
        $all = [];
        $exits = [];

        for ($i = 0; $i < 100000; $i++) {
            $id = IdGen::getSamplePk();
            if (in_array($id, $all)) {
                $exits[] = $id;
            } else {
                $all[] = $id;
            }
        }
        $this->assertNotEmpty($all);
        $this->assertNotEmpty($exits);
    }

    /**
     * @expectedException \LogicException
     */
    public function testSnowFlakeIdWorkIdException()
    {
        $all = [];
        $exits = [];

        for ($i = 0; $i < 100; $i++) {
            $id = IdGen::snowFlakeId(18);
            if (in_array($id, $all)) {
                $exits[] = $id;
            } else {
                $all[] = $id;
            }
        }

        $this->assertEquals(100000, count($all));
        $this->assertEmpty($exits);
    }

    public function testTilNextMillis()
    {
        $snowFlake = new SnowFlake();
        $timestamp = $snowFlake->tilNextMillis(((int)$snowFlake->timeGen()) + 3);

        $this->assertNotEmpty($timestamp);
        $this->assertInternalType('numeric', $timestamp);
    }

    public function testNextIdException()
    {

        $mockery = \Mockery::mock(SnowFlake::class);

        $mockery->shouldReceive('timeGen')
            ->andReturn(-2);

        $this->assertTrue(true);
    }

    public function testGuid()
    {
        $machineId = 23;
        $guid = Guid::getGenerator();
        $id1 = $guid->generate($machineId);
        $id2 = $guid->generate($machineId);

        $this->assertInternalType('numeric', $id1);
        $this->assertInternalType('numeric', $id2);
        $this->assertNotEquals($id1, $id2);

    }

    public function testGuidRepeat()
    {
        $all = [];
        $exits = [];

        for ($i = 0; $i < 100000; $i++) {
            $guid = Guid::getGenerator();
            $id = $guid->generate(12);
            if (in_array($id, $all)) {
                $exits[] = $id;
            } else {
                $all[] = $id;
            }
        }

        $this->assertEquals(100000, count($all));
        $this->assertEmpty($exits);
    }

    public function testGenGuid()
    {
        $all = [];
        $exits = [];

        for ($i = 0; $i < 100000; $i++) {
            $id = IdGen::genGuid(23);
            if (in_array($id, $all)) {
                $exits[] = $id;
            } else {
                $all[] = $id;
            }
        }

        $this->assertEquals(100000, count($all));
        $this->assertEmpty($exits);
    }
}
