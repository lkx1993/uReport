<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Address extends ActiveRecord
{
	protected $tablename = 'peopleAddresses';
	protected $person;
	public static $LABELS = array('Home', 'Business', 'Rental');

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$result = $id;
			}
			else {
				$zend_db = Database::getConnection();
				$sql = 'select * from peopleAddresses where id=?';
				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('addresses/unknownAddress');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setLabel('Home');
		}
	}

	public function validate()
	{
		if (!$this->getAddress()) { throw new Exception('addresses/missingRequiredFields'); }
		if (!$this->getPerson_id()) { throw new Exception('addresses/missingPerson'); }
		if (!$this->getLabel()) { $this->setLabel('Home'); }
	}

	public function save()   { parent::save();   }
	public function delete() { parent::delete(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()      { return parent::get('id');      }
	public function getAddress() { return parent::get('address'); }
	public function getCity()    { return parent::get('city');    }
	public function getState()   { return parent::get('state');   }
	public function getZip()     { return parent::get('zip');     }
	public function getLabel()   { return parent::get('label');   }

	public function setAddress($s) { parent::set('address', $s); }
	public function setCity   ($s) { parent::set('city',    $s); }
	public function setState  ($s) { parent::set('state',   $s); }
	public function setZip    ($s) { parent::set('zip',     $s); }
	public function setLabel  ($s) { parent::set('label',   $s); }

	public function getPerson_id() { return parent::get('person_id'); }
	public function getPerson()    { return parent::getForeignKeyObject('Person', 'person_id');      }
	public function setPerson_id($id)     { parent::setForeignKeyField ('Person', 'person_id', $id); }
	public function setPerson(Person $p)  { parent::setForeignKeyObject('Person', 'person_id', $p);  }

	public function handleUpdate($post)
	{
		$fields = array('label', 'address', 'city', 'state', 'zip', 'person_id');
		foreach ($fields as $key) {
			if (isset($post[$key])) {
				$set = 'set'.ucfirst($key);
				$this->$set($post[$key]);
			}
		}
	}
}