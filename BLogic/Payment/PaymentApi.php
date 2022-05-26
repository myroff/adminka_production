<?php
namespace Payment;
use PDO as PDO;


class PaymentApi
{
	private $colums = ['payment_name', 'logo_file', 'is_active'];

	// data to build html selector
	private static $selectorData = [];

	public function getPaymentMethods()
	{
		$q = "SELECT * FROM payment_methods";
		$dbh = \MVC\DBFactory::getDBH();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute();
			$res = $sth->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $ex) {
			print $ex;
			return FALSE;
		}

		return $res;
	}

	public function handleApiRequest($action)
	{
		$out = [];

		switch($action)
		{
			case 'create':
				$out = $this->create();
				break;

			case 'update':
				$out = $this->update();
				break;

			case 'delete':
				$out = $this->delete();
				break;

			default:
				$out = ['status' => 'error', 'message' => 'API-Methode nicht erkannt.'];
				break;
		}

		header("Content-type: application/json");
		exit(json_encode($out));
	}

	public function create()
	{
		$data = [];

		if(!empty($_POST['new_payment_name']))
		{
			$data[':payment_name'] = $_POST['new_payment_name'];
		}
		if(!empty($_POST['new_payment_logo']))
		{
			$data[':logo_file'] = $_POST['new_payment_logo'];
		}
		if(!empty($_POST['new_payment_active']) || $_POST['new_payment_active'] === '0' )
		{
			$data[':is_active'] = $_POST['new_payment_active'];
		}

		if(count($data) < 3)
		{
			return array('status' => 'error', 'message' => 'Daten fehlen fürs Erstellen neuer Zahlungsmethode.');
		}

		$q = "INSERT INTO payment_methods (payment_name, logo_file, is_active) VALUES (:payment_name, :logo_file, :is_active)";

		$dbh = \MVC\DBFactory::getDBH();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($data);
		}
		catch (Exception $ex) {
			print $ex;
			return "Fehler beim Insert.";
		}

		return array('status' => 'ok', 'message' => 'insert is executed.');
	}

	public function update()
	{
		$missingItems	= array();
		$data			= array();

		if((int)$_POST['edit_item_record_id'] || $_POST['edit_item_record_id'] === '0')
		{
			$data[':payment_id'] = (int)$_POST['edit_item_record_id'];
		}
		else
		{
			$missingItems[] = "payment_id missing";
		}

		if($_POST['edit_item_value_name'] && in_array($_POST['edit_item_value_name'], $this->colums))
		{
			$valName = $_POST['edit_item_value_name'];
		}
		else
		{
			$missingItems[] = "value name missing";
		}

		if($_POST['edit_item_value_value'] || $_POST['edit_item_value_value'] === '0')
		{
			$data[':item_value'] = $_POST['edit_item_value_value'];
		}
		else
		{
			$missingItems[] = "value missing";
		}

		if($missingItems)
		{
			$msg = implode(" \n", $missingItems);
			$out = ['status' => 'error', 'message' => $msg];
			return $out;
		}

		$q = "UPDATE payment_methods SET `{$valName}`=:item_value WHERE payment_id=:payment_id";

		$dbh = \MVC\DBFactory::getDBH();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($data);
		}
		catch (Exception $ex) {
			print $ex;
			return "Fehler beim Insert.";
		}

		return array('status' => 'ok', 'message' => 'update is executed.');
	}

	public function delete()
	{
		$missingItems	= array();
		$data			= array();

		if((int)$_POST['item_record_id'] || $_POST['item_record_id'] === '0')
		{
			$data[':payment_id'] = (int)$_POST['item_record_id'];
		}
		else
		{
			$missingItems[] = "payment_id missing";
		}

		if($missingItems)
		{
			$msg = implode(" \n", $missingItems);
			$out = ['status' => 'error', 'message' => $msg];
			return $out;
		}

		$q = "DELETE FROM payment_methods WHERE payment_id=:payment_id";

		$dbh = \MVC\DBFactory::getDBH();

		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($data);
		}
		catch (Exception $ex) {
			print $ex;
			return "Fehler beim Insert.";
		}

		return array('status' => 'ok', 'message' => 'update is executed.');
	}

	/* get array to bild materialize css selector:
	 * array (<payment_id> => <payment_name>)
	 */
	public static function getSelectorData()
	{
		if(empty(self::$selectorData))
		{
			$q = "SELECT payment_id, payment_name FROM payment_methods";
			$dbh = \MVC\DBFactory::getDBH();
			try
			{
				$sth = $dbh->prepare($q);
				$sth->execute();
				$res = $sth->fetchAll(PDO::FETCH_ASSOC);
			} catch (Exception $ex) {
				print $ex;
				return FALSE;
			}
			foreach($res as $r)
			{
				self::$selectorData[$r['payment_id']] = $r['payment_name'];
			}
		}

		return self::$selectorData;
	}
}
