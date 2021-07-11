<?php

namespace Snowdog\Academy\Model;

use Snowdog\Academy\Core\Database;

class UserManager
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getByLogin(string $login): ?User
    {
        $query = $this->database->prepare('SELECT * FROM users WHERE login = :login');
        $query->setFetchMode(Database::FETCH_CLASS, User::class);
        $query->bindParam(':login', $login, Database::PARAM_STR);
        $query->execute();

        $result = $query->fetch(Database::FETCH_CLASS);
        return $result ?: null;
    }

    public function getById(int $id): User
    {
        $query = $this->database->prepare('SELECT * FROM users WHERE id = :id');
        $query->setFetchMode(Database::FETCH_CLASS, User::class);
        $query->bindParam(':id', $id, Database::PARAM_INT);
        $query->execute();

        return $query->fetch(Database::FETCH_CLASS);
    }

    public function create(string $login, string $password, float $funds): int
    {
        $hash = $this->hashPassword($password);
        $statement = $this->database->prepare('INSERT INTO users (login, password, funds) VALUES (:login, :password, :funds)');
        $statement->bindParam(':login', $login, Database::PARAM_STR);
        $statement->bindParam(':password', $hash, Database::PARAM_STR);
        $statement->bindParam(':funds', $funds, Database::PARAM_STR);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function verifyPassword(User $user, $password): bool
    {
        return $this->hashPassword($password) === $user->getPasswordHash();
    }

    private function hashPassword(string $password): string
    {
        return hash('sha512', $password);
    }
    public function AddFundsToUser(int $userId,float $Funds): void
    { 
    
        // TODO
        $query = $this->database->prepare('SELECT * FROM users WHERE id = :userId');
        $query->bindParam(':userId', $userId, Database::PARAM_INT);
        $query->execute();
        $res=$query->fetchAll();
 
        if (count($res)==1) {
            # code...
            $updatedFunds=$res[0]['funds'] + $Funds;
            
        
        }
        
        if($updatedFunds>0){
           $updatedFunds=strval($updatedFunds);
            $sql = $this->database->prepare(' UPDATE users SET funds=:funds WHERE id = :userId' );
            $sql->bindParam(':userId', $userId, Database::PARAM_INT);
            $sql->bindParam(':funds', $updatedFunds , Database::PARAM_STR);
            $sql->execute();
            
        }
        else{
            print_r("Invalid Funds");die();
        }
    
        
    }
            

}
