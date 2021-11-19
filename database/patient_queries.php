<?php

	include_once 'classes/DatabaseHandler.php';

	final class PatientDatabaseHandler extends DatabaseHandler
	{

		/**
		 * Retrieves all vaccines with their availability status
		 */
		public function get_available_vaccines() : Exception|array
		{
			try {
				$sql = "SELECT vaccines.vaccineID, vaccineName, manufacturer, COUNT(batchNo) > 0 as available FROM vaccines
				LEFT JOIN (SELECT * FROM batches WHERE quantityAvailable > 0 AND expiryDate > current_date) as batches 
				    ON batches.vaccineID = vaccines.vaccineID
					GROUP BY vaccines.vaccineID
				";
				return $this->query_all($sql);
			} catch (Exception $e) {
				return $e;
			}
		}

		/**
		 * Retrieves a vaccine with the provided id with its availability status
		 */
		public function get_vaccine(string $vaccineID) : Exception|array|null
		{
			try {
				$sql = "SELECT vaccines.vaccineID, vaccineName, manufacturer, COUNT(batchNo) > 0 as available FROM vaccines 
    			LEFT JOIN (SELECT * FROM batches WHERE quantityAvailable > 0 AND expiryDate > current_date) as batches
					ON vaccines.vaccineID = batches.vaccineID
				WHERE vaccines.vaccineID =?
				GROUP BY vaccines.vaccineID";
				return $this->query_one($sql, $vaccineID);
			} catch (Exception $e) {
				return $e;
			}
		}

		/**
		 * Retrieves healthcare centres that offer the specified vaccine. <br>
		 * - Healthcare centre must have at least one unexpired batch and
		 * the batch should be available (quantityAvailable > 0)
		 *
		 * @param string $vaccine_id
		 *
		 * @return \Exception|array an associative array containing all healthcare centres that offer the specified
		 *                          vaccine
		 */
		public function get_available_healthcare_centres(string $vaccine_id) : Exception|array
		{
			try {
				$sql = "SELECT healthcarecentres.centreName, address,
       						SUM(quantityAvailable) AS quantityAvailable
						FROM healthcarecentres
							LEFT JOIN(SELECT quantityAvailable, centreName
    									FROM batches WHERE vaccineID =? AND expiryDate > CURRENT_DATE 
    									               AND quantityAvailable > 0) AS batches
							ON healthcarecentres.centreName = batches.centreName
						WHERE quantityAvailable > 0
						GROUP BY healthcarecentres.centreName
						ORDER BY quantityAvailable";
				return $this->query_all($sql, $vaccine_id);
			} catch (Exception $e) {
				return $e;
			}
		}

		public function get_healthcare_centre($vaccineID, $centreName) : Exception|array|null
		{
			try {
				$sql = "SELECT healthcarecentres.centreName, address,
       						SUM(quantityAvailable) AS quantityAvailable
						FROM healthcarecentres
							LEFT JOIN(SELECT quantityAvailable, centreName
    									FROM batches WHERE vaccineID =? AND expiryDate > CURRENT_DATE 
    									               AND quantityAvailable > 0) AS batches
							ON healthcarecentres.centreName = batches.centreName
						WHERE quantityAvailable > 0 AND healthcarecentres.centreName =?
						GROUP BY healthcarecentres.centreName
						ORDER BY quantityAvailable";
				return $this->query_one($sql, $vaccineID, $centreName);
			} catch (Exception $e) {
				return $e;
			}
		}

		/**
		 * Retrieves batches that are offered by the specified healthcare centre and<br>
		 * are of type of the specified vaccine.<br>
		 * - batch must not be expired and be available (quantityAvailable > 0)
		 *
		 * @param string $centre_name
		 * @param string $vaccine_id
		 *
		 * @return \Exception|array an associative array containing all batches offered by specified
		 * healthcare centre and are of type of specified vaccine
		 */
		public function get_available_batches(string $centre_name, string $vaccine_id) : Exception|array
		{
			try {
				$sql = "SELECT * FROM batches
							WHERE centreName =? 
			  						AND vaccineID =? 
			 						AND expiryDate > current_date 
			  						AND quantityAvailable > 0";
				return $this->query_all($sql, $centre_name, $vaccine_id);
			} catch (Exception $e) {
				return $e;
			}
		}

		/**
		 * @param string $centre_name
		 * @param string $vaccine_id
		 *
		 * @return \Exception|array|null
		 */
		public function get_batch(string $vaccine_id, string $centre_name, string $batchNo) : Exception|array|null
		{
			try {
				$sql = "SELECT * FROM batches
							WHERE vaccineID =?
							    AND centreName =? 
			  					AND batchNo =? 
			 					AND expiryDate > current_date 
			  					AND quantityAvailable > 0";
				return $this->query_one($sql, $vaccine_id, $centre_name, $batchNo);
			} catch (Exception $e) {
				return $e;
			}
		}

		/**
		 * @throws \Exception
		 */
		public function login(string $username, string $password) : null|array
		{
			$sql = "SELECT * FROM users WHERE username =? AND password =?";
			$result = $this->query_one($sql, $username, $password);
			return is_null($result) ? $result : array_filter($result, fn ($el) => !is_null($el));
		}

		/**
		 * @throws \Exception
		 */
		public function register(string $username, string $password, string $email, string $full_name,
		                         string $user_type, string $special_field, string $centre_name=null
		) : bool
		{
			$sql = $user_type === 'administrator' ?
				"INSERT INTO users(username, password, email, fullName, staffID, centreName, userType) 
						VALUES (?, ?, ?, ?, ?, ?, $user_type)" :
				//else will be patient
				"INSERT INTO users(username, password, email, fullName, ICPassport, centreName, userType) 
						VALUES (?, ?, ?, ?, ?, ?, 'patient')";
			return $this->cud_query($sql,
					$username,
					$password,
					$email,
					$full_name,
					$special_field,
					empty($centre_name) ? null : $centre_name
				) > 0;
		}

		/**
		 * @throws \Exception
		 */
		public function isUniqueField(string $field, string $value) : bool
		{
			$sql = "SELECT * FROM users WHERE $field = ?";
			return $this->cud_query($sql, $value) < 1;
		}

		/**
		 * @throws \Exception
		 */
		public function isUniqueEmail($value)
		{
			$this->isUniqueField('email', $value);
		}

		/**
		 * @throws \Exception
		 */
		public function isUniqueUsername($value)
		{
			$this->isUniqueField('username', $value);
		}

		/**
		 * @throws \Exception
		 */
		public function isUniqueStaffID($value)
		{
			$this->isUniqueField('staffID', $value);
		}

		public function get_user_vaccinations(string $username) : Exception|array
		{
			try {
				$sql = "SELECT vaccinationID, status, appointmentDate, vaccineName, remarks FROM vaccinations 
    					JOIN (SELECT vaccineName, batchNo 
    							FROM batches JOIN vaccines 
    							    ON batches.vaccineID = vaccines.vaccineID
    					    ) as batches
							ON batches.batchNo = vaccinations.batchNo
						WHERE username =? 
						ORDER BY status";
				return $this->query_all($sql, $username);
			} catch (Exception $e) {
				return $e;
			}
		}

		public function save_vaccination(string $appointmentDate, string $username, string $batchNo) : Exception|int
		{
			try {
				$sql = "INSERT INTO vaccinations(vaccinationID, appointmentDate, username, batchNo, remarks, status)
						VALUES (?, ?, ?, ?, null, 'pending')";
				return $this->cud_query($sql, $this->get_rand_id(8), $appointmentDate, $username, $batchNo);
			} catch (Exception $e) {
				return $e;
			}
		}

		private function get_rand_id(int $len) : string
		{
			$min_range = intval(1 . (str_repeat(0, $len - 1)));
			$max_range = intval(str_repeat(9, $len));
			try {
				return strval(random_int($min_range, $max_range));
			} catch (Exception $e) {
				return strval(mt_rand($min_range, $max_range));
			}
		}
	}

	$patient_queries = new PatientDatabaseHandler();