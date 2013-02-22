<?php 
require_once 'PHPUnit2/Framework/TestSuite.php';
#FILESYSTEM TESTS---------------------------------------------------------------
require_once dirname(__FILE__) . "/Filesystem/DirectoryTest.php";
require_once dirname(__FILE__) . "/Filesystem/FileTest.php";
#-------------------------------------------------------------------------------

$testClassNames = array(
    #FILESYSTEM-----------------------------------------------------------------
    "DirectoryTest",
    "FileTest"
    #---------------------------------------------------------------------------
    );


#EXECUTE TESTS------------------------------------------------------------------
foreach ($testClassNames as $test) {
    $phpunit = new PHPUnit2_Framework_TestSuite($test);
    $phpunit->run();
}
#-------------------------------------------------------------------------------