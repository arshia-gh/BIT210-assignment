<?php

	namespace database;

	use Exception;
	use PDO;

	class DatabaseHandler
	{
		private string $db_server_name = 'localhost';
		private string $db_username = 'root';
		private string $db_password = '';
		private string $db_name = 'pcvs_bit210';

		private array $connections = [];

		public function __construct()
		{
		}

		/**
		 * @throws \Exception
		 */
		public function connect() : PDO
		{
			try {
				$db = new PDO(
					"mysql:host=$this->db_server_name;dbname=$this->db_name",
					$this->db_username,
					$this->db_password,
					[
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					]
				);

				$this->connections[] = &$db;
				return $db;
			} catch (\PDOException $e) {
				throw new \PDOException($e->getMessage(), $e->getCode());
			}
		}

		/**
		 * @throws \Exception
		 */
		private function query($query, array $args) : bool|\PDOStatement
		{
			$db = $this->connect();
			$stmt = $db->prepare($query);

			if (!$stmt->execute($args)) {
				throw new Exception($stmt->errorInfo(), $stmt->errorCode());
			}

			$this->close_connection($db);
			$stmt->closeCursor();
			return $stmt;
		}

		public static function close_connection(&$connection)
		{
			$connection = NULL;
		}

		/**
		 * @throws \Exception
		 */
		public function cud_query(string $query, mixed ...$args) : int
		{
			return $this->query($query, $args)->rowCount();
		}

		/**
		 * @throws \Exception
		 */
		public function query_one(string $query, mixed ...$args) : bool|array|null
		{
			return $this->query($query, $args)->fetch();
		}

		/**
		 * @throws \Exception
		 */
		public function query_all(string $query, mixed ...$args) : bool|array
		{
			return $this->query($query, $args)->fetchAll();
		}

//		private static function fetch_result_and_close(bool|\PDOStatement $result, callable $callback) : bool|array|null
//		{
//			if (is_bool($result)) return $result;
//			$fetched_result = $callback();
//			$result->closeCursor();
//			return $fetched_result;
//		}

		public function __destruct()
		{
			foreach ($this->connections as &$db_connection) {
				self::close_connection($db_connection);
			}
		}
	}