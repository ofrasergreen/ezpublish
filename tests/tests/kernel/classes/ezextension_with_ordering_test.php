<?php
/**
 * File containing the eZExtensionWithOrderingTest class
 *
 * @copyright Copyright (C) 1999-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package tests
 */

class eZExtensionWithOrderingTest extends ezpTestCase
{
    public function setUp()
    {
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionDirectory', 'tests/tests/kernel/classes/extensions/' );
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', 'ExtensionOrdering', 'enabled' );
        self::clearActiveExtensionsCache();
    }

    public function tearDown()
    {
        ezpINIHelper::restoreINISettings();
        self::clearActiveExtensionsCache();
    }

    public function testUnrelatedKeepOrder1()
    {
        self::setExtensions( array( 'ezmultiupload', 'ezfind' ) );
        $this->assertSame(
            array( 'ezmultiupload', 'ezfind' ),
            eZExtension::activeExtensions() );
    }

    public function testUnrelatedKeepOrder2()
    {
        self::setExtensions( array( 'ezfind', 'ezmultiupload' ) );
        $this->assertSame(
            array( 'ezfind', 'ezmultiupload' ),
            eZExtension::activeExtensions() );
    }

    public function testSimpleNoReordering()
    {
        self::setExtensions( array( 'ezjscore', 'ezfind' ) );
        $this->assertSame(
            array( 'ezjscore', 'ezfind' ),
            eZExtension::activeExtensions() );
    }

    public function testSimpleReordering()
    {
        self::setExtensions( array( 'ezfind', 'ezjscore' ) );
        $this->assertSame(
            array( 'ezjscore', 'ezfind' ),
            eZExtension::activeExtensions() );
    }

    public function testSimpleReorderingKeepInitialDummies()
    {
        self::setExtensions( array( 'dummy1', 'dummy2', 'dummy3', 'ezfind', 'ezjscore' ) );
        $this->assertSame(
            array( 'dummy1', 'dummy2', 'dummy3', 'ezjscore', 'ezfind' ),
            eZExtension::activeExtensions() );
    }

    public function testComplexReordering()
    {
        self::setExtensions( array( 'ezfind', 'ezflow', 'ezgmaplocation', 'ezjscore', 'ezmultiupload', 'ezoe', 'ezwebin', 'ezwt' ) );
        $activeExtensions = eZExtension::activeExtensions();
        foreach ( array( 'ezfind', 'ezflow', 'ezgmaplocation', 'ezjscore', 'ezmultiupload', 'ezoe', 'ezwebin', 'ezwt' ) as $extension )
            $$extension = array_search( $extension, $activeExtensions );

        $this->assertLessThan( $ezwt, $ezmultiupload );
        $this->assertLessThan( $ezflow, $ezfind );
        $this->assertLessThan( $ezwebin, $ezfind );
        $this->assertLessThan( $ezwebin, $ezflow );
        $this->assertLessThan( $ezwebin, $ezjscore );
        $this->assertLessThan( $ezgmaplocation, $ezjscore );
        $this->assertLessThan( $ezoe, $ezjscore );
        $this->assertLessThan( $ezfind, $ezjscore );
    }

    public function testCycleInvolvesNoReordering1()
    {
        self::setExtensions( array( 'cycle1', 'cycle2' ) );
        $this->assertSame(
            array( 'cycle1', 'cycle2' ),
            eZExtension::activeExtensions() );
    }

    public function testCycleInvolvesNoReordering2()
    {
        self::setExtensions( array( 'cycle2', 'cycle1' ) );
        $this->assertSame(
            array( 'cycle2', 'cycle1' ),
            eZExtension::activeExtensions() );
    }


    /**
     * Sets the active extensions
     *
     * @param string $type ActiveExtensions or ActiveAccessExtensions
     * @param array $extensions Extensions to set as active ones
     */
    private static function setExtensions( $extensions, $type = 'ActiveExtensions' )
    {
        ezpINIHelper::setINISetting( 'site.ini', 'ExtensionSettings', $type, $extensions );
        self::clearActiveExtensionsCache();
    }

    /**
     * @todo Move to a common extension testing class
     */
    private static function clearActiveExtensionsCache()
    {
        eZCache::clearByID( 'active_extensions' );

        // currently required so that cache will actually be considered expired
        // this is a design issue in eZExpiryHandler we need to address soon as it deeply impacts testing any feature
        // that relies on it, and also impacts runtime on high-trafic sites.
        sleep( 2 );
    }
}
?>
