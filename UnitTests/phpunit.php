<?php 
require_once 'PHPUnit2/Framework/TestSuite.php';
#FILESYSTEM TESTS---------------------------------------------------------------
require_once dirname(__FILE__) . "/Filesystem/DirectoryTest.php";
require_once dirname(__FILE__) . "/Filesystem/FileTest.php";
require_once dirname(__FILE__) . "/Filesystem/ImageFileTest.php";
require_once dirname(__FILE__) . "/Filesystem/IoTest.php";
#-------------------------------------------------------------------------------
#FILTER TESTS-------------------------------------------------------------------
require_once dirname(__FILE__) . "/Filter/InputfilterTest.php";
#-------------------------------------------------------------------------------
#VALIDATION TESTS---------------------------------------------------------------
require_once dirname(__FILE__) . "/Validation/ValidationTest.php";
#-------------------------------------------------------------------------------

$testClassNames = array(
    #FILESYSTEM-----------------------------------------------------------------
    "DirectoryTest",
    "FileTest",
    "ImageFileTest",
    "IoTest",
    #---------------------------------------------------------------------------
    #FILTER---------------------------------------------------------------------
    "InputfilterTest",
    #---------------------------------------------------------------------------
    #VALIDATION-----------------------------------------------------------------
    "ValidationTest"
    #---------------------------------------------------------------------------
    );


#EXECUTE TESTS------------------------------------------------------------------
foreach ($testClassNames as $test) {
    $phpunit = new PHPUnit2_Framework_TestSuite($test);
    $phpunit->run();
}
#-------------------------------------------------------------------------------