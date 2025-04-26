<?php

namespace App\Model;

use App\Class\Database;

class QuotesHandler
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }
    public function getQuotes()
    {
        $this->database->connect();

        $data = $this->database->select(
            "SELECT
                `quoteId`,
                `quoteText`,
                `authorName`,
                `quoteAntirating`
            FROM
                `quotes`
                INNER JOIN `authors` ON `authors`.`authorId` = `quotes`.`quoteAuthor`",
            [],
            []
        );

        return $data;
    }
    public function getQuote($id = 0)
    {
        $this->database->connect();

        $data = $this->database->select(
            "SELECT
                `quoteId`,
                `quoteText`,
                `authorName`,
                `quoteAntirating`
            FROM
                `quotes`
                INNER JOIN `authors` ON `authors`.`authorId` = `quotes`.`quoteAuthor`
            WHERE
                `quoteId` = :id",
            ':id',
            $id
        );

        return $data[0];
    }

    public function getAuthors()
    {
        $this->database->connect();

        $data = $this->database->select(
            "SELECT
                `authorId`,
                `authorName`,
                `authorAntirating`
            FROM
                `authors`
            ORDER BY
                `authorAntirating` DESC",
            [],
            []
        );

        return $data;
    }

    public function voteForQuote($id)
    {
        $this->database->connect();

        $success = $this->database->update(
            "UPDATE
                `quotes`
            SET
                `quoteAntirating` = `quoteAntirating` + 1
            WHERE
                `quoteId` = :id",
            ':id',
            $id
        );

        return $success;
    }
}
