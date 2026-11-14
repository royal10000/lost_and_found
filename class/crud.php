<?php
require_once("../configs/Database.php");

class CRUD extends Database
{
    public function insert($table, $data)
    {
        $columns = implode(", ", array_Keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function query($sql, $params = [], $single = false)
    {
        $stmt = $this->conn->prepare($sql);

        $stmt->execute($params);

        return $single ? $stmt->fetch() : $stmt->fetchAll();
    }



    public function update($table, $id, $data)
    {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "$column=:$column";
        }
        $sql = "UPDATE $table SET " . implode(",", $set) . "WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }
    public function delete($table, $id)
    {
        $sql = "DELETE FROM $table WHERE ID =:id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
