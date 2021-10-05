<?php

namespace classes\Db;

use Base;
use Log;

/**
 * Seed manuscript_contents sqlite table reading from filesystems
 *
 * @author Silvano Aldà / SIB - 2021
 */
class CreateDb
{
    private $f3;
    private $logger;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->f3 = Base::instance();
        $this->logger = new Log(str_replace('\\', '-', __CLASS__) . '.log');
        $this->logger->write('CreateDb');
    }

    public function handle()
    {
        $db = $this->f3->get('DB');
        $db->exec('CREATE TABLE "manuscripts" (
                        "id"	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
                        "name"	TEXT NOT NULL UNIQUE,
                        "published"	INTEGER NOT NULL DEFAULT 0,
                        "url"	TEXT,
                        "content"	TEXT)');

        $db->exec('CREATE TABLE "manuscript_contents" (
                        "id"	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
                        "manuscript_id"	INTEGER NOT NULL,
                        "name"	TEXT NOT NULL,
                        "extension"	TEXT,
                        "url"	TEXT,
                        "content"	TEXT)');

        $this->logger->write($db->log());
        return [
            'success' => true,
            'data' => []
        ];
    }
}
