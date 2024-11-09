<?php

include_once 'Database.php';

class DataLoader {
    private $db;
    private $errorLog = 'error_log.csv';

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function loadCSV($filePath, $tableName): void {
        if (!file_exists(filename: $filePath) || !is_readable(filename: $filePath)) {
            echo "El archivo no se puede leer o no existe.\n";
            return;
        }

        $handle = fopen(filename: $filePath, mode: 'r');
        $header = fgetcsv(stream: $handle);  // Leer los encabezados

        if ($header === false) {
            echo "No se pudo leer el encabezado del CSV.\n";
            return;
        }

        while (($row = fgetcsv(stream: $handle)) !== false) {
            $data = array_combine(keys: $header, values: $row);  // Combinar encabezado con los valores
            $this->insertData($data, $tableName);
        }

        fclose($handle);
    }

    private function insertData($data, $tableName): void {
        try {
            $columns = implode(separator: ", ", array: array_keys($data));
            $placeholders = ":" . implode(separator: ", :", array: array_keys($data));
            $query = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";
            $stmt = $this->db->prepare(query: $query);

            foreach ($data as $key => $value) {
                $stmt->bindValue(param: ":$key", value: $value);
            }

            $stmt->execute();
        } catch (PDOException $e) {
            $this->logError($data, $e->getMessage());
        }
    }

    private function logError($data, $errorMessage): void {
        $errorData = array_merge($data, ['error' => $errorMessage]);

        $handle = fopen(filename: $this->errorLog, mode: 'a');
        fputcsv(stream: $handle, fields: $errorData);
        fclose(stream: $handle);
    }
}
