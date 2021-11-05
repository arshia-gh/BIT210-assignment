<?php

	require_once "classes/DatabaseHandler.php";

	final class PatientDatabaseHandler extends \database\DatabaseHandler
	{

		/**
		 * Retrieves vaccines that have at least one unexpired batch
		 *
		 * @return array an associative array containing all vaccines with their respective columns
		 * @throws \Exception if the database connection is not established
		 */
		public function get_available_vaccines() : array
		{
			$sql = "SELECT * FROM vaccines 
			WHERE (SELECT COUNT(*) FROM batches WHERE batches.vaccineID = vaccines.vaccineID AND expiryDate > current_date) > 0
			";
			return $this->query_all($sql);
		}

		/**
		 * Retrieves healthcare centres that offer the specified vaccine. <br>
		 * - Healthcare centre must have at least one unexpired batch and
		 * the batch should be available (quantityAvailable > 0)
		 *
		 * @param string $vaccine_id
		 *
		 * @return array an associative array containing all healthcare centres that offer the specified vaccine
		 * @throws \Exception if the database connection is not established or statement fails
		 */
		public function get_available_healthcare_centre(string $vaccine_id) : array
		{
			$sql = "SELECT * FROM healthcarecentres 
			WHERE 
			      (SELECT COUNT(*) FROM batches 
			      WHERE vaccineID =? AND expiryDate > current_date 
			        AND quantityAvailable > 0 
			        AND batches.centreName = healthcarecentres.centreName) > 0
			";
			return $this->query_all($sql, $vaccine_id);
		}

		/**
		 * Retrieves batches that are offered by the specified healthcare centre and<br>
		 * are of type of the specified vaccine.<br>
		 * - batch must not be expired and be available (quantityAvailable > 0)
		 *
		 * @param string $centre_name
		 * @param string $vaccine_id
		 *
		 * @return array an associative array containing all batches offered by specified
		 * healthcare centre and are of type of specified vaccine
		 * @throws \Exception if the database connection is not established or statement fails
		 */
		public function get_available_batches(string $centre_name, string $vaccine_id) : array
		{
			$sql = "SELECT * FROM batches
			WHERE centreName =? 
			  AND vaccineID =? 
			  AND expiryDate > current_date 
			  AND quantityAvailable > 0
			";
			return $this->query_all($sql, $centre_name, $vaccine_id);
		}

		/**
		 * @throws \Exception
		 */
		public function login(string $username, string $password) : array
		{
			$sql = "SELECT fullName, username, email, ICPassport, userType FROM users WHERE username =? AND password =?";
			return $this->query_one($sql, $username, $password);
		}

		/**
		 * @throws \Exception
		 */
		public function sing_up(string $username, string $password, string $email, string $full_name, string $IC_passport, bool $is_patient) : bool
		{
			$sql = "INSERT INTO users(username, password, email, fullName, ICPassport, userType) 
			VALUES (?, ?, ?, ?, ?, ?);
			";
			return $this->cud_query($sql,
				$username,
				$password,
				$email,
				$full_name,
				$IC_passport,
				$is_patient ? 'patient' : 'administrator'
			) > 0;
		}


	}