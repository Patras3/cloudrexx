<?phprequire_once(ASCMS_MODULE_PATH.'/checkout/lib/Country.class.php');require_once(ASCMS_DOCUMENT_ROOT.'/testing/testCases/MySQLTestCase.php');class CountryTest extends MySQLTestCase {	public function testGetAll() {		$objCountry = new Country(self::$database);		$this->assertNotEmpty($objCountry->getAll());	}}